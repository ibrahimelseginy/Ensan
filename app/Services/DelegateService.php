<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Delegate;
use App\Models\DelegateTrip;
use App\Models\ChangeRequest;
use App\Models\User;
use App\Repositories\DelegateRepository;
use App\Services\ChangeRequestService;
use App\Services\LogisticsAccountingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class DelegateService
{
    public function __construct(
        private DelegateRepository $delegateRepository
    ) {}

    public function getFilteredDelegates(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $delegates = $this->delegateRepository->paginateFiltered($filters, $perPage);

        $delegates->each(function(Delegate $delegate) {
            $delegate->pendingRequest = ChangeRequest::where('model_type', Delegate::class)
                ->where('model_id', $delegate->id)
                ->where('status', 'pending')
                ->first();
        });

        return $delegates;
    }

    public function findDelegateById(int $id): ?Delegate
    {
        return $this->delegateRepository->findById($id);
    }

    public function createDelegate(array $data, ?UploadedFile $photo): mixed
    {
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            if ($user) {
                $data['name']  = $user->name;
                $data['phone'] = $user->phone;
                $data['email'] = $user->email;
                if ($user->profile_photo_path) {
                    $data['profile_photo_path'] = $user->profile_photo_path;
                }
            }
        }

        if ($photo) {
            $data['profile_photo_path'] = $photo->store('profile-photos', 'public');
        }

        $executor = fn() => $this->delegateRepository->create($data);

        return ChangeRequestService::handleRequest(
            Delegate::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateDelegate(Delegate $delegate, array $data, ?UploadedFile $photo): mixed
    {
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            if ($user) {
                $data['name']  = $user->name;
                $data['phone'] = $user->phone;
                $data['email'] = $user->email;
            }
        }

        if ($photo) {
            if ($delegate->profile_photo_path) {
                Storage::disk('public')->delete($delegate->profile_photo_path);
            }
            $data['profile_photo_path'] = $photo->store('profile-photos', 'public');
        }

        $executor = function () use ($delegate, $data) {
            $this->delegateRepository->update($delegate, $data);
            return $delegate;
        };

        return ChangeRequestService::handleRequest(
            Delegate::class,
            $delegate->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteDelegate(Delegate $delegate): mixed
    {
        $executor = function () use ($delegate) {
            return $this->delegateRepository->delete($delegate);
        };

        return ChangeRequestService::handleRequest(
            Delegate::class,
            $delegate->id,
            'delete',
            [],
            $executor,
            true
        );
    }

    public function bulkUpdate(array $ids, string $action): void
    {
        $targets = Delegate::whereIn('id', $ids)->get();
        if ($action === 'delete') {
            foreach ($targets as $d) { $d->delete(); }
        } else {
            foreach ($targets as $d) { $d->update(['active' => $action === 'activate']); }
        }
    }

    public function exportToCsv(array $filters): StreamedResponse
    {
        $filters['per_page'] = 10000;
        $rows = $this->delegateRepository->paginateFiltered($filters, 10000);

        return response()->stream(function () use ($rows) {
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'الاسم', 'الهاتف', 'نشط', 'خط السير', 'عدد التبرعات']);
            foreach ($rows as $d) {
                fputcsv($out, [$d->id, $d->name, $d->phone, $d->active ? 'نعم' : 'لا', optional($d->route)->name, $d->donations_count]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="delegates.csv"'
        ]);
    }

    public function storeTrip(Delegate $delegate, array $data, bool $createJournal = false): mixed
    {
        $executor = function () use ($delegate, $data, $createJournal) {
            $trip = $delegate->trips()->create($data);
            if ($createJournal) {
                try {
                    app(LogisticsAccountingService::class)->createJournalEntry($trip);
                } catch (\Exception $e) {}
            }
            return $trip;
        };

        return ChangeRequestService::handleRequest(
            DelegateTrip::class,
            null,
            'create',
            array_merge($data, [
                'delegate_id'   => $delegate->id,
                'delegate_name' => $delegate->name
            ]),
            $executor,
            true
        );
    }

    public function updateTripStatus(Delegate $delegate, DelegateTrip $trip, string $status): mixed
    {
        $data = ['status' => $status];
        
        $executor = function () use ($trip, $data) {
            $trip->update($data);
            return $trip;
        };

        return ChangeRequestService::handleRequest(
            DelegateTrip::class,
            $trip->id,
            'update',
            array_merge($data, [
                'delegate_name'    => $delegate->name,
                'trip_date'        => (string)$trip->date?->format('Y-m-d'),
                'trip_description' => $trip->description,
                'trip_cost'        => $trip->cost
            ]),
            $executor,
            true
        );
    }

    public function deleteTrip(Delegate $delegate, DelegateTrip $trip): mixed
    {
        $executor = function () use ($trip) {
            return $trip->delete();
        };

        return ChangeRequestService::handleRequest(
            DelegateTrip::class,
            $trip->id,
            'delete',
            [
                'delegate_id'      => $delegate->id,
                'delegate_name'    => $delegate->name,
                'trip_date'        => (string)$trip->date?->format('Y-m-d'),
                'trip_description' => $trip->description,
                'trip_cost'        => $trip->cost
            ],
            $executor,
            true
        );
    }
}
