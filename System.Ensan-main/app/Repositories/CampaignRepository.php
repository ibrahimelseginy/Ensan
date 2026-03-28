<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class RamadanBagRepository
{
    // Wait, I am overwriting the wrong file or using wrong name. Correcting to CampaignRepository.
}

final readonly class CampaignRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q      = $filters['q'] ?? null;
        $status = $filters['status'] ?? null;
        $year   = $filters['season_year'] ?? null;

        return Campaign::query()
            ->when($q, fn($qr) => $qr->where('name', 'like', '%' . $q . '%'))
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($year, fn($qr) => $qr->where('season_year', $year))
            ->orderByDesc('season_year')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Campaign
    {
        return Campaign::with(['project', 'volunteers', 'monthlyVolunteers.user', 'dailyMenus.responsible'])->find($id);
    }

    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    public function update(Campaign $campaign, array $data): bool
    {
        return $campaign->update($data);
    }

    public function delete(Campaign $campaign): bool
    {
        return $campaign->delete();
    }
}
