<?php
namespace App\Services;

use App\Models\Expense;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;

class ExpenseService
{
    protected static function getOrCreateAccount(string $code, string $name, string $type): Account
    {
        $acc = Account::where('code', $code)->first();
        if (!$acc) { $acc = Account::create(['code' => $code, 'name' => $name, 'type' => $type]); }
        return $acc;
    }

    public static function postCreate(Expense $expense): void
    {
        $cash = self::getOrCreateAccount('102', 'donation_cash', 'asset');
        $map = [
            'operational' => ['code' => '501', 'name' => 'Operational Expense'],
            'aid' => ['code' => '502', 'name' => 'Aid Expense'],
            'logistics' => ['code' => '503', 'name' => 'Logistics Expense'],
        ];
        $expAcc = self::getOrCreateAccount($map[$expense->type]['code'], $map[$expense->type]['name'], 'expense');
        $entry = JournalEntry::create([
            'date' => $expense->paid_at ? $expense->paid_at->toDateString() : now()->toDateString(),
            'entry_type' => 'expense',
            'locked' => false,
            'gate' => 'expense',
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expAcc->id,
            'debit' => $expense->amount,
            'credit' => 0,
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $cash->id,
            'debit' => 0,
            'credit' => $expense->amount,
        ]);
    }
}
