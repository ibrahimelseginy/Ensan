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
    // 1. المقر الرئيسي لمؤسسة إنسان
    public const OFFICE_LAT = 31.1112969;
    public const OFFICE_LNG = 30.9489307;
    public const ALLOWED_RADIUS_METERS = 10;

    // 2. دار ضيافة إنســـــــان - كفر الشيخ (خاصة بموظفي الدار)
    public const KAFR_GUEST_HOUSE_LAT = 31.1061705;
    public const KAFR_GUEST_HOUSE_LNG = 30.9378786;
    public const KAFR_GUEST_HOUSE_RADIUS_METERS = 10;

    // 3. دار ضيافة إنســـــــان - طنطا (خاصة بموظفي دار طنطا)
    public const TANTA_GUEST_HOUSE_LAT = 30.8038626;
    public const TANTA_GUEST_HOUSE_LNG = 30.9969359;
    public const TANTA_GUEST_HOUSE_RADIUS_METERS = 10;

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
        $existing = $this->attendanceRepository->findTodayRecord($userId);

        if ($existing) {
            return [
                'success' => true,
                'already_checked_in' => true,
                'time' => $existing->check_in_at,
                'message' => 'تم تسجيل حضورك اليوم مسبقاً الساعة ' . $existing->check_in_at,
            ];
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'المستخدم غير موجود.',
            ];
        }

        $distToHq    = $this->calculateDistanceMeters($lat, $lng, self::OFFICE_LAT, self::OFFICE_LNG);
        $distToKafr  = $this->calculateDistanceMeters($lat, $lng, self::KAFR_GUEST_HOUSE_LAT, self::KAFR_GUEST_HOUSE_LNG);
        $distToTanta = $this->calculateDistanceMeters($lat, $lng, self::TANTA_GUEST_HOUSE_LAT, self::TANTA_GUEST_HOUSE_LNG);

        // هل الموظف من طاقم دار طنطا؟
        $isTantaStaff = (int) $user->guest_house_id === 1
            || str_contains((string) $user->job_title, 'طنطا')
            || str_contains((string) $user->job_title, 'الغربيه')
            || str_contains((string) $user->job_title, 'الغربية')
            || str_contains((string) $user->department, 'طنطا')
            || str_contains((string) $user->department, 'الغربية');

        // هل الموظف من طاقم دار كفر الشيخ؟
        $isKafrStaff = (int) $user->guest_house_id === 2
            || (int) $user->id === 74 // مريم بدوي مدير دار الضيافة كفر الشيخ
            || str_contains((string) $user->job_title, 'كفر الشيخ')
            || (str_contains((string) $user->job_title, 'مدير دار الضيافة') && !$isTantaStaff)
            || (str_contains((string) $user->department, 'دار الضيافة') && !$isTantaStaff);

        $isAdminOrManager = $user->hasRole(['admin', 'manager', 'super_admin'])
            || in_array((int) $user->id, [2, 8, 50], true);

        // النطاق الأول: دار ضيافة كفر الشيخ
        if ($distToKafr <= self::KAFR_GUEST_HOUSE_RADIUS_METERS) {
            if (!$isKafrStaff && !$isAdminOrManager) {
                $approvedLoc = $isTantaStaff ? 'دار ضيافة طنطا' : 'مقر المؤسسة الرئيسي';
                return [
                    'success' => false,
                    'outside_geofence' => true,
                    'distance' => $distToKafr,
                    'max_distance' => self::KAFR_GUEST_HOUSE_RADIUS_METERS,
                    'location' => 'دار ضيافة كفر الشيخ',
                    'message' => "أنت داخل نطاق دار ضيافة كفر الشيخ، ولكن تسجيل الحضور هنا مخصص لموظفي دار كفر الشيخ فقط. موقع حضورك المعتمد هو {$approvedLoc}.",
                ];
            }

            $cairoNow = now('Africa/Cairo');
            $this->attendanceRepository->create([
                'user_id'     => $userId,
                'date'        => $cairoNow->toDateString(),
                'check_in_at' => $cairoNow->format('H:i'),
                'notes'       => "تسجيل حضور تلقائي من دار ضيافة كفر الشيخ (المسافة: {$distToKafr} متر)",
            ]);

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToKafr,
                'location' => 'دار ضيافة كفر الشيخ',
                'message' => 'تم تسجيل حضورك بنجاح من مقر دار ضيافة كفر الشيخ الساعة ' . $cairoNow->format('h:i A') . " (المسافة: {$distToKafr} متر)",
            ];
        }

        // النطاق الثاني: دار ضيافة طنطا
        if ($distToTanta <= self::TANTA_GUEST_HOUSE_RADIUS_METERS) {
            if (!$isTantaStaff && !$isAdminOrManager) {
                $approvedLoc = $isKafrStaff ? 'دار ضيافة كفر الشيخ' : 'مقر المؤسسة الرئيسي';
                return [
                    'success' => false,
                    'outside_geofence' => true,
                    'distance' => $distToTanta,
                    'max_distance' => self::TANTA_GUEST_HOUSE_RADIUS_METERS,
                    'location' => 'دار ضيافة طنطا',
                    'message' => "أنت داخل نطاق دار ضيافة طنطا، ولكن تسجيل الحضور هنا مخصص لموظفي دار طنطا فقط. موقع حضورك المعتمد هو {$approvedLoc}.",
                ];
            }

            $cairoNow = now('Africa/Cairo');
            $this->attendanceRepository->create([
                'user_id'     => $userId,
                'date'        => $cairoNow->toDateString(),
                'check_in_at' => $cairoNow->format('H:i'),
                'notes'       => "تسجيل حضور تلقائي من دار ضيافة طنطا (المسافة: {$distToTanta} متر)",
            ]);

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToTanta,
                'location' => 'دار ضيافة طنطا',
                'message' => 'تم تسجيل حضورك بنجاح من مقر دار ضيافة طنطا الساعة ' . $cairoNow->format('h:i A') . " (المسافة: {$distToTanta} متر)",
            ];
        }

        // النطاق الثالث: المقر الرئيسي لمؤسسة إنسان
        if ($distToHq <= self::ALLOWED_RADIUS_METERS) {
            $cairoNow = now('Africa/Cairo');
            $locationLabel = ($isKafrStaff || $isTantaStaff) ? 'مقر المؤسسة الرئيسي (زيارة)' : 'مقر المؤسسة الرئيسي';
            $this->attendanceRepository->create([
                'user_id'     => $userId,
                'date'        => $cairoNow->toDateString(),
                'check_in_at' => $cairoNow->format('H:i'),
                'notes'       => "تسجيل حضور تلقائي من {$locationLabel} (المسافة: {$distToHq} متر)",
            ]);

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToHq,
                'location' => 'مقر المؤسسة الرئيسي',
                'message' => 'تم تسجيل حضورك بنجاح من مقر المؤسسة الرئيسي الساعة ' . $cairoNow->format('h:i A') . " (المسافة: {$distToHq} متر)",
            ];
        }

        // خارج كافة المقرات
        if ($isTantaStaff) {
            return [
                'success' => false,
                'outside_geofence' => true,
                'distance' => $distToTanta,
                'max_distance' => self::TANTA_GUEST_HOUSE_RADIUS_METERS,
                'location' => 'دار ضيافة طنطا',
                'message' => "أنت خارج نطاق مقر دار ضيافة طنطا (المسافة الحالية: {$distToTanta} متر — المسموح حتى " . self::TANTA_GUEST_HOUSE_RADIUS_METERS . ' متر).',
            ];
        }

        if ($isKafrStaff) {
            return [
                'success' => false,
                'outside_geofence' => true,
                'distance' => $distToKafr,
                'max_distance' => self::KAFR_GUEST_HOUSE_RADIUS_METERS,
                'location' => 'دار ضيافة كفر الشيخ',
                'message' => "أنت خارج نطاق مقر دار ضيافة كفر الشيخ (المسافة الحالية: {$distToKafr} متر — المسموح حتى " . self::KAFR_GUEST_HOUSE_RADIUS_METERS . ' متر).',
            ];
        }

        return [
            'success' => false,
            'outside_geofence' => true,
            'distance' => $distToHq,
            'max_distance' => self::ALLOWED_RADIUS_METERS,
            'location' => 'مقر المؤسسة الرئيسي',
            'message' => "أنت خارج نطاق مقر المؤسسة (المسافة الحالية: {$distToHq} متر — المسموح حتى " . self::ALLOWED_RADIUS_METERS . ' متر).',
        ];
    }

    public function checkInAdminOverride(int $userId): array
    {
        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if ($existing) {
            return [
                'success' => true,
                'already_checked_in' => true,
                'time' => $existing->check_in_at,
                'message' => 'تم تسجيل حضورك اليوم مسبقاً الساعة ' . $existing->check_in_at,
            ];
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'المستخدم غير موجود.'];
        }

        $isAdminOrManager = $user->hasRole(['admin', 'manager', 'super_admin'])
            || in_array((int) $user->id, [2, 8, 50], true);

        if (!$isAdminOrManager) {
            return ['success' => false, 'message' => 'تسجيل الحضور بدون GPS مقتصر على الإدارة فقط.'];
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => $cairoNow->toDateString(),
            'check_in_at' => $cairoNow->format('H:i'),
            'notes'       => 'تسجيل حضور إداري معتمد (تجاوز GPS لجهاز الكمبيوتر)',
        ]);

        return [
            'success' => true,
            'checked_in' => true,
            'time' => $cairoNow->format('H:i'),
            'message' => 'تم تسجيل حضورك الإداري بنجاح الساعة ' . $cairoNow->format('h:i A'),
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
