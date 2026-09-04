<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeAttendance;
use App\Models\ChangeRequest;
use App\Repositories\EmployeeAttendanceRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class EmployeeAttendanceService
{
    public function __construct(
        private EmployeeAttendanceRepository $attendanceRepository
    ) {}

    public function getFilteredAttendance(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->attendanceRepository->paginateFiltered($filters, $perPage);
    }

    public function findAttendanceById(int $id): ?EmployeeAttendance
    {
        return $this->attendanceRepository->findById($id);
    }

    public function findTodayRecord(int $userId): ?EmployeeAttendance
    {
        return $this->attendanceRepository->findTodayRecord($userId);
    }

    public function checkIn(int $userId): string
    {
        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if ($existing) {
            throw new \Exception('لقد قمت بتسجيل الدخول لهذا اليوم مسبقاً في الساعة ' . $existing->check_in_at);
        }

        $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => now()->toDateString(),
            'check_in_at' => now()->format('H:i'),
        ]);

        return 'تم تسجيل الدخول بنجاح: ' . now()->format('h:i A');
    }

    public function checkOut(int $userId): string
    {
        $record = $this->attendanceRepository->findTodayRecord($userId);
        if (!$record) {
            throw new \Exception('يجب عليك تسجيل الدخول (Check In) أولاً قبل تسجيل الخروج.');
        }

        if (!is_null($record->check_out_at)) {
            throw new \Exception('لقد قمت بتسجيل الخروج بالفعل في الساعة ' . $record->check_out_at);
        }

        $this->attendanceRepository->update($record, [
            'check_out_at' => now()->format('H:i'),
        ]);

        return 'تم تسجيل الخروج بنجاح: ' . now()->format('h:i A');
    }

    public function createAttendance(array $data): EmployeeAttendance
    {
        return $this->attendanceRepository->create($data);
    }

    public function updateAttendance(EmployeeAttendance $attendance, array $data): mixed
    {
        $executor = function () use ($attendance, $data) {
            $this->attendanceRepository->update($attendance, $data);
            return $attendance;
        };

        return ChangeRequestService::handleRequest(
            EmployeeAttendance::class,
            $attendance->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteAttendance(EmployeeAttendance $attendance): mixed
    {
        $executor = function () use ($attendance) {
            return $this->attendanceRepository->delete($attendance);
        };

        return ChangeRequestService::handleRequest(
            EmployeeAttendance::class,
            $attendance->id,
            'delete',
            [
                'note'          => 'حذف سجل حضور موظف',
                'employee_name' => $attendance->user->name ?? '—',
                'date'          => $attendance->date ? $attendance->date->format('Y-m-d') : '—'
            ],
            $executor,
            true
        );
    }

    public function bulkDelete(array $ids): int
    {
        $count = 0;
        foreach ($ids as $id) {
            $record = $this->attendanceRepository->findById((int)$id);
            if (!$record) continue;

            $this->deleteAttendance($record);
            $count++;
        }
        return $count;
    }
}
