<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\EmployeeAttendanceService;
use App\Http\Requests\StoreEmployeeAttendanceRequest;
use App\Http\Requests\UpdateEmployeeAttendanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EmployeeAttendanceWebController extends Controller
{
    public function __construct(
        private EmployeeAttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $user    = $request->user();
        $filters = $request->only(['user_id']);

        if ($user && !$user->hasRole('admin') && !$user->hasRole('manager') && !$user->hasRole('hr')) {
            $filters['user_id'] = $user->id;
        }

        $records     = $this->attendanceService->getFilteredAttendance($filters, 50);
        $todayRecord = $user ? $this->attendanceService->findTodayRecord($user->id) : null;

        return view('employee_attendance.index', compact('records', 'todayRecord'));
    }

    public function checkIn(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
                }
                return redirect()->route('login');
            }

            if ($request->filled('device_token') || $request->hasCookie('ensan_device_token')) {
                $token = $request->input('device_token') ?: $request->cookie('ensan_device_token');
                $result = $this->attendanceService->checkInWithAuthorizedDevice(
                    (int) $user->id,
                    (string) $token,
                    $request->ip(),
                    $request->userAgent()
                );
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result, $result['success'] ? 200 : 422);
                }
                return $result['success']
                    ? back()->with('success', $result['message'])
                    : back()->with('error', $result['message']);
            }

            if ($request->filled('latitude') && $request->filled('longitude')) {
                $lat = (float) $request->input('latitude');
                $lng = (float) $request->input('longitude');
                $result = $this->attendanceService->checkInWithLocation((int) $user->id, $lat, $lng);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result, $result['success'] ? 200 : 422);
                }

                return $result['success']
                    ? back()->with('success', $result['message'])
                    : back()->with('error', $result['message']);
            }

            if ($request->boolean('admin_override')) {
                $result = $this->attendanceService->checkInAdminOverride((int) $user->id);
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result, $result['success'] ? 200 : 422);
                }
                return $result['success']
                    ? back()->with('success', $result['message'])
                    : back()->with('error', $result['message']);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى تفعيل صلاحية الموقع (GPS) لتسجيل الحضور من مقر المؤسسة.',
                ], 422);
            }

            return back()->with('error', 'يرجى تفعيل الموقع الجغرافي (GPS) لتسجيل الحضور من مقر المؤسسة.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkOut(): RedirectResponse
    {
        try {
            $user = request()->user();
            if (!$user) {
                return redirect()->route('login');
            }

            $message = $this->attendanceService->checkOut((int)$user->id);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deviceAutoCheckIn(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 401);
        }

        $token = $request->input('device_token') ?: $request->cookie('ensan_device_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'device_authorized' => false,
                'message' => 'هذا الجهاز غير معتمد حالياً.',
            ]);
        }

        $result = $this->attendanceService->checkInWithAuthorizedDevice(
            (int) $user->id,
            (string) $token,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json($result);
    }

    public function authorizeDevice(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user || (!$user->hasRole(['admin', 'manager', 'super_admin']) && !in_array((int)$user->id, [2,8,50], true))) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك باعتماد الأجهزة.'], 403);
        }

        $request->validate([
            'device_name'   => 'required|string|max:100',
            'location_name' => 'required|string|max:100',
            'guest_house_id'=> 'nullable|integer',
        ]);

        $token = bin2hex(random_bytes(32));

        $device = \App\Models\AttendanceDevice::create([
            'device_token'          => $token,
            'device_name'           => $request->input('device_name'),
            'location_name'         => $request->input('location_name'),
            'guest_house_id'        => $request->input('guest_house_id'),
            'authorized_by_user_id' => $user->id,
            'ip_address'            => $request->ip(),
            'user_agent'            => $request->userAgent(),
            'last_used_at'          => now(),
            'is_active'             => true,
        ]);

        $checkInResult = $this->attendanceService->checkInWithAuthorizedDevice(
            (int) $user->id,
            $token,
            $request->ip(),
            $request->userAgent()
        );

        $cookie = cookie('ensan_device_token', $token, 60 * 24 * 365 * 5); // 5 years

        return response()->json([
            'success'      => true,
            'message'      => "تم اعتماد هذا الجهاز بنجاح: {$device->device_name} ({$device->location_name})",
            'device_token' => $token,
            'device'       => $device,
            'check_in'     => $checkInResult,
        ])->withCookie($cookie);
    }

    public function authorizedDevicesList(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user || (!$user->hasRole(['admin', 'manager', 'super_admin']) && !in_array((int)$user->id, [2,8,50], true))) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $devices = \App\Models\AttendanceDevice::with(['authorizedBy:id,name', 'guestHouse:id,name'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['success' => true, 'devices' => $devices]);
    }

    public function revokeDevice(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user || (!$user->hasRole(['admin', 'manager', 'super_admin']) && !in_array((int)$user->id, [2,8,50], true))) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $device = \App\Models\AttendanceDevice::findOrFail($id);
        $device->update(['is_active' => false]);

        return response()->json(['success' => true, 'message' => 'تم إلغاء اعتماد الجهاز بنجاح.']);
    }

    public function create(): View
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        return view('employee_attendance.create', compact('users'));
    }

    public function store(StoreEmployeeAttendanceRequest $request): RedirectResponse
    {
        $this->attendanceService->createAttendance($request->validated());
        return redirect()->route('employee-attendance.index');
    }

    public function show(EmployeeAttendance $employee_attendance): View
    {
        return view('employee_attendance.show', ['rec' => $employee_attendance->load('user')]);
    }

    public function edit(EmployeeAttendance $employee_attendance): View
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        return view('employee_attendance.edit', ['rec' => $employee_attendance, 'users' => $users]);
    }

    public function update(UpdateEmployeeAttendanceRequest $request, EmployeeAttendance $employee_attendance): RedirectResponse
    {
        $result = $this->attendanceService->updateAttendance($employee_attendance, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل سجل الحضور للموافقة');
        }

        return redirect()->route('employee-attendance.show', $employee_attendance)->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    public function destroy(EmployeeAttendance $employee_attendance): RedirectResponse
    {
        $result = $this->attendanceService->deleteAttendance($employee_attendance);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف سجل الحضور للموافقة');
        }

        return redirect()->route('employee-attendance.index')->with('success', 'تم حذف سجل الحضور بنجاح');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:employee_attendances,id'
        ]);

        $count = $this->attendanceService->bulkDelete($request->input('ids'));

        return back()->with('success', "تم إرسال طلبات الحذف لـ $count من السجلات للموافقة");
    }
}
