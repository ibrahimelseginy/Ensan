<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RamadanIftar;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class RamadanIftarRepository
{
    public function paginateFiltered(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $search = $filters['q'] ?? null;

        return RamadanIftar::with(['project', 'campaign'])
            ->when($search, function ($query) use ($search) {
                $query->where('beneficiary_name', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('guide_name', 'like', "%{$search}%")
                    ->orWhere('guide_phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?RamadanIftar
    {
        return RamadanIftar::with(['project', 'campaign'])->find($id);
    }

    public function getRegionStats(): Collection
    {
        return RamadanIftar::with('project')
            ->select('project_id', 'region', DB::raw('COUNT(*) as families_count'), DB::raw('SUM(meals_count) as meals_sum'))
            ->groupBy('project_id', 'region')
            ->get();
    }

    public function create(array $data): RamadanIftar
    {
        return RamadanIftar::create($data);
    }

    public function update(RamadanIftar $iftar, array $data): bool
    {
        return $iftar->update($data);
    }

    public function delete(RamadanIftar $iftar): bool
    {
        return $iftar->delete();
    }
}
