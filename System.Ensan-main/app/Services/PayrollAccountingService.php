<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;

class PayrollAccountingService
{
    /**
     * Create journal entry for payroll
     * 
     * @param Payroll $payroll
     * @return JournalEntry|null
     */
    public function createJournalEntry(Payroll $payroll)
    {
        // Find required accounts
        $salariesExpenseAccount = Account::where('code', 'LIKE', '%5101%')
            ->orWhere('name', 'LIKE', '%رواتب%')
            ->orWhere('name', 'LIKE', '%أجور%')
            ->where('type', 'expense')
            ->first();
            
        $cashAccount = Account::where('code', 'LIKE', '%1101%')
            ->orWhere('name', 'LIKE', '%نقدية%')
            ->orWhere('name', 'LIKE', '%صندوق%')
            ->where('type', 'asset')
            ->first();
            
        $salariesPayableAccount = Account::where('code', 'LIKE', '%2101%')
            ->orWhere('name', 'LIKE', '%رواتب مستحقة%')
            ->orWhere('name', 'LIKE', '%مرتبات دائنة%')
            ->where('type', 'liability')
            ->first();

        // If accounts don't exist, create them
        if (!$salariesExpenseAccount) {
            $salariesExpenseAccount = Account::create([
                'code' => '5101',
                'name' => 'مصروف الرواتب والأجور',
                'type' => 'expense',
                'description' => 'حساب مصروفات الرواتب والأجور للموظفين'
            ]);
        }

        if (!$cashAccount) {
            $cashAccount = Account::create([
                'code' => '1101',
                'name' => 'النقدية بالصندوق',
                'type' => 'asset',
                'description' => 'حساب النقدية المتاحة بالصندوق'
            ]);
        }

        if (!$salariesPayableAccount) {
            $salariesPayableAccount = Account::create([
                'code' => '2101',
                'name' => 'رواتب مستحقة الدفع',
                'type' => 'liability',
                'description' => 'حساب الرواتب المستحقة للموظفين'
            ]);
        }

        // Calculate net amount
        $netAmount = $payroll->calculateNetAmount();
        
        // Create journal entry
        $journalEntry = JournalEntry::create([
            'date' => $payroll->paid_at ?? Carbon::now(),
            'description' => "راتب {$payroll->user->name} - {$payroll->month}",
            'entry_type' => 'payroll',
            'locked' => false
        ]);

        // Debit: Salaries Expense (مصروف الرواتب)
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $salariesExpenseAccount->id,
            'debit' => $payroll->amount,
            'credit' => 0,
            'description' => "راتب أساسي - {$payroll->user->name}"
        ]);

        // If there are bonuses
        if ($payroll->bonuses > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salariesExpenseAccount->id,
                'debit' => $payroll->bonuses,
                'credit' => 0,
                'description' => "مكافآت - {$payroll->user->name}"
            ]);
        }

        // If there are deductions
        if ($payroll->deductions > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salariesExpenseAccount->id,
                'debit' => 0,
                'credit' => $payroll->deductions,
                'description' => "خصومات - {$payroll->user->name}"
            ]);
        }

        // Credit: Cash or Salaries Payable
        if ($payroll->paid_at) {
            // If paid, credit cash account
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $netAmount,
                'description' => "دفع راتب - {$payroll->user->name}"
            ]);
        } else {
            // If not paid yet, credit salaries payable
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salariesPayableAccount->id,
                'debit' => 0,
                'credit' => $netAmount,
                'description' => "راتب مستحق - {$payroll->user->name}"
            ]);
        }

        // Update payroll with journal entry
        $payroll->update([
            'journal_entry_id' => $journalEntry->id,
            'net_amount' => $netAmount
        ]);

        return $journalEntry;
    }

    /**
     * Update journal entry when payroll is paid
     * 
     * @param Payroll $payroll
     * @return void
     */
    public function markAsPaid(Payroll $payroll)
    {
        if (!$payroll->journalEntry) {
            return;
        }

        // Find accounts
        $cashAccount = Account::where('type', 'asset')
            ->where(function($q) {
                $q->where('code', 'LIKE', '%1101%')
                  ->orWhere('name', 'LIKE', '%نقدية%');
            })->first();
            
        $salariesPayableAccount = Account::where('type', 'liability')
            ->where(function($q) {
                $q->where('code', 'LIKE', '%2101%')
                  ->orWhere('name', 'LIKE', '%رواتب مستحقة%');
            })->first();

        if (!$cashAccount || !$salariesPayableAccount) {
            return;
        }

        // Create payment journal entry
        $paymentEntry = JournalEntry::create([
            'date' => $payroll->paid_at ?? Carbon::now(),
            'description' => "دفع راتب {$payroll->user->name} - {$payroll->month}",
            'entry_type' => 'payment',
            'locked' => false
        ]);

        // Debit: Salaries Payable
        JournalEntryLine::create([
            'journal_entry_id' => $paymentEntry->id,
            'account_id' => $salariesPayableAccount->id,
            'debit' => $payroll->net_amount,
            'credit' => 0,
            'description' => "سداد راتب مستحق"
        ]);

        // Credit: Cash
        JournalEntryLine::create([
            'journal_entry_id' => $paymentEntry->id,
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $payroll->net_amount,
            'description' => "دفع نقدي"
        ]);
    }

    /**
     * Reverse journal entry (for payroll deletion/cancellation)
     * 
     * @param Payroll $payroll
     * @return JournalEntry|null
     */
    public function reverseJournalEntry(Payroll $payroll)
    {
        if (!$payroll->journalEntry) {
            return null;
        }

        $originalEntry = $payroll->journalEntry;
        
        // Create reversal entry
        $reversalEntry = JournalEntry::create([
            'date' => Carbon::now(),
            'description' => "عكس قيد: {$originalEntry->description}",
            'entry_type' => 'reversal',
            'locked' => false
        ]);

        // Reverse all lines
        foreach ($originalEntry->lines as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $reversalEntry->id,
                'account_id' => $line->account_id,
                'debit' => $line->credit, // Swap debit and credit
                'credit' => $line->debit,
                'description' => "عكس: {$line->description}"
            ]);
        }

        return $reversalEntry;
    }
}
