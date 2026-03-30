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

    public function checkIn(): RedirectResponse
    {
        try {
            $user = request()->user();
            if (!$user) {
                return redirect()->route('login');
            }

            $message = $this->attendanceService->checkIn((int)$user->id);
            return back()->with('success', $message);
        } catch (\Exception $e) {
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
