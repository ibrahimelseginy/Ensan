<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\InventoryTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class InventoryTransactionRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return InventoryTransaction::with([
                'item', 'warehouse', 'beneficiary', 'project', 'campaign',
                'sourceDonation.donor', 'sourceDonation.delegate', 'sourceDonation.route'
            ])
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->when($filters['warehouse_id'] ?? null, fn($q, $id) => $q->where('warehouse_id', $id))
            ->when($filters['item_id'] ?? null, fn($q, $id) => $q->where('item_id', $id))
            ->when($filters['date_from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?InventoryTransaction
    {
        return InventoryTransaction::with(['item', 'warehouse', 'beneficiary', 'project', 'campaign', 'sourceDonation'])->find($id);
    }

    public function create(array $data): InventoryTransaction
    {
        return InventoryTransaction::create($data);
    }

    public function update(InventoryTransaction $transaction, array $data): bool
    {
        return $transaction->update($data);
    }

    public function delete(InventoryTransaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function getCurrentStock(int $itemId, int $warehouseId): float
    {
        return (float) InventoryTransaction::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->selectRaw("SUM(CASE 
                WHEN type IN ('in', 'stock_count_increase', 'transfer_in') THEN quantity 
                WHEN type IN ('out', 'transfer', 'transfer_out', 'stock_count_shortage') THEN -quantity 
                ELSE 0 END) as stock")
            ->value('stock') ?? 0.0;
    }
}
