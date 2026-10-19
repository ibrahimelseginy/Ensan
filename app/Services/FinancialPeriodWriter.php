<?php

namespace App\Services;

use App\Models\FinancialClosure;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

final class FinancialPeriodWriter
{
    public function execute(string $action, array $data, ?int $id, ?int $userId): FinancialClosure|bool
    {
        return DB::transaction(function () use ($action, $data, $id, $userId) {
            DB::table('application_locks')->where('name', 'ledger')->lockForUpdate()->firstOrFail();
            $closure = $id ? FinancialClosure::query()->lockForUpdate()->findOrFail($id) : null;
            if ($action === 'delete') {
                $result = $closure->delete();
            } elseif ($action === 'toggle') {
                $closure->update(['approved' => !$closure->approved, 'approved_by' => $closure->approved ? null : $userId]);
                $result = $closure;
            } elseif ($closure) {
                $closure->update($data);
                $result = $closure;
            } else {
                $result = FinancialClosure::create($data + ['closed_by' => $userId]);
            }
            // Keep period locks separate from manual posting locks. Reopening one period
            // must not unlock entries covered by another approved closure.
            JournalEntry::query()->update(['period_locked' => DB::raw("EXISTS (
                SELECT 1 FROM financial_closures fc WHERE fc.approved = 1
                AND fc.date >= journal_entries.date
                AND (fc.branch IS NULL OR fc.branch = '' OR fc.branch = journal_entries.branch)
            )")]);
            return $result;
        }, 3);
    }
}
