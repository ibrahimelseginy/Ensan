<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\VolunteerHour;
use App\Models\User;
use Illuminate\Http\Request;

final class VolunteerHourWebController extends Controller
{
    public function index() { $hours = VolunteerHour::with('user')->orderByDesc('date')->paginate(50); return view('vhours.index', compact('hours')); }
    public function create() { $users = User::orderBy('name')->get(); return view('vhours.create', compact('users')); }
    public function store(Request $request) { $data = $request->validate(['user_id' => 'required|exists:users,id','date' => 'required|date','hours' => 'required|numeric','task' => 'nullable|string']); VolunteerHour::create($data); return redirect()->route('volunteer-hours.index'); }
    public function show(VolunteerHour $volunteer_hour) { return view('vhours.show', ['vh' => $volunteer_hour->load('user')]); }
    public function edit(VolunteerHour $volunteer_hour) { $users = User::orderBy('name')->get(); return view('vhours.edit', ['vh' => $volunteer_hour, 'users' => $users]); }
    public function update(Request $request, VolunteerHour $volunteer_hour) {
        $data = $request->validate(['date' => 'nullable|date','hours' => 'nullable|numeric','task' => 'nullable|string']);
        
        $executor = function () use ($volunteer_hour, $data) {
            $volunteer_hour->update($data);
            return $volunteer_hour;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\VolunteerHour::class,
            $volunteer_hour->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('volunteer-hours.show', $volunteer_hour)->with('success', 'تم إرسال طلب تعديل الساعات للموافقة');
        }

        return redirect()->route('volunteer-hours.show', $volunteer_hour)->with('success', 'تم تحديث الساعات بنجاح');
    }
    public function destroy(VolunteerHour $volunteer_hour) {
        $executor = function () use ($volunteer_hour) {
            $volunteer_hour->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\VolunteerHour::class,
            $volunteer_hour->id,
            'delete',
            [
                'note' => 'حذف ساعات تطوع',
                'user_name' => $volunteer_hour->user->name ?? '—',
                'date' => $volunteer_hour->date->format('Y-m-d')
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('volunteer-hours.index')->with('success', 'تم إرسال طلب حذف الساعات للموافقة');
        }

        return redirect()->route('volunteer-hours.index')->with('success', 'تم حذف الساعات بنجاح');
    }
}
