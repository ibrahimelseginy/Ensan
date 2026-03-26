<?php

namespace App\Services;

use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TreasuryIntegrationService
{
    /**
     * Treasury Types Configuration
     */
    const TREASURY_TYPES = [
        'main' => [
            'name' => 'الخزينة الرئيسية',
            'code_prefix' => 'MAIN',
            'departments' => ['donations', 'expenses', 'payroll', 'projects', 'campaigns'],
            'account_code' => '1101'
        ],
        'branch' => [
            'name' => 'خزينة فرع',
            'code_prefix' => 'BRANCH',
            'departments' => ['donations', 'expenses'],
            'account_code' => '1102'
        ],
        'delegate' => [
            'name' => 'عهدة مندوب',
            'code_prefix' => 'DEL',
            'departments' => ['donations', 'logistics'],
            'account_code' => '1103'
        ],
        'project' => [
            'name' => 'خزينة مشروع',
            'code_prefix' => 'PROJ',
            'departments' => ['projects', 'expenses'],
            'account_code' => '1104'
        ],
        'campaign' => [
            'name' => 'خزينة حملة',
            'code_prefix' => 'CAMP',
            'departments' => ['campaigns', 'donations'],
            'account_code' => '1105'
        ],
        'petty_cash' => [
            'name' => 'صندوق مصروفات نثرية',
            'code_prefix' => 'PETTY',
            'departments' => ['expenses'],
            'account_code' => '1106'
        ]
    ];

    /**
     * ==========================================
     * DONATION INTEGRATION
     * ==========================================
     */

    /**
     * Process cash donation to treasury
     */
    public function processDonationToTreasury(Donation $donation, $treasuryId = null)
    {
        if ($donation->type !== 'cash') {
            return null;
        }

        $treasury = $treasuryId 
            ? Treasury::find($treasuryId) 
            : $this->getDefaultTreasury('donations');

        if (!$treasury) {
            throw new \Exception('لا توجد خزينة متاحة لاستلام التبرعات');
        }

        DB::beginTransaction();
        try {
            // Create treasury transaction
            $transaction = TreasuryTransaction::create([
                'treasury_id' => $treasury->id,
                'type' => 'in',
                'amount' => $donation->amount,
                'currency' => $donation->currency ?? 'EGP',
                'description' => "تبرع نقدي - " . ($donation->donor->name ?? 'متبرع'),
                'reference' => "DON-{$donation->id}",
                'transaction_date' => $donation->received_at ?? now(),
                'created_by' => auth()->id(),
                'donation_id' => $donation->id
            ]);

            // Update donation with treasury
            $donation->update(['treasury_id' => $treasury->id]);

            // Update treasury balance
            $treasury->updateBalance();

            // Create journal entry
            $this->createDonationJournalEntry($donation, $treasury);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ==========================================
     * EXPENSE INTEGRATION
     * ==========================================
     */

    /**
     * Process expense from treasury
     */
    public function processExpenseFromTreasury(Expense $expense, $treasuryId = null)
    {
        $treasury = $treasuryId 
            ? Treasury::find($treasuryId) 
            : $this->getDefaultTreasury('expenses');

        if (!$treasury) {
            throw new \Exception('لا توجد خزينة متاحة للمصروفات');
        }

        // Check balance
        if ($treasury->current_balance < $expense->amount) {
            throw new \Exception("رصيد الخزينة غير كافي. المتاح: {$treasury->current_balance}");
        }

        DB::beginTransaction();
        try {
            // Create treasury transaction
            $transaction = TreasuryTransaction::create([
                'treasury_id' => $treasury->id,
                'type' => 'out',
                'amount' => $expense->amount,
                'currency' => $expense->currency ?? 'EGP',
                'description' => "مصروف - {$expense->description}",
                'reference' => "EXP-{$expense->id}",
                'transaction_date' => $expense->expense_date ?? now(),
                'created_by' => auth()->id(),
                'expense_id' => $expense->id
            ]);

            // Update expense with treasury
            if (Schema::hasColumn('expenses', 'treasury_id')) {
                $expense->update(['treasury_id' => $treasury->id]);
            }

            // Update treasury balance
            $treasury->updateBalance();

            // Create journal entry
            $this->createExpenseJournalEntry($expense, $treasury);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ==========================================
     * PAYROLL INTEGRATION
     * ==========================================
     */

    /**
     * Process payroll payment from treasury
     */
    public function processPayrollFromTreasury(Payroll $payroll, $treasuryId = null)
    {
        $treasury = $treasuryId 
            ? Treasury::find($treasuryId) 
            : $this->getDefaultTreasury('payroll');

        if (!$treasury) {
            throw new \Exception('لا توجد خزينة متاحة للرواتب');
        }

        $amount = $payroll->net_amount ?? $payroll->amount;

        // Check balance
        if ($treasury->current_balance < $amount) {
            throw new \Exception("رصيد الخزينة غير كافي. المتاح: {$treasury->current_balance}");
        }

        DB::beginTransaction();
        try {
            // Create treasury transaction
            $transaction = TreasuryTransaction::create([
                'treasury_id' => $treasury->id,
                'type' => 'out',
                'amount' => $amount,
                'currency' => 'EGP',
                'description' => "راتب - " . ($payroll->user->name ?? 'موظف'),
                'reference' => "PAY-{$payroll->id}",
                'transaction_date' => $payroll->paid_at ?? ($payroll->created_at ?? now()),
                'created_by' => auth()->id(),
                'payroll_id' => $payroll->id
            ]);

            // Update payroll with treasury
            if (Schema::hasColumn('payrolls', 'treasury_id')) {
                $payroll->update(['treasury_id' => $treasury->id]);
            }

            // Update treasury balance
            $treasury->updateBalance();

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ==========================================
     * TRANSFER BETWEEN TREASURIES
     * ==========================================
     */

    /**
     * Transfer between treasuries
     */
    public function transferBetweenTreasuries($fromTreasuryId, $toTreasuryId, $amount, $description = null)
    {
        $fromTreasury = Treasury::findOrFail($fromTreasuryId);
        $toTreasury = Treasury::findOrFail($toTreasuryId);

        if ($fromTreasury->current_balance < $amount) {
            throw new \Exception("رصيد الخزينة المصدر غير كافي");
        }

        DB::beginTransaction();
        try {
            $reference = 'TRF-' . date('YmdHis');

            // Out from source
            TreasuryTransaction::create([
                'treasury_id' => $fromTreasury->id,
                'type' => 'out',
                'amount' => $amount,
                'currency' => $fromTreasury->currency,
                'description' => $description ?? "تحويل إلى {$toTreasury->name}",
                'reference' => $reference,
                'transaction_date' => now(),
                'created_by' => auth()->id()
            ]);

            // In to destination
            TreasuryTransaction::create([
                'treasury_id' => $toTreasury->id,
                'type' => 'in',
                'amount' => $amount,
                'currency' => $toTreasury->currency,
                'description' => $description ?? "تحويل من {$fromTreasury->name}",
                'reference' => $reference,
                'transaction_date' => now(),
                'created_by' => auth()->id()
            ]);

            // Update balances
            $fromTreasury->updateBalance();
            $toTreasury->updateBalance();

            // Create journal entry
            $this->createTransferJournalEntry($fromTreasury, $toTreasury, $amount, $description);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ==========================================
     * DELEGATE CUSTODY (عهدة مندوب)
     * ==========================================
     */

    /**
     * Issue custody to delegate
     */
    public function issueCustodyToDelegate($delegateId, $amount, $treasuryId = null, $description = null)
    {
        $mainTreasury = $treasuryId 
            ? Treasury::find($treasuryId) 
            : $this->getDefaultTreasury('main');

        // Get or create delegate treasury
        $delegateTreasury = $this->getOrCreateDelegateTreasury($delegateId);

        return $this->transferBetweenTreasuries(
            $mainTreasury->id,
            $delegateTreasury->id,
            $amount,
            $description ?? "عهدة مندوب"
        );
    }

    /**
     * Settle delegate custody
     */
    public function settleDelegateCustody($delegateId, $amountReturned, $expensesAmount = 0)
    {
        $delegateTreasury = Treasury::where('code', 'like', "DEL-{$delegateId}%")->first();
        
        if (!$delegateTreasury) {
            throw new \Exception('لا توجد عهدة لهذا المندوب');
        }

        $mainTreasury = $this->getDefaultTreasury('main');

        DB::beginTransaction();
        try {
            // Return cash to main treasury
            if ($amountReturned > 0) {
                $this->transferBetweenTreasuries(
                    $delegateTreasury->id,
                    $mainTreasury->id,
                    $amountReturned,
                    'تسوية عهدة - مرتجع نقدي'
                );
            }

            // Record expenses
            if ($expensesAmount > 0) {
                TreasuryTransaction::create([
                    'treasury_id' => $delegateTreasury->id,
                    'type' => 'out',
                    'amount' => $expensesAmount,
                    'currency' => 'EGP',
                    'description' => 'تسوية عهدة - مصروفات',
                    'reference' => 'SETTLE-' . date('YmdHis'),
                    'transaction_date' => now(),
                    'created_by' => auth()->id()
                ]);
                $delegateTreasury->updateBalance();
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ==========================================
     * HELPER METHODS
     * ==========================================
     */

    /**
     * Get default treasury for department
     */
    public function getDefaultTreasury($department)
    {
        // First try to find active main treasury
        $treasury = Treasury::where('is_active', true)
            ->where(function($q) use ($department) {
                $q->where('code', 'like', 'MAIN%')
                    ->orWhere('type', 'main');
            })
            ->first();

        if (!$treasury) {
            // Get any active treasury
            $treasury = Treasury::where('is_active', true)->first();
        }

        return $treasury;
    }

    /**
     * Get or create delegate treasury
     */
    protected function getOrCreateDelegateTreasury($delegateId)
    {
        $code = "DEL-{$delegateId}";
        
        $treasury = Treasury::where('code', $code)->first();

        if (!$treasury) {
            $delegate = \App\Models\Delegate::find($delegateId);
            $treasury = Treasury::create([
                'name' => "عهدة - " . ($delegate->name ?? "مندوب {$delegateId}"),
                'code' => $code,
                'description' => 'عهدة مندوب',
                'currency' => 'EGP',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true
            ]);
        }

        return $treasury;
    }

    /**
     * Get or create account
     */
    protected function getOrCreateAccount($name, $code, $type = 'asset')
    {
        $account = Account::where('code', $code)->first();

        if (!$account) {
            $account = Account::create([
                'name' => $name,
                'code' => $code,
                'type' => $type,
                'is_active' => true
            ]);
        }

        return $account;
    }

    /**
     * ==========================================
     * JOURNAL ENTRY CREATION
     * ==========================================
     */

    /**
     * Create journal entry for donation
     */
    protected function createDonationJournalEntry(Donation $donation, Treasury $treasury)
    {
        $treasuryAccount = $this->getOrCreateAccount(
            $treasury->name,
            self::TREASURY_TYPES['main']['account_code'],
            'asset'
        );

        $donationAccount = $this->getOrCreateAccount(
            'تبرعات نقدية',
            '4101',
            'revenue'
        );

        $entry = JournalEntry::create([
            'date' => $donation->received_at ?? now(),
            'description' => "تبرع نقدي - " . ($donation->donor->name ?? 'متبرع'),
            'reference' => "DON-{$donation->id}",
            'entry_type' => 'donation',
            'created_by' => auth()->id()
        ]);

        // Debit treasury
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $treasuryAccount->id,
            'debit' => $donation->amount,
            'credit' => 0,
            'description' => 'وارد خزينة - تبرع'
        ]);

        // Credit donations
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $donationAccount->id,
            'debit' => 0,
            'credit' => $donation->amount,
            'description' => 'إيراد تبرعات'
        ]);

        return $entry;
    }

    /**
     * Create journal entry for expense
     */
    protected function createExpenseJournalEntry(Expense $expense, Treasury $treasury)
    {
        $treasuryAccount = $this->getOrCreateAccount(
            $treasury->name,
            self::TREASURY_TYPES['main']['account_code'],
            'asset'
        );

        $expenseAccount = $this->getOrCreateAccount(
            'مصروفات عامة',
            '5101',
            'expense'
        );

        $entry = JournalEntry::create([
            'date' => $expense->expense_date ?? now(),
            'description' => "مصروف - " . ($expense->description ?? ''),
            'reference' => "EXP-{$expense->id}",
            'entry_type' => 'expense',
            'created_by' => auth()->id()
        ]);

        // Debit expense
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expenseAccount->id,
            'debit' => $expense->amount,
            'credit' => 0,
            'description' => $expense->description
        ]);

        // Credit treasury
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $treasuryAccount->id,
            'debit' => 0,
            'credit' => $expense->amount,
            'description' => 'صادر خزينة - مصروف'
        ]);

        return $entry;
    }

    /**
     * Create journal entry for transfer
     */
    protected function createTransferJournalEntry(Treasury $from, Treasury $to, $amount, $description = null)
    {
        $fromAccount = $this->getOrCreateAccount($from->name, '110' . $from->id, 'asset');
        $toAccount = $this->getOrCreateAccount($to->name, '110' . $to->id, 'asset');

        $entry = JournalEntry::create([
            'date' => now(),
            'description' => $description ?? "تحويل من {$from->name} إلى {$to->name}",
            'reference' => 'TRF-' . date('YmdHis'),
            'entry_type' => 'transfer',
            'created_by' => auth()->id()
        ]);

        // Debit destination
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $toAccount->id,
            'debit' => $amount,
            'credit' => 0,
            'description' => "وارد من {$from->name}"
        ]);

        // Credit source
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $fromAccount->id,
            'debit' => 0,
            'credit' => $amount,
            'description' => "صادر إلى {$to->name}"
        ]);

        return $entry;
    }

    /**
     * ==========================================
     * STATISTICS & REPORTS
     * ==========================================
     */

    /**
     * Get treasury statistics
     */
    public function getTreasuryStats($treasuryId = null)
    {
        $query = Treasury::query();
        
        if ($treasuryId) {
            $query->where('id', $treasuryId);
        }

        $treasuries = $query->get();

        return [
            'total_balance' => $treasuries->sum('current_balance'),
            'total_treasuries' => $treasuries->count(),
            'active_treasuries' => $treasuries->where('is_active', true)->count(),
            'by_type' => $this->getStatsByType()
        ];
    }

    /**
     * Get stats by treasury type
     */
    protected function getStatsByType()
    {
        $stats = [];
        
        foreach (self::TREASURY_TYPES as $type => $config) {
            $treasuries = Treasury::where('code', 'like', "{$config['code_prefix']}%")->get();
            $stats[$type] = [
                'name' => $config['name'],
                'count' => $treasuries->count(),
                'total_balance' => $treasuries->sum('current_balance')
            ];
        }

        return $stats;
    }

    /**
     * Get department treasury summary
     */
    public function getDepartmentTreasurySummary($department)
    {
        $treasuries = Treasury::where('is_active', true)->get();
        
        $summary = [
            'department' => $department,
            'available_treasuries' => [],
            'total_available_balance' => 0
        ];

        foreach ($treasuries as $treasury) {
            // Check if treasury is linked to this department
            foreach (self::TREASURY_TYPES as $type => $config) {
                if (in_array($department, $config['departments'])) {
                    if (str_starts_with($treasury->code, $config['code_prefix'])) {
                        $summary['available_treasuries'][] = [
                            'id' => $treasury->id,
                            'name' => $treasury->name,
                            'balance' => $treasury->current_balance,
                            'type' => $type
                        ];
                        $summary['total_available_balance'] += $treasury->current_balance;
                    }
                }
            }
        }

        return $summary;
    }
    /**
     * Cancel a donation transaction and reverse its financial impact
     */
    public function cancelDonationTransaction(Donation $donation, $reason = null)
    {
        if ($donation->type !== 'cash') {
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Find the original transaction
            $transaction = TreasuryTransaction::where('donation_id', $donation->id)
                ->where('type', 'in')
                ->where('status', 'active')
                ->first();

            if ($transaction) {
                // Update original transaction status
                $transaction->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'cancellation_reason' => $reason
                ]);

                // Create reversal transaction (Out)
                $reversal = TreasuryTransaction::create([
                    'treasury_id' => $transaction->treasury_id,
                    'type' => 'out', // Reverse of 'in'
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'description' => "إلغاء تبرع - " . $transaction->description,
                    'reference' => "REV-" . $transaction->reference,
                    'transaction_date' => now(),
                    'created_by' => auth()->id(),
                    'donation_id' => $donation->id,
                    'status' => 'active' // This is an active reversal transaction
                ]);

                // Update treasury balance
                $treasury = Treasury::find($transaction->treasury_id);
                if ($treasury) {
                    $treasury->updateBalance();
                }

                // Reverse Journal Entry
                $this->createReversalJournalEntry($donation, $treasury, $transaction->amount);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createReversalJournalEntry(Donation $donation, Treasury $treasury, $amount)
    {
        $donationAccount = $this->getOrCreateAccount('401', 'Donations Revenue', 'revenue');
        $treasuryAccount = $this->getOrCreateAccount($treasury->code ?? '101', $treasury->name, 'asset');

        $entry = JournalEntry::create([
            'date' => now(),
            'description' => "قيد عكسي - إلغاء تبرع " . $donation->id,
            'reference' => "REV-DON-{$donation->id}",
            'entry_type' => 'reversal',
            'gate' => 'donation',
            'created_by' => auth()->id()
        ]);

        // Debit Donation Revenue (Decrease Revenue)
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $donationAccount->id,
            'debit' => $amount,
            'credit' => 0,
            'description' => 'إلغاء إيراد تبرعات'
        ]);

        // Credit Treasury (Decrease Asset)
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $treasuryAccount->id,
            'debit' => 0,
            'credit' => $amount,
            'description' => 'رد وارد خزينة'
        ]);
    }

    /**
     * Cancel an expense transaction and reverse its financial impact
     */
    public function cancelExpenseTransaction(Expense $expense, $reason = null)
    {
        DB::beginTransaction();
        try {
            // 1. Find the original transaction
            $transaction = TreasuryTransaction::where('expense_id', $expense->id)
                ->where('type', 'out')
                ->where('status', 'active')
                ->first();

            if ($transaction) {
                // Update original transaction status
                $transaction->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'cancellation_reason' => $reason
                ]);

                // Create reversal transaction (In)
                $reversal = TreasuryTransaction::create([
                    'treasury_id' => $transaction->treasury_id,
                    'type' => 'in', // Reverse of 'out'
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'description' => "إلغاء مصروف - " . $transaction->description,
                    'reference' => "REV-" . $transaction->reference,
                    'transaction_date' => now(),
                    'created_by' => auth()->id(),
                    'expense_id' => $expense->id,
                    'status' => 'active'
                ]);

                // Update treasury balance
                $treasury = Treasury::find($transaction->treasury_id);
                if ($treasury) {
                    $treasury->updateBalance();
                }

                // Reverse Journal Entry
                $this->createExpenseReversalJournalEntry($expense, $treasury, $transaction->amount);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createExpenseReversalJournalEntry(Expense $expense, Treasury $treasury, $amount)
    {
        $treasuryAccount = $this->getOrCreateAccount(
            $treasury->name,
            self::TREASURY_TYPES['main']['account_code'],
            'asset'
        );

        $expenseAccount = $this->getOrCreateAccount(
            'مصروفات عامة',
            '5101',
            'expense'
        );

        $entry = JournalEntry::create([
            'date' => now(),
            'description' => "قيد عكسي - إلغاء مصروف " . $expense->id,
            'reference' => "REV-EXP-{$expense->id}",
            'entry_type' => 'reversal',
            'created_by' => auth()->id()
        ]);

        // Debit Treasury (Increase Asset)
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $treasuryAccount->id,
            'debit' => $amount,
            'credit' => 0,
            'description' => 'رد صادر خزينة - إلغاء مصروف'
        ]);

        // Credit Expense (Decrease Expense)
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expenseAccount->id,
            'debit' => 0,
            'credit' => $amount,
            'description' => 'إلغاء مصروف'
        ]);

        return $entry;
    }

    /**
     * Cancel a payroll transaction and reverse its financial impact
     */
    public function cancelPayrollTransaction(Payroll $payroll, $reason = null)
    {
        DB::beginTransaction();
        try {
            // Find the original transaction
            $transaction = TreasuryTransaction::where('payroll_id', $payroll->id)
                ->where('type', 'out')
                ->where('status', 'active')
                ->first();

            if ($transaction) {
                // Update status
                $transaction->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'cancellation_reason' => $reason
                ]);

                // Create reversal transaction (In)
                $reversal = TreasuryTransaction::create([
                    'treasury_id' => $transaction->treasury_id,
                    'type' => 'in',
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'description' => "إلغاء راتب - " . $transaction->description,
                    'reference' => "REV-" . $transaction->reference,
                    'transaction_date' => now(),
                    'created_by' => auth()->id(),
                    'payroll_id' => $payroll->id,
                    'status' => 'active'
                ]);

                // Update balance
                $treasury = Treasury::find($transaction->treasury_id);
                if ($treasury) $treasury->updateBalance();

                // Reverse Journal Entry
                $this->createPayrollReversalJournalEntry($payroll, $treasury, $transaction->amount);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createPayrollReversalJournalEntry(Payroll $payroll, Treasury $treasury, $amount)
    {
        $treasuryAccount = $this->getOrCreateAccount($treasury->name, self::TREASURY_TYPES['main']['account_code'], 'asset');
        $expenseAccount = $this->getOrCreateAccount('مصروف الرواتب والأجور', '5101', 'expense');

        $entry = JournalEntry::create([
            'date' => now(),
            'description' => "قيد عكسي - إلغاء راتب " . ($payroll->user->name ?? $payroll->id),
            'reference' => "REV-PAY-{$payroll->id}",
            'entry_type' => 'reversal',
            'created_by' => auth()->id()
        ]);

        // Debit Treasury
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $treasuryAccount->id,
            'debit' => $amount,
            'credit' => 0,
            'description' => 'إلغاء صرف راتب'
        ]);

        // Credit Expense
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expenseAccount->id,
            'debit' => 0,
            'credit' => $amount,
            'description' => 'عكس مصروف رواتب'
        ]);
    }
}

