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

    public function saveAttendancePhoto(mixed $photoData, string $prefix = 'attendance'): ?string
    {
        if (empty($photoData)) {
            return null;
        }

        $dir = public_path('uploads/attendance_photos');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }

        $filename = $prefix . '_' . date('Ymd_His') . '_' . \Illuminate\Support\Str::random(8) . '.jpg';
        $targetPath = $dir . '/' . $filename;

        if ($photoData instanceof \Illuminate\Http\UploadedFile) {
            $photoData->move($dir, $filename);
            return 'uploads/attendance_photos/' . $filename;
        }

        if (is_string($photoData)) {
            if (str_starts_with($photoData, 'data:image')) {
                $parts = explode(',', $photoData, 2);
                if (count($parts) === 2) {
                    $decoded = base64_decode($parts[1]);
                    if ($decoded !== false) {
                        file_put_contents($targetPath, $decoded);
                        return 'uploads/attendance_photos/' . $filename;
                    }
                }
            } elseif (file_exists(public_path($photoData))) {
                return $photoData;
            }
        }

        return null;
    }

    public function checkInWithLocation(int $userId, float $lat, float $lng, mixed $photo = null): array
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

        if (empty($photo)) {
            return [
                'success' => false,
                'photo_required' => true,
                'message' => 'التقاط صورة شخصية في مقر العمل إلزامي لتسجيل الحضور.',
            ];
        }

        $photoPath = $this->saveAttendancePhoto($photo, 'checkin');
        if (!$photoPath) {
            return [
                'success' => false,
                'photo_required' => true,
                'message' => 'تعذر حفظ صورة الحضور. يرجى إعادة التقاط الصورة.',
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
                'user_id'        => $userId,
                'date'           => $cairoNow->toDateString(),
                'check_in_at'    => $cairoNow->format('H:i'),
                'check_in_photo' => $photoPath,
                'check_in_lat'   => $lat,
                'check_in_lng'   => $lng,
                'notes'          => "تسجيل حضور موثق بالصورة من دار ضيافة كفر الشيخ (المسافة: {$distToKafr} متر)",
            ]);

            $this->notifyDirectorAndAdmins(
                "تسجيل حضور: {$user->name}",
                "سجل الموظف {$user->name} حضوره الساعة {$cairoNow->format('h:i A')} موثقاً بصورة سيلفي من دار ضيافة كفر الشيخ (نطاق {$distToKafr}م)."
            );

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToKafr,
                'location' => 'دار ضيافة كفر الشيخ',
                'photo' => $photoPath,
                'message' => 'تم تسجيل حضورك وتوثيق صورتك وموقعك بنجاح من دار ضيافة كفر الشيخ الساعة ' . $cairoNow->format('h:i A'),
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
                'user_id'        => $userId,
                'date'           => $cairoNow->toDateString(),
                'check_in_at'    => $cairoNow->format('H:i'),
                'check_in_photo' => $photoPath,
                'check_in_lat'   => $lat,
                'check_in_lng'   => $lng,
                'notes'          => "تسجيل حضور موثق بالصورة من دار ضيافة طنطا (المسافة: {$distToTanta} متر)",
            ]);

            $this->notifyDirectorAndAdmins(
                "تسجيل حضور: {$user->name}",
                "سجل الموظف {$user->name} حضوره الساعة {$cairoNow->format('h:i A')} موثقاً بصورة سيلفي من دار ضيافة طنطا (نطاق {$distToTanta}م)."
            );

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToTanta,
                'location' => 'دار ضيافة طنطا',
                'photo' => $photoPath,
                'message' => 'تم تسجيل حضورك وتوثيق صورتك وموقعك بنجاح من دار ضيافة طنطا الساعة ' . $cairoNow->format('h:i A'),
            ];
        }

        // النطاق الثالث: المقر الرئيسي لمؤسسة إنسان
        if ($distToHq <= self::ALLOWED_RADIUS_METERS) {
            $cairoNow = now('Africa/Cairo');
            $locationLabel = ($isKafrStaff || $isTantaStaff) ? 'مقر المؤسسة الرئيسي (زيارة)' : 'مقر المؤسسة الرئيسي';
            $this->attendanceRepository->create([
                'user_id'        => $userId,
                'date'           => $cairoNow->toDateString(),
                'check_in_at'    => $cairoNow->format('H:i'),
                'check_in_photo' => $photoPath,
                'check_in_lat'   => $lat,
                'check_in_lng'   => $lng,
                'notes'          => "تسجيل حضور موثق بالصورة من {$locationLabel} (المسافة: {$distToHq} متر)",
            ]);

            $this->notifyDirectorAndAdmins(
                "تسجيل حضور: {$user->name}",
                "سجل الموظف {$user->name} حضوره الساعة {$cairoNow->format('h:i A')} موثقاً بصورة سيلفي من {$locationLabel} (نطاق {$distToHq}م)."
            );

            return [
                'success' => true,
                'checked_in' => true,
                'time' => $cairoNow->format('H:i'),
                'distance' => $distToHq,
                'location' => 'مقر المؤسسة الرئيسي',
                'photo' => $photoPath,
                'message' => 'تم تسجيل حضورك وتوثيق صورتك وموقعك بنجاح من مقر المؤسسة الرئيسي الساعة ' . $cairoNow->format('h:i A'),
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

    public function checkInWithAuthorizedDevice(int $userId, string $deviceToken, ?string $ip = null, ?string $userAgent = null): array
    {
        $device = \App\Models\AttendanceDevice::where('device_token', $deviceToken)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return [
                'success' => false,
                'device_authorized' => false,
                'message' => 'هذا الجهاز غير معتمد حالياً لتسجيل الحضور.',
            ];
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'المستخدم غير موجود.'];
        }

        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if ($existing) {
            $device->update([
                'last_used_at' => now(),
                'ip_address'   => $ip ?: $device->ip_address,
            ]);
            return [
                'success' => true,
                'already_checked_in' => true,
                'time' => $existing->check_in_at,
                'device' => $device->device_name,
                'location' => $device->location_name,
                'message' => 'تم تسجيل حضورك اليوم مسبقاً الساعة ' . $existing->check_in_at,
            ];
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->create([
            'user_id'     => $userId,
            'date'        => $cairoNow->toDateString(),
            'check_in_at' => $cairoNow->format('H:i'),
            'notes'       => "تسجيل حضور تلقائي من جهاز المقر المعتمد: {$device->device_name} ({$device->location_name})",
        ]);

        $this->notifyDirectorAndAdmins(
            "تسجيل حضور (جهاز معتمد): {$user->name}",
            "سجل الموظف {$user->name} حضوره الساعة {$cairoNow->format('h:i A')} من جهاز المقر المعتمد ({$device->device_name} - {$device->location_name})."
        );

        $device->update([
            'last_used_at' => now(),
            'ip_address'   => $ip ?: $device->ip_address,
            'user_agent'   => $userAgent ?: $device->user_agent,
        ]);

        return [
            'success' => true,
            'checked_in' => true,
            'time' => $cairoNow->format('H:i'),
            'device' => $device->device_name,
            'location' => $device->location_name,
            'message' => "تم تسجيل حضورك بنجاح من جهاز المقر المعتمد ({$device->device_name} - {$device->location_name}) الساعة " . $cairoNow->format('h:i A'),
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

        $this->notifyDirectorAndAdmins(
            "تسجيل حضور إداري: {$user->name}",
            "سجل الموظف {$user->name} حضوره إدارياً الساعة " . $cairoNow->format('h:i A') . "."
        );

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

    public function checkOutWithLocation(int $userId, float $lat, float $lng, mixed $photo = null): array
    {
        $existing = $this->attendanceRepository->findTodayRecord($userId);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'لم يتم تسجيل حضورك اليوم حتى الآن.',
            ];
        }

        if (!empty($existing->check_out_at)) {
            return [
                'success' => false,
                'already_checked_out' => true,
                'time' => $existing->check_out_at,
                'message' => 'تم تسجيل انصرافك اليوم مسبقاً الساعة ' . $existing->check_out_at,
            ];
        }

        if (empty($photo)) {
            return [
                'success' => false,
                'photo_required' => true,
                'message' => 'التقاط صورة سيلفي في مقر العمل إلزامي لتسجيل الانصراف.',
            ];
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'المستخدم غير موجود.'];
        }

        $distToHq    = $this->calculateDistanceMeters($lat, $lng, self::OFFICE_LAT, self::OFFICE_LNG);
        $distToKafr  = $this->calculateDistanceMeters($lat, $lng, self::KAFR_GUEST_HOUSE_LAT, self::KAFR_GUEST_HOUSE_LNG);
        $distToTanta = $this->calculateDistanceMeters($lat, $lng, self::TANTA_GUEST_HOUSE_LAT, self::TANTA_GUEST_HOUSE_LNG);

        $isTantaStaff = (int) $user->guest_house_id === 1
            || str_contains((string) $user->job_title, 'طنطا')
            || str_contains((string) $user->job_title, 'الغربيه')
            || str_contains((string) $user->job_title, 'الغربية')
            || str_contains((string) $user->department, 'طنطا')
            || str_contains((string) $user->department, 'الغربية');

        $isKafrStaff = (int) $user->guest_house_id === 2
            || (int) $user->id === 74
            || str_contains((string) $user->job_title, 'كفر الشيخ')
            || (str_contains((string) $user->job_title, 'مدير دار الضيافة') && !$isTantaStaff)
            || (str_contains((string) $user->department, 'دار الضيافة') && !$isTantaStaff);

        $isAdminOrManager = $user->hasRole(['admin', 'manager', 'super_admin'])
            || in_array((int) $user->id, [2, 8, 50], true);

        $locationLabel = null;
        $matchedDist = 0;

        if ($distToKafr <= self::KAFR_GUEST_HOUSE_RADIUS_METERS) {
            $locationLabel = 'دار ضيافة كفر الشيخ';
            $matchedDist = $distToKafr;
        } elseif ($distToTanta <= self::TANTA_GUEST_HOUSE_RADIUS_METERS) {
            $locationLabel = 'دار ضيافة طنطا';
            $matchedDist = $distToTanta;
        } elseif ($distToHq <= self::ALLOWED_RADIUS_METERS) {
            $locationLabel = 'مقر المؤسسة الرئيسي';
            $matchedDist = $distToHq;
        } else {
            $targetDist = $isTantaStaff ? $distToTanta : ($isKafrStaff ? $distToKafr : $distToHq);
            $targetName = $isTantaStaff ? 'دار ضيافة طنطا' : ($isKafrStaff ? 'دار ضيافة كفر الشيخ' : 'مقر المؤسسة الرئيسي');
            return [
                'success' => false,
                'outside_geofence' => true,
                'distance' => $targetDist,
                'max_distance' => self::ALLOWED_RADIUS_METERS,
                'message' => "أنت خارج نطاق مقر العمل ({$targetName}). المسافة: {$targetDist} متر، والمسموح حتى 10 أمتار فقط. يجب التواجد داخل المقر لتسجيل الانصراف.",
            ];
        }

        $photoPath = $this->saveAttendancePhoto($photo, 'checkout');
        if (!$photoPath) {
            return [
                'success' => false,
                'photo_required' => true,
                'message' => 'تعذر حفظ صورة الانصراف. يرجى إعادة المحاولة.',
            ];
        }

        $cairoNow = now('Africa/Cairo');
        $this->attendanceRepository->update($existing, [
            'check_out_at'    => $cairoNow->format('H:i'),
            'check_out_photo' => $photoPath,
            'check_out_lat'   => $lat,
            'check_out_lng'   => $lng,
            'notes'           => ($existing->notes ? $existing->notes . ' | ' : '') . "انصراف موثق بصورة وGPS من {$locationLabel} (المسافة: {$matchedDist}م)",
        ]);

        $this->notifyDirectorAndAdmins(
            "تسجيل انصراف: {$user->name}",
            "سجل الموظف {$user->name} انصرافه الساعة {$cairoNow->format('h:i A')} موثقاً بالصورة وموقع المقر ({$locationLabel})."
        );

        return [
            'success' => true,
            'checked_out' => true,
            'time' => $cairoNow->format('H:i'),
            'location' => $locationLabel,
            'distance' => $matchedDist,
            'photo' => $photoPath,
            'message' => 'تم تسجيل انصرافك وتوثيق صورتك وموقعك بنجاح الساعة ' . $cairoNow->format('h:i A'),
        ];
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

    private function notifyDirectorAndAdmins(string $title, string $body, string $category = 'attendance'): void
    {
        try {
            $adminIds = \App\Models\User::role(['admin', 'super_admin', 'manager'])
                ->orWhereIn('id', [2, 8, 50])
                ->pluck('id')
                ->unique();

            foreach ($adminIds as $adminId) {
                \App\Models\MobileNotification::create([
                    'title'          => $title,
                    'body'           => $body,
                    'category'       => \App\Models\MobileNotification::CATEGORY_GENERAL,
                    'target_type'    => 'user',
                    'target_user_id' => $adminId,
                    'is_sent'        => true,
                    'sent_at'        => now(),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Attendance notification dispatch error: ' . $e->getMessage());
        }
    }
}
