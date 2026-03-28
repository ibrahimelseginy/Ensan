<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignDailyMenu;
use App\Models\ChangeRequest;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\CampaignMonthlyVolunteer;
use App\Repositories\CampaignRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

final readonly class CampaignService
{
    public function __construct(
        private CampaignRepository $campaignRepository
    ) {}

    public function getFilteredCampaigns(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $campaigns = $this->campaignRepository->paginateFiltered($filters, $perPage);

        $campaigns->each(function(Campaign $campaign) {
            $campaign->pendingRequest = ChangeRequest::where('model_type', Campaign::class)
                ->where('model_id', $campaign->id)
                ->where('status', 'pending')
                ->first();
        });

        return $campaigns;
    }

    public function findCampaignById(int $id): ?Campaign
    {
        return $this->campaignRepository->findById($id);
    }

    public function getCampaignStats(Campaign $campaign): array
    {
        $cashSum      = (float) Donation::where('campaign_id', $campaign->id)->where('type', 'cash')->sum('amount');
        $inKindSum    = (float) Donation::where('campaign_id', $campaign->id)->where('type', 'in_kind')->sum('estimated_value');
        $expensesTotal = (float) Expense::where('campaign_id', $campaign->id)->sum('amount');
        
        $donationsTotal = $cashSum + $inKindSum;
        
        return [
            'donationsCount'     => Donation::where('campaign_id', $campaign->id)->count(),
            'cashSum'            => $cashSum,
            'inKindSum'          => $inKindSum,
            'beneficiariesCount' => Beneficiary::where('campaign_id', $campaign->id)->count(),
            'expensesCount'      => Expense::where('campaign_id', $campaign->id)->count(),
            'latestDonations'    => Donation::where('campaign_id', $campaign->id)->orderByDesc('id')->limit(5)->get(),
            'latestExpenses'     => Expense::where('campaign_id', $campaign->id)->orderByDesc('id')->limit(5)->get(),
            'latestBeneficiaries'=> Beneficiary::where('campaign_id', $campaign->id)->orderByDesc('id')->limit(5)->get(),
            'expensesTotal'      => $expensesTotal,
            'donationsTotal'     => $donationsTotal,
            'netBalance'         => $donationsTotal - $expensesTotal,
            'cashPct'            => $donationsTotal > 0 ? (int) round(($cashSum / $donationsTotal) * 100) : 0,
        ];
    }

    public function createCampaign(array $data): mixed
    {
        $executor = fn() => $this->campaignRepository->create($data);

        return ChangeRequestService::handleRequest(
            Campaign::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateCampaign(Campaign $campaign, array $data): mixed
    {
        $executor = function () use ($campaign, $data) {
            $this->campaignRepository->update($campaign, $data);
            return $campaign;
        };

        return ChangeRequestService::handleRequest(
            Campaign::class,
            $campaign->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function setManager(Campaign $campaign, array $data, ?UploadedFile $photo): mixed
    {
        if ($photo) {
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/managers'), $filename);
            $data['manager_photo_url'] = '/uploads/managers/' . $filename;
        }

        $executor = function() use ($campaign, $data) {
            $this->campaignRepository->update($campaign, $data);
            return $campaign;
        };

        return ChangeRequestService::handleRequest(
            Campaign::class,
            $campaign->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteCampaign(Campaign $campaign): mixed
    {
        $executor = function () use ($campaign) {
            return $this->campaignRepository->delete($campaign);
        };

        return ChangeRequestService::handleRequest(
            Campaign::class,
            $campaign->id,
            'delete',
            ['name' => $campaign->name],
            $executor,
            true
        );
    }

    public function attachVolunteer(Campaign $campaign, array $data): void
    {
        $campaign->volunteers()->syncWithoutDetaching([
            $data['user_id'] => [
                'role'       => $data['role'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'hours'      => $data['hours'] ?? 0
            ]
        ]);
    }

    public function detachVolunteer(Campaign $campaign, int $userId): void
    {
        $campaign->volunteers()->detach($userId);
    }

    public function storeMonthlyVolunteer(Campaign $campaign, array $data): void
    {
        $exists = $campaign->monthlyVolunteers()
            ->where('user_id', $data['user_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if (!$exists) {
            $campaign->monthlyVolunteers()->create($data);
        }
    }

    public function deleteMonthlyVolunteer(int $id): void
    {
        CampaignMonthlyVolunteer::where('id', $id)->delete();
    }

    public function storeDailyMenu(Campaign $campaign, array $data): CampaignDailyMenu
    {
        return $campaign->dailyMenus()->create($data);
    }

    public function deleteDailyMenu(int $id): void
    {
        CampaignDailyMenu::where('id', $id)->delete();
    }
}
