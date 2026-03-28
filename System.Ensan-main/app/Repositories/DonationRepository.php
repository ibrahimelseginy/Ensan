<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Donation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class DonationRepository
{
    public function paginateFiltered(array $filters, int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        $q            = $filters['q'] ?? '';
        $type         = $filters['type'] ?? null;
        $projectId    = $filters['project_id'] ?? null;
        $campaignId   = $filters['campaign_id'] ?? null;
        $guestHouseId = $filters['guest_house_id'] ?? null;
        $channel      = $filters['channel'] ?? null;

        return Donation::with(['donor', 'project', 'campaign', 'warehouse'])
            ->when($type, fn($qr) => $qr->where('type', $type))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn($qr) => $qr->where('guest_house_id', $guestHouseId))
            ->when($channel, fn($qr) => $qr->where('cash_channel', $channel))
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->whereHas('donor', fn($d) => $d->where('name', 'like', '%' . $q . '%'))
                      ->orWhere('receipt_number', 'like', '%' . $q . '%')
                      ->orWhereHas('project', fn($p) => $p->where('name', 'like', '%' . $q . '%'))
                      ->orWhereHas('campaign', fn($c) => $c->where('name', 'like', '%' . $q . '%'))
                      ->orWhereHas('warehouse', fn($wh) => $wh->where('name', 'like', '%' . $q . '%'));
                });
            })
            ->orderByDesc('received_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName);
    }

    public function findById(int $id): ?Donation
    {
        return Donation::with(['donor', 'project', 'campaign', 'warehouse', 'delegate', 'route'])->find($id);
    }

    public function create(array $data): Donation
    {
        return Donation::create($data);
    }

    public function update(Donation $donation, array $data): bool
    {
        return $donation->update($data);
    }

    public function delete(Donation $donation): bool
    {
        return $donation->delete();
    }

    public function getDailySummary(int $days = 7, array $filters = []): Collection
    {
        $projectId    = $filters['project_id'] ?? null;
        $campaignId   = $filters['campaign_id'] ?? null;
        $guestHouseId = $filters['guest_house_id'] ?? null;

        return Donation::select(DB::raw("DATE(received_at) as day"), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->whereDate('received_at', '>=', now()->subDays($days))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn($qr) => $qr->where('guest_house_id', $guestHouseId))
            ->groupBy(DB::raw("DATE(received_at)"))
            ->orderByDesc(DB::raw("DATE(received_at)"))
            ->limit($days)
            ->get();
    }

    public function getTodaySummaryByChannel(array $filters = []): Collection
    {
        $projectId    = $filters['project_id'] ?? null;
        $campaignId   = $filters['campaign_id'] ?? null;
        $guestHouseId = $filters['guest_house_id'] ?? null;
        $today        = now()->toDateString();

        return Donation::select('cash_channel', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->whereDate('received_at', $today)
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn($qr) => $qr->where('guest_house_id', $guestHouseId))
            ->groupBy('cash_channel')
            ->get();
    }

    public function getTodayInKindSummary(array $filters = []): ?object
    {
        $projectId    = $filters['project_id'] ?? null;
        $campaignId   = $filters['campaign_id'] ?? null;
        $guestHouseId = $filters['guest_house_id'] ?? null;
        $today        = now()->toDateString();

        return Donation::select(DB::raw('COUNT(*) as count'), DB::raw('SUM(estimated_value) as total'))
            ->where('type', 'in_kind')
            ->where('status', '!=', 'cancelled')
            ->whereDate('received_at', $today)
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn($qr) => $qr->where('guest_house_id', $guestHouseId))
            ->first();
    }
}
