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
        return DB::transaction(function () use ($data) {
            $this->lockItem((int) $data['item_id']);
            $this->assertStock($data, 0);
            return InventoryTransaction::create($data);
        }, 3);
    }

    public function update(InventoryTransaction $transaction, array $data): bool
    {
        return DB::transaction(function () use ($transaction, $data) {
            $this->lockItem((int) $transaction->item_id);
            $transaction = InventoryTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            if ($transaction->related_transaction_id) {
                throw \Illuminate\Validation\ValidationException::withMessages(['transaction' => 'لا يمكن تعديل طرف تحويل منفرداً. يجب عكس التحويل بالكامل.']);
            }
            $merged = array_merge($transaction->getAttributes(), $data);
            $this->assertStock($merged, $this->signedQuantity($transaction->getAttributes()));
            return $transaction->update($data);
        }, 3);
    }

    public function delete(InventoryTransaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->lockItem((int) $transaction->item_id);
            $transaction = InventoryTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            if ($transaction->related_transaction_id) {
                throw \Illuminate\Validation\ValidationException::withMessages(['transaction' => 'لا يمكن حذف طرف تحويل منفرداً. يجب عكس التحويل بالكامل.']);
            }
            $data = $transaction->getAttributes();
            $data['quantity'] = 0;
            $this->assertStock($data, $this->signedQuantity($transaction->getAttributes()));
            return $transaction->delete();
        }, 3);
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
    private function lockItem(int $id): void
    {
        \App\Models\Item::query()->whereKey($id)->lockForUpdate()->firstOrFail();
    }

    private function signedQuantity(array $data): float
    {
        return match ($data['type']) {
            'in', 'stock_count_increase', 'transfer_in' => (float) $data['quantity'],
            'out', 'transfer', 'transfer_out', 'stock_count_shortage' => -(float) $data['quantity'],
            default => 0,
        };
    }

    private function assertStock(array $data, float $previous): void
    {
        $stock = $this->getCurrentStock((int) $data['item_id'], (int) $data['warehouse_id']);
        if ($stock - $previous + $this->signedQuantity($data) < -0.00001) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'العملية ستجعل رصيد المخزون سالباً. يرجى مراجعة الكمية الحالية.']);
        }
    }
}
