<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class ItemRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        return Item::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->withCount('transactions')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Item
    {
        return Item::find($id);
    }

    public function create(array $data): Item
    {
        return Item::create($data);
    }

    public function update(Item $item, array $data): bool
    {
        return $item->update($data);
    }

    public function delete(Item $item): bool
    {
        return $item->delete();
    }

    public function getStockByWarehouse(int $itemId): Collection
    {
        return InventoryTransaction::where('item_id', $itemId)
            ->select('warehouse_id', DB::raw("SUM(CASE WHEN type = 'in' THEN quantity WHEN type = 'out' THEN -quantity ELSE 0 END) as current_stock"))
            ->groupBy('warehouse_id')
            ->having('current_stock', '>', 0)
            ->with('warehouse')
            ->get();
    }

    public function getRecentTransactions(Item $item, int $limit = 10): Collection
    {
        return $item->transactions()
            ->with(['warehouse'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
