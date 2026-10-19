<?php

namespace App\Services;

use App\Models\FinancialClosure;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/** The same execution rules apply to direct changes and approved review requests. */
final class JournalEntryWriter
{
    public function execute(string $action, array $data, ?int $id = null): JournalEntry|bool
    {
        return DB::transaction(function () use ($action, $data, $id) {
            DB::table('application_locks')->where('name', 'ledger')->lockForUpdate()->firstOrFail();
            $entry = $id ? JournalEntry::query()->lockForUpdate()->findOrFail($id) : null;
            if ($entry) {
                if ($entry->locked) throw ValidationException::withMessages(['entry' => 'هذا القيد مقفل ولا يمكن تعديله أو حذفه.']);
                $this->assertOpen($entry->date->toDateString(), $entry->branch);
            }
            if ($action === 'delete') return $entry->delete();
            $data = Validator::make($data, (new \App\Http\Requests\StoreJournalEntryRequest)->rules())->validate();
            $this->assertOpen($data['date'], $data['branch'] ?? null);
            $debit = $credit = 0;
            foreach ($data['lines'] as $line) {
                $d = (int) round(((float) ($line['debit'] ?? 0)) * 100);
                $c = (int) round(((float) ($line['credit'] ?? 0)) * 100);
                if (($d > 0) === ($c > 0)) {
                    throw ValidationException::withMessages(['lines' => 'كل سطر يجب أن يحتوي مبلغاً موجباً في المدين أو الدائن فقط.']);
                }
                $debit += $d;
                $credit += $c;
            }
            if ($debit !== $credit) throw ValidationException::withMessages(['lines' => 'القيد غير متزن.']);
            $lines = $data['lines'];
            unset($data['lines']);
            if ($entry) {
                $entry->update($data);
                $entry->lines()->delete();
            } else {
                $entry = JournalEntry::create($data);
            }
            $entry->lines()->createMany(array_map(fn ($line) => [
                'account_id' => $line['account_id'], 'debit' => $line['debit'] ?? 0, 'credit' => $line['credit'] ?? 0,
            ], $lines));
            return $entry;
        }, 3);
    }

    private function assertOpen(string $date, ?string $branch): void
    {
        $closed = FinancialClosure::where('approved', true)->whereDate('date', '>=', $date)
            ->where(fn ($q) => $q->whereNull('branch')->orWhere('branch', '')->orWhere('branch', $branch))->exists();
        if ($closed) throw ValidationException::withMessages(['date' => 'التاريخ يقع داخل فترة مالية مغلقة.']);
    }
}
