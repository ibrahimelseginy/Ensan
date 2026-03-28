<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final readonly class PayrollAccountingService
{
    public function createJournalEntry(Payroll $payroll): ?JournalEntry
    {
        return DB::transaction(function () use ($payroll) {
            $salariesExpenseAccount = $this->getAccountByCodeOrName('5101', 'رواتب', 'expense');
            $cashAccount            = $this->getAccountByCodeOrName('1101', 'نقدية', 'asset');
            $salariesPayableAccount = $this->getAccountByCodeOrName('2101', 'رواتب مستحقة', 'liability');

            $netAmount = (float)$payroll->calculateNetAmount();
            
            $journalEntry = JournalEntry::create([
                'date'        => $payroll->paid_at ?? Carbon::now(),
                'description' => "راتب {$payroll->user->name} - {$payroll->month}",
                'entry_type'  => 'payroll',
                'locked'      => false
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id'       => $salariesExpenseAccount->id,
                'debit'            => $payroll->amount,
                'credit'           => 0,
                'description'      => "راتب أساسي - {$payroll->user->name}"
            ]);

            if ($payroll->bonuses > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id'       => $salariesExpenseAccount->id,
                    'debit'            => $payroll->bonuses,
                    'credit'           => 0,
                    'description'      => "مكافآت - {$payroll->user->name}"
                ]);
            }

            if ($payroll->deductions > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id'       => $salariesExpenseAccount->id,
                    'debit'            => 0,
                    'credit'           => $payroll->deductions,
                    'description'      => "خصومات - {$payroll->user->name}"
                ]);
            }

            if ($payroll->paid_at) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id'       => $cashAccount->id,
                    'debit'            => 0,
                    'credit'           => $netAmount,
                    'description'      => "دفع راتب - {$payroll->user->name}"
                ]);
            } else {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id'       => $salariesPayableAccount->id,
                    'debit'            => 0,
                    'credit'           => $netAmount,
                    'description'      => "راتب مستحق - {$payroll->user->name}"
                ]);
            }

            $payroll->update([
                'journal_entry_id' => $journalEntry->id,
                'net_amount'       => $netAmount
            ]);

            return $journalEntry;
        });
    }

    public function markAsPaid(Payroll $payroll): void
    {
        if (!$payroll->journalEntry) {
            return;
        }

        $cashAccount            = $this->getAccountByCodeOrName('1101', 'نقدية', 'asset');
        $salariesPayableAccount = $this->getAccountByCodeOrName('2101', 'رواتب مستحقة', 'liability');

        DB::transaction(function () use ($payroll, $cashAccount, $salariesPayableAccount) {
            $paymentEntry = JournalEntry::create([
                'date'        => $payroll->paid_at ?? Carbon::now(),
                'description' => "دفع راتب {$payroll->user->name} - {$payroll->month}",
                'entry_type'  => 'payment',
                'locked'      => false
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $paymentEntry->id,
                'account_id'       => $salariesPayableAccount->id,
                'debit'            => $payroll->net_amount,
                'credit'           => 0,
                'description'      => "سداد راتب مستحق"
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $paymentEntry->id,
                'account_id'       => $cashAccount->id,
                'debit'            => 0,
                'credit'           => $payroll->net_amount,
                'description'      => "دفع نقدي"
            ]);
        });
    }

    public function reverseJournalEntry(Payroll $payroll): ?JournalEntry
    {
        if (!$payroll->journalEntry) {
            return null;
        }

        return DB::transaction(function () use ($payroll) {
            $originalEntry = $payroll->journalEntry;
            
            $reversalEntry = JournalEntry::create([
                'date'        => Carbon::now(),
                'description' => "عكس قيد: {$originalEntry->description}",
                'entry_type'  => 'reversal',
                'locked'      => false
            ]);

            foreach ($originalEntry->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $reversalEntry->id,
                    'account_id'       => $line->account_id,
                    'debit'            => $line->credit,
                    'credit'           => $line->debit,
                    'description'      => "عكس: {$line->description}"
                ]);
            }

            return $reversalEntry;
        });
    }

    private function getAccountByCodeOrName(string $code, string $name, string $type): Account
    {
        $account = Account::where('code', 'LIKE', '%' . $code . '%')
            ->orWhere('name', 'LIKE', '%' . $name . '%')
            ->where('type', $type)
            ->first();

        if (!$account) {
            $account = Account::create([
                'code'        => $code,
                'name'        => ($type === 'expense' ? 'مصروف الرواتب' : ($type === 'asset' ? 'النقدية' : 'رواتب مستحقة')),
                'type'        => $type,
                'description' => 'حساب تلقائي للرواتب'
            ]);
        }

        return $account;
    }
}
