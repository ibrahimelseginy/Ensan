<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\CashAccount;
use App\Models\Finance\CashTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** A transfer affects balances only when its receiver confirms actual receipt. */
final readonly class CashTransferService
{
    public function __construct(private LedgerPostingService $ledger) {}

    public function submit(array $data, int $actorId): CashTransfer
    {
        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0 || empty($data['from_cash_account_id']) || empty($data['to_cash_account_id']) || (int) $data['from_cash_account_id'] === (int) $data['to_cash_account_id']) {
            throw ValidationException::withMessages(['transfer' => 'بيانات التحويل غير صالحة.']);
        }
        return CashTransfer::create([
            'transfer_number' => 'TRF-' . now()->format('Ymd') . '-' . strtoupper(Str::random(10)),
            'from_cash_account_id' => $data['from_cash_account_id'],
            'to_cash_account_id' => $data['to_cash_account_id'],
            'amount' => $amount,
            'currency' => $data['currency'] ?? 'EGP',
            'requested_on' => $data['requested_on'] ?? now()->toDateString(),
            'status' => 'submitted',
            'requested_by' => $actorId,
            'reference' => $data['reference'] ?? null,
            'note' => $data['note'] ?? null,
        ]);
    }

    public function receive(CashTransfer $transfer, int $receiverId): CashTransfer
    {
        return DB::transaction(function () use ($transfer, $receiverId): CashTransfer {
            $transfer = CashTransfer::query()->lockForUpdate()->findOrFail($transfer->id);
            if ($transfer->status !== 'submitted' || $transfer->ledger_entry_id) {
                throw ValidationException::withMessages(['transfer' => 'التحويل ليس في حالة انتظار الاستلام.']);
            }
            $accounts = CashAccount::query()->whereIn('id', [$transfer->from_cash_account_id, $transfer->to_cash_account_id])->lockForUpdate()->get()->keyBy('id');
            $from = $accounts->get($transfer->from_cash_account_id);
            $to = $accounts->get($transfer->to_cash_account_id);
            if (!$from || !$to || !$from->is_active || !$to->is_active || $from->currency !== $transfer->currency || $to->currency !== $transfer->currency) {
                throw ValidationException::withMessages(['transfer' => 'حسابا التحويل غير نشطين أو العملة غير متطابقة.']);
            }
            $entry = $this->ledger->post([
                'entry_type' => 'cash_transfer', 'entry_date' => now()->toDateString(), 'currency' => $transfer->currency,
                'source_type' => CashTransfer::class, 'source_id' => $transfer->id,
                'idempotency_key' => 'cash-transfer:' . $transfer->id,
                'reference' => $transfer->transfer_number,
                'narrative' => 'استلام تحويل ' . $transfer->transfer_number,
                'lines' => [
                    ['financial_account_id' => $to->ledger_account_id, 'debit' => $transfer->amount, 'credit' => 0, 'cash_account_id' => $to->id],
                    ['financial_account_id' => $from->ledger_account_id, 'debit' => 0, 'credit' => $transfer->amount, 'cash_account_id' => $from->id],
                ],
            ], $receiverId);
            $transfer->update(['status' => 'received', 'received_at' => now(), 'received_by' => $receiverId, 'ledger_entry_id' => $entry->id]);
            return $transfer->refresh();
        }, 3);
    }
}
