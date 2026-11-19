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
    public const OFFICE_LAT = 31.1112969;
    public const OFFICE_LNG = 30.9489307;
    public const ALLOWED_RADIUS_METERS = 250;

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

    public function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    public function checkInWithLocation(int $userId, float $lat, float $lng): array
    {
        $distance = $this->calculateDistanceMeters($lat, $lng, self::OFFICE_LAT, self::OFFICE_LNG);
        $existing = $this->attendanceRepository->findTodayRecord($userId);

        if ($existing) {
            return [
                'success' => true,
                'already_checked_in' => true,
                'time' => $existing->check_in_at,
                'distance' => $distance,
                'message' => 'تم تسجيل حضورك اليوم مسبقاً الساعة ' . $existing->check_in_at,
            ];
        }

        if ($distance > self::ALLOWED_RADIUS_METERS) {
            return [
                'success' => false,
                'outside_geofence' => true,
                'distance' => $distance,
                'max_distance' => self::ALLOWED_RADIUS_METERS,
                'message' => "أنت خارج نطاق مقر المؤسسة (المسافة الحالية: {$distance} متر — المسموح حتى " . self::ALLOWED_RADIUS_METERS . ' متر).',
            ];
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => $cairoNow->toDateString(),
            'check_in_at' => $cairoNow->format('H:i'),
            'notes'       => "تسجيل حضور تلقائي من مقر المؤسسة (المسافة: {$distance} متر)",
        ]);

        return [
            'success' => true,
            'checked_in' => true,
            'time' => $cairoNow->format('H:i'),
            'distance' => $distance,
            'message' => 'تم تسجيل حضورك تلقائياً من مقر المؤسسة الساعة ' . $cairoNow->format('h:i A') . " (المسافة: {$distance} متر)",
        ];
    }

    public function ensureAutoCheckIn(int $userId): EmployeeAttendance
    {
        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if ($existing) {
            return $existing;
        }

        $cairoNow = now('Africa/Cairo');

        return $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => $cairoNow->toDateString(),
            'check_in_at' => $cairoNow->format('H:i'),
            'notes'       => 'تسجيل حضور تلقائي عند الدخول للنظام',
        ]);
    }

    public function checkIn(int $userId): string
    {
        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if ($existing) {
            throw new \Exception('لقد قمت بتسجيل الدخول لهذا اليوم مسبقاً في الساعة ' . $existing->check_in_at);
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => $cairoNow->toDateString(),
            'check_in_at' => $cairoNow->format('H:i'),
        ]);

        return 'تم تسجيل الدخول بنجاح: ' . $cairoNow->format('h:i A');
    }

    public function checkOut(int $userId): string
    {
        $record = $this->attendanceRepository->findTodayRecord($userId);
        if (!$record) {
            $record = $this->ensureAutoCheckIn($userId);
        }

        if (!is_null($record->check_out_at)) {
            throw new \Exception('لقد قمت بتسجيل الخروج بالفعل في الساعة ' . $record->check_out_at);
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->update($record, [
            'check_out_at' => $cairoNow->format('H:i'),
        ]);

        return 'تم تسجيل الانصراف بنجاح: ' . $cairoNow->format('h:i A');
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
