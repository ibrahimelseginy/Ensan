<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class SupplierRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = $filters['q'] ?? null;

        return Supplier::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                      ->orWhere('phone', 'like', '%' . $q . '%');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Supplier
    {
        return Supplier::with(['purchases'])->find($id);
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        return $supplier->update($data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}
