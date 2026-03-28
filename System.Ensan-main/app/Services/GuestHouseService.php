<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GuestHouse;
use App\Models\GuestHouseMonthlyVolunteer;
use App\Models\ChangeRequest;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\User;
use App\Repositories\GuestHouseRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

final readonly class GuestHouseService
{
    public function __construct(
        private GuestHouseRepository $guestHouseRepository
    ) {}

    public function getFilteredHouses(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $houses = $this->guestHouseRepository->paginateFiltered($filters, $perPage);

        $houses->each(function(GuestHouse $house) {
            $house->pendingRequest = ChangeRequest::where('model_type', GuestHouse::class)
                ->where('model_id', $house->id)
                ->where('status', 'pending')
                ->first();
        });

        return $houses;
    }

    public function findHouseById(int $id): ?GuestHouse
    {
        return $this->guestHouseRepository->findById($id);
    }

    public function getHouseStats(GuestHouse $house): array
    {
        $cashSum      = (float) Donation::where('guest_house_id', $house->id)->where('type', 'cash')->sum('amount');
        $inKindSum    = (float) Donation::where('guest_house_id', $house->id)->where('type', 'in_kind')->sum('estimated_value');
        $expensesTotal = (float) Expense::where('guest_house_id', $house->id)->sum('amount');
        
        $donationsTotal = $cashSum + $inKindSum;
        
        return [
            'donationsCount'     => Donation::where('guest_house_id', $house->id)->count(),
            'cashSum'            => $cashSum,
            'inKindSum'          => $inKindSum,
            'beneficiariesCount' => Beneficiary::where('guest_house_id', $house->id)->count(),
            'expensesCount'      => Expense::where('guest_house_id', $house->id)->count(),
            'latestDonations'    => Donation::where('guest_house_id', $house->id)->orderByDesc('id')->limit(5)->get(),
            'latestExpenses'     => Expense::where('guest_house_id', $house->id)->orderByDesc('id')->limit(5)->get(),
            'latestBeneficiaries'=> Beneficiary::where('guest_house_id', $house->id)->orderByDesc('id')->limit(5)->get(),
            'expensesTotal'      => $expensesTotal,
            'donationsTotal'     => $donationsTotal,
            'netBalance'         => $donationsTotal - $expensesTotal,
            'cashPct'            => $donationsTotal > 0 ? (int) round(($cashSum / $donationsTotal) * 100) : 0,
        ];
    }

    public function createHouse(array $data): mixed
    {
        $data['status'] = $data['status'] ?? 'active';

        $executor = fn() => $this->guestHouseRepository->create($data);

        return ChangeRequestService::handleRequest(
            GuestHouse::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateHouse(GuestHouse $house, array $data): mixed
    {
        $executor = function () use ($house, $data) {
            $this->guestHouseRepository->update($house, $data);
            return $house;
        };

        return ChangeRequestService::handleRequest(
            GuestHouse::class,
            $house->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function setManager(GuestHouse $house, array $data, ?UploadedFile $photo): mixed
    {
        if ($photo) {
            $filename = time() . '_gh_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/managers'), $filename);
            $data['manager_photo_url'] = '/uploads/managers/' . $filename;
        }

        $executor = function() use ($house, $data) {
            $this->guestHouseRepository->update($house, $data);
            return $house;
        };

        return ChangeRequestService::handleRequest(
            GuestHouse::class,
            $house->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteHouse(GuestHouse $house): mixed
    {
        $executor = function () use ($house) {
            return $this->guestHouseRepository->delete($house);
        };

        return ChangeRequestService::handleRequest(
            GuestHouse::class,
            $house->id,
            'delete',
            ['name' => $house->name],
            $executor,
            true
        );
    }

    public function attachVolunteer(GuestHouse $house, array $data): void
    {
        $house->volunteers()->syncWithoutDetaching([
            $data['user_id'] => [
                'role'       => $data['role'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'hours'      => $data['hours'] ?? 0
            ]
        ]);
    }

    public function detachVolunteer(GuestHouse $house, int $userId): void
    {
        $house->volunteers()->detach($userId);
    }

    public function storeMonthlyVolunteer(GuestHouse $house, array $data): void
    {
        $exists = $house->monthlyVolunteers()
            ->where('user_id', $data['user_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if (!$exists) {
            $house->monthlyVolunteers()->create($data);
        }
    }

    public function deleteMonthlyVolunteer(int $id): void
    {
        GuestHouseMonthlyVolunteer::where('id', $id)->delete();
    }
}
