<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RamadanBag;
use App\Repositories\RamadanBagRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class RamadanBagService
{
    public function __construct(
        private RamadanBagRepository $bagRepository
    ) {}

    public function getFilteredBags(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->bagRepository->paginateFiltered($filters, $perPage);
    }

    public function getStatistics(): array
    {
        $rawStats = $this->bagRepository->getRegionStats();
        $statistics = [];

        foreach ($rawStats as $stat) {
            $projectName = $stat->project ? $stat->project->name : 'بدون مشروع';
            $regionName  = $stat->region ?: 'أخرى';
            $key         = $projectName . '_' . $regionName;

            if (!isset($statistics[$key])) {
                $statistics[$key] = [
                    'project'        => $projectName,
                    'region'         => $regionName,
                    'families_count' => 0,
                    'items_count'    => 0,
                ];
            }
            $statistics[$key]['families_count'] += $stat->families_count;
            $statistics[$key]['items_count']    += $stat->bags_sum ?: $stat->families_count;
        }

        return $statistics;
    }

    public function findBagById(int $id): ?RamadanBag
    {
        return $this->bagRepository->findById($id);
    }

    public function createBag(array $data): RamadanBag
    {
        return $this->bagRepository->create($data);
    }

    public function updateBag(RamadanBag $bag, array $data): bool
    {
        return $this->bagRepository->update($bag, $data);
    }

    public function deleteBag(RamadanBag $bag): bool
    {
        return $this->bagRepository->delete($bag);
    }
}
