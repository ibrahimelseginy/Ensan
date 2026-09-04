<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestHouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class GuestHouseRepository
{
    public function paginateFiltered(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $q      = $filters['q'] ?? '';
        $status = $filters['status'] ?? null;
        $governorate = $filters['governorate'] ?? null;

        return GuestHouse::query()
            ->withCount(['wings', 'beds', 'stays as resident_stays_count' => fn ($query) => $query->where('status', 'resident')])
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")->orWhere('location', 'like', "%$q%");
            })
            ->when($status, fn($qb) => $qb->where('status', $status))
            ->when($governorate, fn($qb) => $qb->where('governorate', $governorate))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?GuestHouse
    {
        return GuestHouse::with(['manager', 'volunteers', 'monthlyVolunteers.user'])->find($id);
    }

    public function create(array $data): GuestHouse
    {
        return GuestHouse::create($data);
    }

    public function update(GuestHouse $house, array $data): bool
    {
        return $house->update($data);
    }

    public function delete(GuestHouse $house): bool
    {
        return $house->delete();
    }
}
