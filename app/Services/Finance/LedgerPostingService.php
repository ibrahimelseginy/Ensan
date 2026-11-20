<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\AccountingPeriod;
use App\Models\Finance\FinancialAccount;
use App\Models\Finance\LedgerEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * The only writer for Finance V2 journal entries.  A posted entry is immutable:
 * correction happens through reverse(), never through update/delete.
 */
final class LedgerPostingService
{
    public function post(array $payload, int $actorId): LedgerEntry
    {
        return DB::transaction(function () use ($payload, $actorId): LedgerEntry {
            $lines = array_values($payload['lines'] ?? []);
            if (count($lines) < 2) {
                throw ValidationException::withMessages(['lines' => 'القيد يحتاج سطرين على الأقل.']);
            }

            $date = (string) ($payload['entry_date'] ?? now()->toDateString());
            $period = AccountingPeriod::query()
                ->whereDate('starts_on', '<=', $date)
                ->whereDate('ends_on', '>=', $date)
                ->lockForUpdate()
                ->first();
            if (!$period || $period->status !== 'open') {
                throw ValidationException::withMessages(['entry_date' => 'لا توجد فترة مالية مفتوحة لتاريخ القيد.']);
            }

            $idempotencyKey = $payload['idempotency_key'] ?? null;
            if ($idempotencyKey) {
                $existing = LedgerEntry::query()->where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
                if ($existing) {
                    return $existing;
                }
            }

            $debit = 0;
            $credit = 0;
            $accountIds = [];
            foreach ($lines as $index => $line) {
                $d = $this->units($line['debit'] ?? 0);
                $c = $this->units($line['credit'] ?? 0);
                if (($d > 0) === ($c > 0)) {
                    throw ValidationException::withMessages(["lines.$index" => 'كل سطر يجب أن يكون مديناً أو دائناً فقط وبقيمة موجبة.']);
                }
                $accountIds[] = (int) ($line['financial_account_id'] ?? 0);
                $debit += $d;
                $credit += $c;
            }
            if ($debit !== $credit) {
                throw ValidationException::withMessages(['lines' => 'القيد غير متزن.']);
            }

            $accounts = FinancialAccount::query()
                ->whereIn('id', array_unique($accountIds))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            foreach ($accountIds as $accountId) {
                $account = $accounts->get($accountId);
                if (!$account || !$account->is_active || !$account->is_posting) {
                    throw ValidationException::withMessages(['lines' => 'تم اختيار حساب غير صالح أو تجميعي أو موقوف.']);
                }
            }

            $entry = LedgerEntry::create([
                'entry_number' => $payload['entry_number'] ?? ('LE-' . now()->format('Ymd') . '-' . strtoupper(Str::random(12))),
                'accounting_period_id' => $period->id,
                'entry_date' => $date,
                'posted_at' => now(),
                'entry_type' => (string) ($payload['entry_type'] ?? 'manual'),
                'currency' => (string) ($payload['currency'] ?? 'EGP'),
                'status' => 'posted',
                'source_type' => $payload['source_type'] ?? null,
                'source_id' => $payload['source_id'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'reversal_of_id' => $payload['reversal_of_id'] ?? null,
                'document_id' => $payload['document_id'] ?? null,
                'posted_by' => $actorId,
                'narrative' => (string) ($payload['narrative'] ?? 'قيد مالي'),
                'reference' => $payload['reference'] ?? null,
                'metadata' => $payload['metadata'] ?? null,
            ]);

            foreach ($lines as $index => $line) {
                $entry->lines()->create([
                    'line_no' => $index + 1,
                    'financial_account_id' => $line['financial_account_id'],
                    'debit' => $this->decimal($line['debit'] ?? 0),
                    'credit' => $this->decimal($line['credit'] ?? 0),
                    'fund_id' => $line['fund_id'] ?? null,
                    'cash_account_id' => $line['cash_account_id'] ?? null,
                    'project_id' => $line['project_id'] ?? null,
                    'campaign_id' => $line['campaign_id'] ?? null,
                    'beneficiary_id' => $line['beneficiary_id'] ?? null,
                    'custody_id' => $line['custody_id'] ?? null,
                    'counterparty_type' => $line['counterparty_type'] ?? null,
                    'counterparty_id' => $line['counterparty_id'] ?? null,
                    'description' => $line['description'] ?? null,
                    'dimensions' => $line['dimensions'] ?? null,
                ]);
            }

            return $entry;
        }, 3);
    }

    public function reverse(LedgerEntry $entry, string $reason, int $actorId): LedgerEntry
    {
        return DB::transaction(function () use ($entry, $reason, $actorId): LedgerEntry {
            $original = LedgerEntry::query()->with('lines')->lockForUpdate()->findOrFail($entry->id);
            if ($original->status !== 'posted') {
                throw ValidationException::withMessages(['entry' => 'لا يمكن عكس قيد غير مرحل أو سبق عكسه.']);
            }
            if (LedgerEntry::query()->where('reversal_of_id', $original->id)->exists()) {
                throw ValidationException::withMessages(['entry' => 'القيد سبق عكسه.']);
            }
            $reversal = $this->post([
                'entry_type' => 'reversal',
                'entry_date' => now()->toDateString(),
                'currency' => $original->currency,
                'source_type' => 'ledger_reversal',
                'source_id' => $original->id,
                'reversal_of_id' => $original->id,
                'idempotency_key' => 'reverse:' . $original->id,
                'reference' => 'REV-' . $original->entry_number,
                'narrative' => 'عكس القيد ' . $original->entry_number . ': ' . $reason,
                'lines' => $original->lines->map(fn ($line) => [
                    'financial_account_id' => $line->financial_account_id,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'fund_id' => $line->fund_id,
                    'cash_account_id' => $line->cash_account_id,
                    'project_id' => $line->project_id,
                    'campaign_id' => $line->campaign_id,
                    'beneficiary_id' => $line->beneficiary_id,
                    'custody_id' => $line->custody_id,
                    'counterparty_type' => $line->counterparty_type,
                    'counterparty_id' => $line->counterparty_id,
                    'description' => $line->description,
                    'dimensions' => $line->dimensions,
                ])->all(),
            ], $actorId);
            return $reversal;
        }, 3);
    }

    private function units(mixed $value): int
    {
        return (int) round(((float) $value) * 10000);
    }

    private function decimal(mixed $value): string
    {
        return number_format((float) $value, 4, '.', '');
    }
}
