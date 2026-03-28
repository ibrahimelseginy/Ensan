<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RamadanIftar;
use App\Repositories\RamadanIftarRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class RamadanIftarService
{
    public function __construct(
        private RamadanIftarRepository $iftarRepository
    ) {}

    public function getFilteredIftars(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->iftarRepository->paginateFiltered($filters, $perPage);
    }

    public function getStatistics(): array
    {
        $rawStats = $this->iftarRepository->getRegionStats();
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
            $statistics[$key]['items_count']    += $stat->meals_sum;
        }

        return $statistics;
    }

    public function findIftarById(int $id): ?RamadanIftar
    {
        return $this->iftarRepository->findById($id);
    }

    public function createIftar(array $data): RamadanIftar
    {
        return $this->iftarRepository->create($data);
    }

    public function updateIftar(RamadanIftar $iftar, array $data): bool
    {
        return $this->iftarRepository->update($iftar, $data);
    }

    public function deleteIftar(RamadanIftar $iftar): bool
    {
        return $this->iftarRepository->delete($iftar);
    }
}
