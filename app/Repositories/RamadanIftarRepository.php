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
        $region = $filters['region'] ?? null;

        return RamadanIftar::with(['project', 'campaign'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('beneficiary_name', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('guide_name', 'like', "%{$search}%")
                        ->orWhere('guide_phone', 'like', "%{$search}%");
                });
            })
            ->when($region, fn($q) => $q->where('region', $region))
            ->latest()
            ->paginate($perPage);
    }

    public function getAllFiltered(array $filters): Collection
    {
        $search = $filters['q'] ?? null;
        $region = $filters['region'] ?? null;

        return RamadanIftar::with(['project', 'campaign'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('beneficiary_name', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('guide_name', 'like', "%{$search}%")
                        ->orWhere('guide_phone', 'like', "%{$search}%");
                });
            })
            ->when($region, fn($q) => $q->where('region', $region))
            ->orderBy('region')
            ->orderBy('beneficiary_name')
            ->get();
    }

    public function getDistinctRegions(): array
    {
        return RamadanIftar::whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region')
            ->toArray();
    }

    public function findById(int $id): ?RamadanIftar
    {
        return RamadanIftar::with(['project', 'campaign'])->find($id);
    }

    public function getRegionStats(?string $region = null): Collection
    {
        return RamadanIftar::with('project')
            ->when($region, fn($q) => $q->where('region', $region))
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
