<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\VolunteerAttendance;
use App\Models\User;
use Illuminate\Http\Request;

final class VolunteerAttendanceWebController extends Controller
{
    public function index(Request $request) { 
        $query = VolunteerAttendance::with('user')->orderByDesc('date');
        $user = $request->user();
        if ($user && !$user->hasRole('admin') && !$user->hasRole('manager') && !$user->hasRole('hr')) {
            $query->where('user_id', $user->id);
        }

        if($request->has('user_id')){ $query->where('user_id', $request->user_id); }
        $records = $query->paginate(50); 
        return view('attendance.index', compact('records')); 
    }
    public function create() { $users = User::where('is_volunteer',true)->orderBy('name')->get(); return view('attendance.create', compact('users')); }
    public function store(Request $request) { $data = $request->validate(['user_id' => 'required|exists:users,id','date' => 'required|date','check_in_at' => 'nullable','check_out_at' => 'nullable','notes' => 'nullable|string','rating'=>'nullable|integer|min:1|max:5','evaluation_notes'=>'nullable|string']); VolunteerAttendance::create($data); return redirect()->route('volunteer-attendance.index'); }
    public function show(VolunteerAttendance $volunteer_attendance) { return view('attendance.show', ['rec' => $volunteer_attendance->load('user')]); }
    public function edit(VolunteerAttendance $volunteer_attendance) { $users = User::where('is_volunteer',true)->orderBy('name')->get(); return view('attendance.edit', ['rec' => $volunteer_attendance, 'users' => $users]); }
    public function update(Request $request, VolunteerAttendance $volunteer_attendance)
    {
        $data = $request->validate([
            'date' => 'nullable|date',
            'check_in_at' => 'nullable',
            'check_out_at' => 'nullable',
            'notes' => 'nullable|string',
            'rating'=>'nullable|integer|min:1|max:5',
            'evaluation_notes'=>'nullable|string'
        ]);

        $executor = function () use ($volunteer_attendance, $data) {
            $volunteer_attendance->update($data);
            return $volunteer_attendance;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\VolunteerAttendance::class,
            $volunteer_attendance->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل سجل الحضور للموافقة');
        }

        return redirect()->route('volunteer-attendance.show', $volunteer_attendance)->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    public function destroy(VolunteerAttendance $volunteer_attendance)
    {
        $executor = function () use ($volunteer_attendance) {
            $volunteer_attendance->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\VolunteerAttendance::class,
            $volunteer_attendance->id,
            'delete',
            [
                'note' => 'حذف سجل حضور متطوع',
                'volunteer_name' => $volunteer_attendance->user->name ?? '—',
                'date' => $volunteer_attendance->date ? $volunteer_attendance->date->format('Y-m-d') : '—'
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف سجل الحضور للموافقة');
        }

        return redirect()->route('volunteer-attendance.index')->with('success', 'تم حذف سجل الحضور بنجاح');
    }
}

