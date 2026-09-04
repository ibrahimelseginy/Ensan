<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;

class WarehouseIntegrationService
{
    /**
     * Process incoming donation (in-kind)
     * 
     * @param array $data
     * @return InventoryTransaction
     */
    public function processDonationReceived($data)
    {
        // Create inventory transaction
        $transaction = InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'in',
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'] ?? 0,
            'source_donation_id' => $data['donation_id'] ?? null,
            'transaction_date' => $data['date'] ?? now(),
            'reference' => 'DONATION-' . ($data['donation_id'] ?? time()),
            'notes' => $data['notes'] ?? 'تبرع عيني',
            'status' => 'approved',
            'user_id' => auth()->id(),
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null
        ]);

        $transaction->calculateTotalCost();
        $transaction->save();

        // Create journal entry if accounting integration enabled
        if ($data['create_journal_entry'] ?? false) {
            $this->createJournalEntryForDonation($transaction);
        }

        return $transaction;
    }

    /**
     * Process distribution to beneficiary
     * 
     * @param array $data
     * @return InventoryTransaction
     */
    public function processDistributionToBeneficiary($data)
    {
        // Check stock availability
        $currentStock = $this->getCurrentStock($data['item_id'], $data['warehouse_id']);
        
        if ($currentStock < $data['quantity']) {
            throw new \Exception('المخزون غير كافٍ. المتاح: ' . $currentStock);
        }

        // Create outgoing transaction
        $transaction = InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'out',
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'] ?? $this->getAverageCost($data['item_id']),
            'beneficiary_id' => $data['beneficiary_id'],
            'project_id' => $data['project_id'] ?? null,
            'campaign_id' => $data['campaign_id'] ?? null,
            'transaction_date' => $data['date'] ?? now(),
            'reference' => 'DIST-' . ($data['beneficiary_id'] ?? time()),
            'notes' => $data['notes'] ?? 'توزيع على مستفيد',
            'status' => $data['status'] ?? 'pending',
            'user_id' => auth()->id()
        ]);

        $transaction->calculateTotalCost();
        $transaction->save();

        // Create journal entry
        if ($data['create_journal_entry'] ?? false) {
            $this->createJournalEntryForDistribution($transaction);
        }

        return $transaction;
    }

    /**
     * Process purchase/expense
     * 
     * @param array $data
     * @return InventoryTransaction
     */
    public function processPurchase($data)
    {
        $transaction = InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'in',
            'quantity' => $data['quantity'],
            'unit_cost' => $data['unit_cost'],
            'expense_id' => $data['expense_id'] ?? null,
            'transaction_date' => $data['date'] ?? now(),
            'reference' => 'PURCHASE-' . time(),
            'notes' => $data['notes'] ?? 'شراء',
            'status' => 'approved',
            'user_id' => auth()->id(),
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null
        ]);

        $transaction->calculateTotalCost();
        $transaction->save();

        // Create journal entry
        if ($data['create_journal_entry'] ?? false) {
            $this->createJournalEntryForPurchase($transaction);
        }

        return $transaction;
    }

    /**
     * Process transfer between warehouses
     * 
     * @param array $data
     * @return array
     */
    public function processTransfer($data)
    {
        // Check stock in source warehouse
        $currentStock = $this->getCurrentStock($data['item_id'], $data['from_warehouse_id']);
        
        if ($currentStock < $data['quantity']) {
            throw new \Exception('المخزون غير كافٍ في المخزن المصدر');
        }

        // Out from source warehouse
        $outTransaction = InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['from_warehouse_id'],
            'type' => 'transfer',
            'quantity' => $data['quantity'],
            'unit_cost' => $this->getAverageCost($data['item_id']),
            'transaction_date' => $data['date'] ?? now(),
            'reference' => 'TRANSFER-OUT-' . time(),
            'notes' => 'تحويل إلى مخزن ' . $data['to_warehouse_id'],
            'status' => 'approved',
            'user_id' => auth()->id()
        ]);

        $outTransaction->calculateTotalCost();
        $outTransaction->save();

        // In to destination warehouse
        $inTransaction = InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['to_warehouse_id'],
            'type' => 'transfer',
            'quantity' => $data['quantity'],
            'unit_cost' => $outTransaction->unit_cost,
            'transaction_date' => $data['date'] ?? now(),
            'reference' => 'TRANSFER-IN-' . time(),
            'notes' => 'تحويل من مخزن ' . $data['from_warehouse_id'],
            'status' => 'approved',
            'user_id' => auth()->id(),
            'related_transaction_id' => $outTransaction->id
        ]);

        $inTransaction->calculateTotalCost();
        $inTransaction->save();

        // Link transactions
        $outTransaction->update(['related_transaction_id' => $inTransaction->id]);

        return [
            'out' => $outTransaction,
            'in' => $inTransaction
        ];
    }

    /**
     * Create journal entry for donation received
     */
    private function createJournalEntryForDonation($transaction)
    {
        $inventoryAccount = $this->getOrCreateAccount('1301', 'المخزون', 'asset');
        $donationsAccount = $this->getOrCreateAccount('4101', 'تبرعات عينية', 'revenue');

        $journalEntry = JournalEntry::create([
            'date' => $transaction->transaction_date,
            'description' => "استلام تبرع عيني - {$transaction->item->name}",
            'entry_type' => 'inventory',
            'locked' => false
        ]);

        // Debit: Inventory
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $inventoryAccount->id,
            'debit' => $transaction->total_cost,
            'credit' => 0,
            'description' => "مخزون - {$transaction->item->name}"
        ]);

        // Credit: Donations Revenue
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $donationsAccount->id,
            'debit' => 0,
            'credit' => $transaction->total_cost,
            'description' => "تبرع عيني"
        ]);

        $transaction->update(['journal_entry_id' => $journalEntry->id]);

        return $journalEntry;
    }

    /**
     * Create journal entry for distribution
     */
    private function createJournalEntryForDistribution($transaction)
    {
        $inventoryAccount = $this->getOrCreateAccount('1301', 'المخزون', 'asset');
        $programExpenseAccount = $this->getOrCreateAccount('5301', 'مصروفات برامج', 'expense');

        $journalEntry = JournalEntry::create([
            'date' => $transaction->transaction_date,
            'description' => "توزيع على مستفيد - {$transaction->item->name}",
            'entry_type' => 'inventory',
            'locked' => false
        ]);

        // Debit: Program Expense
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $programExpenseAccount->id,
            'debit' => $transaction->total_cost,
            'credit' => 0,
            'description' => "توزيع - {$transaction->item->name}"
        ]);

        // Credit: Inventory
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $inventoryAccount->id,
            'debit' => 0,
            'credit' => $transaction->total_cost,
            'description' => "خصم من المخزون"
        ]);

        $transaction->update(['journal_entry_id' => $journalEntry->id]);

        return $journalEntry;
    }

    /**
     * Create journal entry for purchase
     */
    private function createJournalEntryForPurchase($transaction)
    {
        $inventoryAccount = $this->getOrCreateAccount('1301', 'المخزون', 'asset');
        $cashAccount = $this->getOrCreateAccount('1101', 'النقدية بالصندوق', 'asset');

        $journalEntry = JournalEntry::create([
            'date' => $transaction->transaction_date,
            'description' => "شراء مخزون - {$transaction->item->name}",
            'entry_type' => 'inventory',
            'locked' => false
        ]);

        // Debit: Inventory
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $inventoryAccount->id,
            'debit' => $transaction->total_cost,
            'credit' => 0,
            'description' => "شراء - {$transaction->item->name}"
        ]);

        // Credit: Cash
        JournalEntryLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $transaction->total_cost,
            'description' => "دفع نقدي"
        ]);

        $transaction->update(['journal_entry_id' => $journalEntry->id]);

        return $journalEntry;
    }

    /**
     * Get current stock for item in warehouse
     */
    public function getCurrentStock($itemId, $warehouseId)
    {
        $inStock = InventoryTransaction::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'in')
            ->where('status', 'approved')
            ->sum('quantity');

        $outStock = InventoryTransaction::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'out')
            ->where('status', 'approved')
            ->sum('quantity');

        return $inStock - $outStock;
    }

    /**
     * Get average cost for item
     */
    public function getAverageCost($itemId)
    {
        $transactions = InventoryTransaction::where('item_id', $itemId)
            ->where('type', 'in')
            ->where('status', 'approved')
            ->where('unit_cost', '>', 0)
            ->get();

        if ($transactions->isEmpty()) {
            return 0;
        }

        $totalCost = $transactions->sum('total_cost');
        $totalQuantity = $transactions->sum('quantity');

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    /**
     * Get or create account
     */
    private function getOrCreateAccount($code, $name, $type)
    {
        $account = Account::where('code', $code)->first();
        
        if (!$account) {
            $account = Account::create([
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'description' => $name
            ]);
        }

        return $account;
    }

    /**
     * Get warehouse statistics
     */
    public function getWarehouseStats($warehouseId = null)
    {
        $query = InventoryTransaction::query();
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $totalIn = (clone $query)->where('type', 'in')->where('status', 'approved')->sum('quantity');
        $totalOut = (clone $query)->where('type', 'out')->where('status', 'approved')->sum('quantity');
        $currentStock = $totalIn - $totalOut;

        $totalValue = (clone $query)->where('type', 'in')->where('status', 'approved')->sum('total_cost');
        
        $pendingTransactions = (clone $query)->where('status', 'pending')->count();

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'current_stock' => $currentStock,
            'total_value' => $totalValue,
            'pending_transactions' => $pendingTransactions
        ];
    }
}
