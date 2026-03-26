<?php

namespace App\Services;

use App\Models\DelegateTrip;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;

class LogisticsAccountingService
{
    /**
     * Create journal entry for delegate trip
     * 
     * @param DelegateTrip $trip
     * @return JournalEntry|null
     */
    public function createJournalEntry(DelegateTrip $trip)
    {
        // Find or create required accounts
        $transportExpenseAccount = $this->getOrCreateAccount(
            '5201',
            'مصروفات النقل والمواصلات',
            'expense',
            'حساب مصروفات النقل والمواصلات للمندوبين'
        );

        $fuelExpenseAccount = $this->getOrCreateAccount(
            '5202',
            'مصروفات الوقود',
            'expense',
            'حساب مصروفات الوقود للمركبات'
        );

        $cashAccount = $this->getOrCreateAccount(
            '1101',
            'النقدية بالصندوق',
            'asset',
            'حساب النقدية المتاحة بالصندوق'
        );

        $delegatesPayableAccount = $this->getOrCreateAccount(
            '2102',
            'مصروفات مستحقة للمندوبين',
            'liability',
            'حساب المصروفات المستحقة للمندوبين'
        );

        // Calculate total cost
        $totalCost = $trip->calculateTotalCost();
        
        // Create journal entry
        $journalEntry = JournalEntry::create([
            'date' => $trip->date ?? Carbon::now(),
            'description' => "مصروفات رحلة - {$trip->delegate->name} - {$trip->description}",
            'entry_type' => 'logistics',
            'locked' => false
        ]);

        // Debit: Transport Expense (basic cost)
        if ($trip->cost > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $transportExpenseAccount->id,
                'debit' => $trip->cost,
                'credit' => 0,
                'description' => "تكلفة رحلة - {$trip->delegate->name}"
            ]);
        }

        // Debit: Fuel Expense
        if (($trip->fuel_cost ?? 0) > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fuelExpenseAccount->id,
                'debit' => $trip->fuel_cost,
                'credit' => 0,
                'description' => "وقود - {$trip->delegate->name}"
            ]);
        }

        // Debit: Other Expenses
        if (($trip->other_expenses ?? 0) > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $transportExpenseAccount->id,
                'debit' => $trip->other_expenses,
                'credit' => 0,
                'description' => "مصروفات أخرى - {$trip->delegate->name}"
            ]);
        }

        // Credit: Cash or Payable
        if ($trip->status === 'paid') {
            // If paid, credit cash account
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $totalCost,
                'description' => "دفع مصروفات رحلة - {$trip->delegate->name}"
            ]);
        } else {
            // If pending, credit payable account
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $delegatesPayableAccount->id,
                'debit' => 0,
                'credit' => $totalCost,
                'description' => "مصروفات مستحقة - {$trip->delegate->name}"
            ]);
        }

        // Update trip with journal entry
        $trip->update(['journal_entry_id' => $journalEntry->id]);

        return $journalEntry;
    }

    /**
     * Mark trip as paid and create payment entry
     * 
     * @param DelegateTrip $trip
     * @return JournalEntry|null
     */
    public function markAsPaid(DelegateTrip $trip)
    {
        if (!$trip->journalEntry || $trip->status === 'paid') {
            return null;
        }

        $cashAccount = $this->getOrCreateAccount('1101', 'النقدية بالصندوق', 'asset');
        $delegatesPayableAccount = $this->getOrCreateAccount('2102', 'مصروفات مستحقة للمندوبين', 'liability');

        $totalCost = $trip->calculateTotalCost();

        // Create payment journal entry
        $paymentEntry = JournalEntry::create([
            'date' => Carbon::now(),
            'description' => "دفع مصروفات رحلة - {$trip->delegate->name} - {$trip->description}",
            'entry_type' => 'payment',
            'locked' => false
        ]);

        // Debit: Payable
        JournalEntryLine::create([
            'journal_entry_id' => $paymentEntry->id,
            'account_id' => $delegatesPayableAccount->id,
            'debit' => $totalCost,
            'credit' => 0,
            'description' => "سداد مصروفات مستحقة"
        ]);

        // Credit: Cash
        JournalEntryLine::create([
            'journal_entry_id' => $paymentEntry->id,
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $totalCost,
            'description' => "دفع نقدي"
        ]);

        // Update trip status
        $trip->update(['status' => 'paid']);

        return $paymentEntry;
    }

    /**
     * Reverse journal entry (for trip deletion/cancellation)
     * 
     * @param DelegateTrip $trip
     * @return JournalEntry|null
     */
    public function reverseJournalEntry(DelegateTrip $trip)
    {
        if (!$trip->journalEntry) {
            return null;
        }

        $originalEntry = $trip->journalEntry;
        
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

    /**
     * Get or create account
     * 
     * @param string $code
     * @param string $name
     * @param string $type
     * @param string|null $description
     * @return Account
     */
    private function getOrCreateAccount($code, $name, $type, $description = null)
    {
        $account = Account::where('code', $code)->first();
        
        if (!$account) {
            $account = Account::where('name', 'LIKE', "%{$name}%")
                ->where('type', $type)
                ->first();
        }

        if (!$account) {
            $account = Account::create([
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'description' => $description ?? $name
            ]);
        }

        return $account;
    }

    /**
     * Get logistics statistics for accounting
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getLogisticsAccountingStats($startDate = null, $endDate = null)
    {
        $query = DelegateTrip::query();

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $totalTrips = $query->count();
        $paidTrips = (clone $query)->where('status', 'paid')->count();
        $pendingTrips = (clone $query)->where('status', 'pending')->count();

        $totalCost = 0;
        $paidCost = 0;
        $pendingCost = 0;

        foreach ($query->get() as $trip) {
            $cost = $trip->calculateTotalCost();
            $totalCost += $cost;
            
            if ($trip->status === 'paid') {
                $paidCost += $cost;
            } else {
                $pendingCost += $cost;
            }
        }

        return [
            'total_trips' => $totalTrips,
            'paid_trips' => $paidTrips,
            'pending_trips' => $pendingTrips,
            'total_cost' => $totalCost,
            'paid_cost' => $paidCost,
            'pending_cost' => $pendingCost,
            'with_journal_entries' => DelegateTrip::whereNotNull('journal_entry_id')->count()
        ];
    }
}
