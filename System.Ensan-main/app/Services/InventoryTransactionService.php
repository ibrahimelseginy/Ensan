<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\ChangeRequest;
use App\Repositories\InventoryTransactionRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class InventoryTransactionService
{
    public function __construct(
        private InventoryTransactionRepository $transactionRepository
    ) {}

    public function getFilteredTransactions(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $transactions = $this->transactionRepository->paginateFiltered($filters, $perPage);

        $transactions->each(function(InventoryTransaction $transaction) {
            $transaction->pendingRequest = ChangeRequest::where('model_type', InventoryTransaction::class)
                ->where('model_id', $transaction->id)
                ->where('status', 'pending')
                ->first();
        });

        return $transactions;
    }

    public function findTransactionById(int $id): ?InventoryTransaction
    {
        return $this->transactionRepository->findById($id);
    }

    public function createTransaction(array $data): mixed
    {
        // Stock Validation for OUT/TRANSFER
        if (in_array($data['type'], ['out', 'transfer'])) {
            $currentStock = $this->transactionRepository->getCurrentStock((int)$data['item_id'], (int)$data['warehouse_id']);
            if ($data['quantity'] > $currentStock) {
                throw new \Exception("الكمية غير متوفرة. الرصيد الحالي: " . number_format($currentStock, 2));
            }
        }

        $executor = fn() => $this->transactionRepository->create($data);

        return ChangeRequestService::handleRequest(
            InventoryTransaction::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function createTransfer(array $data): bool
    {
        // Stock Validation
        $currentStock = $this->transactionRepository->getCurrentStock((int)$data['item_id'], (int)$data['from_warehouse_id']);
        if ($data['quantity'] > $currentStock) {
            throw new \Exception("الكمية غير متوفرة في المخزن المصدر. الرصيد الحالي: " . number_format($currentStock, 2));
        }

        DB::transaction(function() use ($data) {
            $date = $data['date'] ?? now();
            
            // 1. Create OUT transaction (Source)
            $out = $this->transactionRepository->create([
                'item_id'      => $data['item_id'],
                'warehouse_id' => $data['from_warehouse_id'],
                'type'         => 'transfer_out',
                'quantity'     => $data['quantity'],
                'project_id'   => $data['project_id'] ?? null,
                'campaign_id'  => $data['campaign_id'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'created_at'   => $date,
                'updated_at'   => $date
            ]);

            // 2. Create IN transaction (Destination)
            $in = $this->transactionRepository->create([
                'item_id'                => $data['item_id'],
                'warehouse_id'           => $data['to_warehouse_id'],
                'type'                   => 'transfer_in',
                'quantity'               => $data['quantity'],
                'project_id'             => $data['project_id'] ?? null,
                'campaign_id'            => $data['campaign_id'] ?? null,
                'notes'                  => $data['notes'] ?? null,
                'related_transaction_id' => $out->id,
                'created_at'             => $date,
                'updated_at'             => $date
            ]);

            // Update OUT to link to IN
            $this->transactionRepository->update($out, ['related_transaction_id' => $in->id]);
        });

        return true;
    }

    public function createReconcile(array $data): InventoryTransaction
    {
        // Stock Validation if shortage
        if ($data['type'] === 'stock_count_shortage') {
            $currentStock = $this->transactionRepository->getCurrentStock((int)$data['item_id'], (int)$data['warehouse_id']);
            if ($data['quantity'] > $currentStock) {
                throw new \Exception("لا يمكن تسجيل عجز أكبر من الرصيد الحالي (" . number_format($currentStock, 2) . ").");
            }
        }

        $date = $data['date'] ?? now();
        $data['created_at'] = $date;
        $data['updated_at'] = $date;

        return $this->transactionRepository->create($data);
    }

    public function updateTransaction(InventoryTransaction $transaction, array $data): mixed
    {
        $executor = function () use ($transaction, $data) {
            $this->transactionRepository->update($transaction, $data);
            return $transaction;
        };

        return ChangeRequestService::handleRequest(
            InventoryTransaction::class,
            $transaction->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteTransaction(InventoryTransaction $transaction): mixed
    {
        $executor = function () use ($transaction) {
            return $this->transactionRepository->delete($transaction);
        };

        return ChangeRequestService::handleRequest(
            InventoryTransaction::class,
            $transaction->id,
            'delete',
            ['note' => 'حذف حركة مخزون'],
            $executor,
            true
        );
    }

    public function getGlobalStats(): array
    {
        $today = now()->toDateString();
        
        $tripsToday = (int) InventoryTransaction::whereDate('created_at', $today)->count();
        
        $valueToday = (float) DB::table('inventory_transactions as it')
            ->join('donations as d', 'd.id', '=', 'it.source_donation_id')
            ->whereDate('it.created_at', $today)
            ->selectRaw('COALESCE(SUM(COALESCE(d.amount, d.estimated_value, 0)), 0) as total')
            ->value('total');

        return [
            'date'        => $today,
            'trips_today' => $tripsToday,
            'value_today' => $valueToday,
        ];
    }
}
