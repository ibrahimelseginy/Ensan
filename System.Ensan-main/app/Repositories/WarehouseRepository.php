<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class WarehouseRepository
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Warehouse::withCount('transactions')->orderBy('name')->paginate($perPage);
    }

    public function findById(int $id): ?Warehouse
    {
        return Warehouse::find($id);
    }

    public function create(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function update(Warehouse $warehouse, array $data): bool
    {
        return $warehouse->update($data);
    }

    public function delete(Warehouse $warehouse): bool
    {
        return $warehouse->delete();
    }

    public function getStock(int $warehouseId): Collection
    {
        return InventoryTransaction::where('warehouse_id', $warehouseId)
            ->select('item_id', DB::raw("SUM(CASE WHEN type = 'in' THEN quantity WHEN type = 'out' THEN -quantity ELSE 0 END) as current_stock"))
            ->groupBy('item_id')
            ->having('current_stock', '>', 0)
            ->with('item')
            ->get();
    }

    public function getRecentTransactions(Warehouse $warehouse, int $limit = 10): Collection
    {
        return $warehouse->transactions()
            ->with(['item']) 
            ->latest()
            ->take($limit)
            ->get();
    }
}
