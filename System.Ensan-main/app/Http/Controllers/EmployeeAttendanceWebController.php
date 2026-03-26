<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeAttendanceWebController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAttendance::with('user')->orderByDesc('date');

        $user = $request->user();
        if ($user && !$user->hasRole('admin') && !$user->hasRole('manager') && !$user->hasRole('hr')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $records = $query->paginate(50);

        // Today's record for current user
        $todayRecord = null;
        if ($user = $request->user()) {
            $todayRecord = EmployeeAttendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->first();
        }

        return view('employee_attendance.index', compact('records', 'todayRecord'));
    }

    public function checkIn()
    {
        try {
            $user = request()->user();
            if (!$user)
                return redirect()->route('login');

            // 1. Check if record already exists for TODAY
            $existingRecord = EmployeeAttendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->first();

            if ($existingRecord) {
                return back()->with('error', 'لقد قمت بتسجيل الدخول لهذا اليوم مسبقاً في الساعة ' . $existingRecord->check_in_at);
            }

            // 2. Create new record
            EmployeeAttendance::create([
                'user_id' => $user->id,
                'date' => now()->toDateString(),
                'check_in_at' => now()->format('H:i'),
                // check_out_at remains null
            ]);

            return back()->with('success', 'تم تسجيل الدخول بنجاح: ' . now()->format('h:i A'));
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ غير متوقع: ' . $e->getMessage());
        }
    }

    public function checkOut()
    {
        try {
            $user = request()->user();
            if (!$user)
                return redirect()->route('login');

            // 1. Find TODAY's record
            $record = EmployeeAttendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->first();

            // 2. Validation: Must have checked in first
            if (!$record) {
                return back()->with('error', 'يجب عليك تسجيل الدخول (Check In) أولاً قبل تسجيل الخروج.');
            }

            // 3. Validation: Already checked out?
            if (!is_null($record->check_out_at)) {
                return back()->with('error', 'لقد قمت بتسجيل الخروج بالفعل في الساعة ' . $record->check_out_at);
            }

            // 4. Update record
            $record->update([
                'check_out_at' => now()->format('H:i'),
            ]);

            return back()->with('success', 'تم تسجيل الخروج بنجاح: ' . now()->format('h:i A'));
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تسجيل الخروج: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // Filter only employees
        $users = User::where('is_employee', true)->orderBy('name')->get();
        return view('employee_attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in_at' => 'nullable',
            'check_out_at' => 'nullable',
            'notes' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'evaluation_notes' => 'nullable|string'
        ]);

        EmployeeAttendance::create($data);

        return redirect()->route('employee-attendance.index');
    }

    public function show(EmployeeAttendance $employee_attendance)
    {
        return view('employee_attendance.show', ['rec' => $employee_attendance->load('user')]);
    }

    public function edit(EmployeeAttendance $employee_attendance)
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        return view('employee_attendance.edit', ['rec' => $employee_attendance, 'users' => $users]);
    }

    public function update(Request $request, EmployeeAttendance $employee_attendance)
    {
        $data = $request->validate([
            'date' => 'nullable|date',
            'check_in_at' => 'nullable',
            'check_out_at' => 'nullable',
            'notes' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'evaluation_notes' => 'nullable|string'
        ]);

        $executor = function () use ($employee_attendance, $data) {
            $employee_attendance->update($data);
            return $employee_attendance;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\EmployeeAttendance::class,
            $employee_attendance->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل سجل الحضور للموافقة');
        }

        return redirect()->route('employee-attendance.show', $employee_attendance)->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    public function destroy(EmployeeAttendance $employee_attendance)
    {
        $executor = function () use ($employee_attendance) {
            $employee_attendance->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\EmployeeAttendance::class,
            $employee_attendance->id,
            'delete',
            [
                'note' => 'حذف سجل حضور موظف',
                'employee_name' => $employee_attendance->user->name ?? '—',
                'date' => $employee_attendance->date ? $employee_attendance->date->format('Y-m-d') : '—'
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف سجل الحضور للموافقة');
        }

        $employee_attendance->delete();
        return redirect()->route('employee-attendance.index')->with('success', 'تم حذف سجل الحضور بنجاح');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:employee_attendances,id'
        ]);

        $ids = $request->input('ids');
        $count = 0;

        foreach ($ids as $id) {
            $record = EmployeeAttendance::find($id);
            if (!$record) continue;

            // Use the same logic as destroy to ensure Change Request is created
            $executor = function () use ($record) {
                $record->delete();
                return true;
            };

            \App\Services\ChangeRequestService::handleRequest(
                \App\Models\EmployeeAttendance::class,
                $record->id,
                'delete',
                ['note' => 'حذف جماعي لسجلات الحضور'],
                $executor,
                true // Force Request
            );
            $count++;
        }

        return back()->with('success', "تم إرسال طلبات الحذف لـ $count من السجلات للموافقة");
    }
}
