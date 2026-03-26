<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\Warehouse;
use Carbon\Carbon;

class DonationProcessingService
{
    protected $warehouseService;

    public function __construct(WarehouseIntegrationService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Process new donation
     * 
     * @param array $data
     * @return Donation
     */
    public function processDonation($data)
    {
        // Create donation record
        $donation = Donation::create([
            'donor_id' => $data['donor_id'],
            'type' => $data['type'],
            'cash_channel' => $data['cash_channel'] ?? null,
            'amount' => $data['type'] === 'cash' ? $data['amount'] : null,
            'currency' => $data['currency'] ?? 'EGP',
            'receipt_number' => $data['receipt_number'] ?? $this->generateReceiptNumber(),
            'estimated_value' => $data['type'] === 'in_kind' ? $data['estimated_value'] : null,
            'project_id' => $data['project_id'] ?? null,
            'campaign_id' => $data['campaign_id'] ?? null,
            'warehouse_id' => $data['type'] === 'in_kind' ? $data['warehouse_id'] : null,
            'treasury_id' => $data['type'] === 'cash' ? $data['treasury_id'] : null,
            'item_id' => $data['type'] === 'in_kind' ? $data['item_id'] : null,
            'quantity' => $data['type'] === 'in_kind' ? $data['quantity'] : null,
            'delegate_id' => $data['delegate_id'] ?? null,
            'route_id' => $data['route_id'] ?? null,
            'allocation_note' => $data['allocation_note'] ?? null,
            'received_at' => $data['received_at'] ?? now(),
            'created_by' => auth()->id()
        ]);

        // Process based on type
        if ($donation->type === 'cash') {
            $this->processCashDonation($donation);
        } else {
            $this->processInKindDonation($donation);
        }

        return $donation;
    }

    /**
     * Process cash donation
     * 
     * @param Donation $donation
     * @return void
     */
    protected function processCashDonation($donation)
    {
        if (!$donation->treasury_id) {
            // Get default treasury
            $treasury = Treasury::where('is_active', true)->first();
            if ($treasury) {
                $donation->update(['treasury_id' => $treasury->id]);
            } else {
                throw new \Exception('لا توجد خزينة نشطة. يرجى إنشاء خزينة أولاً.');
            }
        }

        // Add to treasury
        $treasury = Treasury::find($donation->treasury_id);
        
        if ($treasury) {
            $treasury->addTransaction(
                'in',
                $donation->amount,
                "تبرع نقدي - {$donation->donor->name}",
                "DONATION-{$donation->id}"
            );

            // Link treasury transaction to donation
            $treasuryTransaction = TreasuryTransaction::where('reference', "DONATION-{$donation->id}")->first();
            if ($treasuryTransaction) {
                $treasuryTransaction->update(['donation_id' => $donation->id]);
            }
        }
    }

    /**
     * Process in-kind donation
     * 
     * @param Donation $donation
     * @return void
     */
    protected function processInKindDonation($donation)
    {
        if (!$donation->warehouse_id) {
            // Get default warehouse
            $warehouse = Warehouse::where('is_active', true)->first();
            if ($warehouse) {
                $donation->update(['warehouse_id' => $warehouse->id]);
            } else {
                throw new \Exception('لا يوجد مخزن نشط. يرجى إنشاء مخزن أولاً.');
            }
        }

        if (!$donation->item_id) {
            throw new \Exception('يجب تحديد الصنف للتبرع العيني');
        }

        // Add to inventory
        $inventoryData = [
            'item_id' => $donation->item_id,
            'warehouse_id' => $donation->warehouse_id,
            'quantity' => $donation->quantity ?? 1,
            'unit_cost' => $donation->estimated_value ?? 0,
            'donation_id' => $donation->id,
            'date' => $donation->received_at,
            'notes' => "تبرع عيني - {$donation->donor->name}",
            'create_journal_entry' => true
        ];

        $inventoryTransaction = $this->warehouseService->processDonationReceived($inventoryData);

        // Mark donation as added to inventory
        $donation->update(['auto_added_to_inventory' => true]);
    }

    /**
     * Generate receipt number
     * 
     * @return string
     */
    protected function generateReceiptNumber()
    {
        $year = date('Y');
        $lastDonation = Donation::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastDonation ? (int) substr($lastDonation->receipt_number, -6) + 1 : 1;

        return 'REC-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get donation statistics
     * 
     * @param array $filters
     * @return array
     */
    public function getDonationStats($filters = [])
    {
        $query = Donation::query();

        if (isset($filters['start_date'])) {
            $query->where('received_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('received_at', '<=', $filters['end_date']);
        }

        $totalDonations = $query->count();
        $cashDonations = (clone $query)->where('type', 'cash')->count();
        $inKindDonations = (clone $query)->where('type', 'in_kind')->count();

        $totalCashAmount = (clone $query)->where('type', 'cash')->sum('amount');
        $totalInKindValue = (clone $query)->where('type', 'in_kind')->sum('estimated_value');

        $totalValue = $totalCashAmount + $totalInKindValue;

        return [
            'total_donations' => $totalDonations,
            'cash_donations' => $cashDonations,
            'in_kind_donations' => $inKindDonations,
            'total_cash_amount' => $totalCashAmount,
            'total_in_kind_value' => $totalInKindValue,
            'total_value' => $totalValue,
            'average_donation' => $totalDonations > 0 ? $totalValue / $totalDonations : 0
        ];
    }

    /**
     * Get treasury balance
     * 
     * @param int $treasuryId
     * @return float
     */
    public function getTreasuryBalance($treasuryId)
    {
        $treasury = Treasury::find($treasuryId);
        return $treasury ? $treasury->current_balance : 0;
    }

    /**
     * Get warehouse stock value
     * 
     * @param int $warehouseId
     * @return float
     */
    public function getWarehouseStockValue($warehouseId)
    {
        $warehouse = Warehouse::find($warehouseId);
        return $warehouse ? $warehouse->getTotalStockValue() : 0;
    }
}
