<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\VolunteerAttendance;
use App\Models\ChangeRequest;
use App\Repositories\VolunteerAttendanceRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class VolunteerAttendanceService
{
    public function __construct(
        private VolunteerAttendanceRepository $attendanceRepository
    ) {}

    public function getFilteredRecords(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->attendanceRepository->paginateFiltered($filters, $perPage);
    }

    public function findRecordById(int $id): ?VolunteerAttendance
    {
        return $this->attendanceRepository->findById($id);
    }

    public function createRecord(array $data): VolunteerAttendance
    {
        return $this->attendanceRepository->create($data);
    }

    public function updateRecord(VolunteerAttendance $attendance, array $data): mixed
    {
        $executor = function () use ($attendance, $data) {
            $this->attendanceRepository->update($attendance, $data);
            return $attendance;
        };

        return ChangeRequestService::handleRequest(
            VolunteerAttendance::class,
            $attendance->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteRecord(VolunteerAttendance $attendance): mixed
    {
        $executor = function () use ($attendance) {
            return $this->attendanceRepository->delete($attendance);
        };

        return ChangeRequestService::handleRequest(
            VolunteerAttendance::class,
            $attendance->id,
            'delete',
            [
                'note'           => 'حذف سجل حضور متطوع',
                'volunteer_name' => $attendance->user->name ?? '—',
                'date'           => $attendance->date ? $attendance->date->format('Y-m-d') : '—'
            ],
            $executor,
            true
        );
    }
}
