<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Delegate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class DelegateRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q        = $filters['q'] ?? null;
        $routeId  = $filters['route_id'] ?? null;
        $active   = $filters['active'] ?? null;
        $hasPhone = $filters['has_phone'] ?? null;
        $sort     = $filters['sort'] ?? 'name';
        $dir      = $filters['dir'] ?? 'asc';

        return Delegate::query()
            ->with(['route'])
            ->withCount('donations')
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . $q . '%')
                      ->orWhere('phone', 'like', '%' . $q . '%');
                });
            })
            ->when($routeId, fn($qb) => $qb->where('route_id', $routeId))
            ->when(!is_null($active) && $active !== '', fn($qb) => $qb->where('active', $active === '1'))
            ->when($hasPhone === '1', fn($qb) => $qb->whereNotNull('phone')->where('phone', '<>', ''))
            ->orderBy($sort, $dir)
            ->paginate($perPage);
    }

    public function findById(int $id): ?Delegate
    {
        return Delegate::with(['route', 'trips'])->find($id);
    }

    public function create(array $data): Delegate
    {
        return Delegate::create($data);
    }

    public function update(Delegate $delegate, array $data): bool
    {
        return $delegate->update($data);
    }

    public function delete(Delegate $delegate): bool
    {
        return $delegate->delete();
    }

    public function allActive(): Collection
    {
        return Delegate::where('active', true)->orderBy('name')->get();
    }
}
