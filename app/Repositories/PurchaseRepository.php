<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Purchase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class PurchaseRepository
{
    public function paginateBySupplier(int $supplierId, int $perPage = 20): LengthAwarePaginator
    {
        return Purchase::where('supplier_id', $supplierId)
            ->orderByDesc('purchase_date')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Purchase
    {
        return Purchase::with('supplier')->find($id);
    }

    public function create(array $data): Purchase
    {
        return Purchase::create($data);
    }

    public function update(Purchase $purchase, array $data): bool
    {
        return $purchase->update($data);
    }

    public function delete(Purchase $purchase): bool
    {
        return $purchase->delete();
    }
}
