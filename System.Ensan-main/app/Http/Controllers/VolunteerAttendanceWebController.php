<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\VolunteerAttendance;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\VolunteerAttendanceService;
use App\Http\Requests\StoreVolunteerAttendanceRequest;
use App\Http\Requests\UpdateVolunteerAttendanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class VolunteerAttendanceWebController extends Controller
{
    public function __construct(
        private VolunteerAttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $user    = $request->user();
        $filters = $request->only(['user_id']);

        if ($user && !$user->hasRole('admin') && !$user->hasRole('manager') && !$user->hasRole('hr')) {
            $filters['user_id'] = $user->id;
        }

        $records = $this->attendanceService->getFilteredRecords($filters, 50);

        return view('attendance.index', compact('records'));
    }

    public function create(): View
    {
        $users = User::where('is_volunteer', true)->orderBy('name')->get();
        return view('attendance.create', compact('users'));
    }

    public function store(StoreVolunteerAttendanceRequest $request): RedirectResponse
    {
        $this->attendanceService->createRecord($request->validated());
        return redirect()->route('volunteer-attendance.index')->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function show(VolunteerAttendance $volunteer_attendance): View
    {
        return view('attendance.show', ['rec' => $volunteer_attendance->load('user')]);
    }

    public function edit(VolunteerAttendance $volunteer_attendance): View
    {
        $users = User::where('is_volunteer', true)->orderBy('name')->get();
        return view('attendance.edit', ['rec' => $volunteer_attendance, 'users' => $users]);
    }

    public function update(UpdateVolunteerAttendanceRequest $request, VolunteerAttendance $volunteer_attendance): RedirectResponse
    {
        $result = $this->attendanceService->updateRecord($volunteer_attendance, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل سجل الحضور للموافقة');
        }

        return redirect()->route('volunteer-attendance.show', $volunteer_attendance)->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    public function destroy(VolunteerAttendance $volunteer_attendance): RedirectResponse
    {
        $result = $this->attendanceService->deleteRecord($volunteer_attendance);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف سجل الحضور للموافقة');
        }

        return redirect()->route('volunteer-attendance.index')->with('success', 'تم حذف سجل الحضور بنجاح');
    }
}
