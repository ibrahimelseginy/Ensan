<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Donation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class TripRepository
{
    public function paginateFilteredTrips(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $routeId    = $filters['route_id'] ?? null;
        $delegateId = $filters['delegate_id'] ?? null;
        $type       = $filters['type'] ?? null;
        $q          = $filters['q'] ?? '';
        $sort       = $filters['sort'] ?? 'date_desc';

        return Donation::with(['donor', 'delegate', 'route'])
            ->whereNotNull('route_id')
            ->when($routeId, fn($qb) => $qb->where('route_id', $routeId))
            ->when($delegateId, fn($qb) => $qb->where('delegate_id', $delegateId))
            ->when($type, fn($qb) => $qb->where('type', $type))
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->whereHas('donor', fn($d) => $d->where('name', 'like', '%' . $q . '%'))
                      ->orWhereHas('delegate', fn($dl) => $dl->where('name', 'like', '%' . $q . '%'))
                      ->orWhereHas('route', fn($r) => $r->where('name', 'like', '%' . $q . '%'));
                });
            })
            ->when($sort === 'date_asc', fn($qb) => $qb->orderBy('received_at', 'asc')->orderBy('id', 'asc'))
            ->when($sort === 'date_desc', fn($qb) => $qb->orderBy('received_at', 'desc')->orderBy('id', 'desc'))
            ->when($sort === 'amount_asc', fn($qb) => $qb->orderByRaw("CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END ASC"))
            ->when($sort === 'amount_desc', fn($qb) => $qb->orderByRaw("CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END DESC"))
            ->orderByDesc('received_at')
            ->paginate($perPage);
    }

    public function getTripStats(array $filters): array
    {
        $base = Donation::whereNotNull('route_id')
            ->when($filters['route_id'] ?? null, fn($qb, $rid) => $qb->where('route_id', $rid))
            ->when($filters['delegate_id'] ?? null, fn($qb, $did) => $qb->where('delegate_id', $did))
            ->when($filters['type'] ?? null, fn($qb, $t) => $qb->where('type', $t));

        return [
            'count'   => (int) (clone $base)->count(),
            'cash'    => (float) (clone $base)->where('type', 'cash')->sum('amount'),
            'in_kind' => (float) (clone $base)->where('type', 'in_kind')->sum('estimated_value'),
        ];
    }

    public function findById(int $id): ?Donation
    {
        return Donation::with(['donor', 'delegate', 'route'])->find($id);
    }
}
