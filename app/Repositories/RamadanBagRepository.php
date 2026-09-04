<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RamadanBag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class RamadanBagRepository
{
    public function paginateFiltered(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $search = $filters['q'] ?? null;
        $status = $filters['status'] ?? null;

        return RamadanBag::with(['project', 'campaign'])
            ->when($search, function ($query) use ($search) {
                $query->where('beneficiary_name', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('phone_2', 'like', "%{$search}%");
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?RamadanBag
    {
        return RamadanBag::with(['project', 'campaign'])->find($id);
    }

    public function getRegionStats(): Collection
    {
        return RamadanBag::with('project')
            ->select('project_id', 'region', DB::raw('COUNT(*) as families_count'), DB::raw('SUM(bags_count) as bags_sum'))
            ->groupBy('project_id', 'region')
            ->get();
    }

    public function create(array $data): RamadanBag
    {
        return RamadanBag::create($data);
    }

    public function update(RamadanBag $bag, array $data): bool
    {
        return $bag->update($data);
    }

    public function delete(RamadanBag $bag): bool
    {
        return $bag->delete();
    }
}
