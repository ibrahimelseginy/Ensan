<?php
namespace App\Http\Controllers;

use App\Models\VolunteerHour;
use Illuminate\Http\Request;

class VolunteerHourController extends Controller
{
    public function index() { return VolunteerHour::with('user')->paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours' => 'required|numeric',
            'task' => 'nullable|string'
        ]);
        return VolunteerHour::create($data);
    }
    public function show(VolunteerHour $volunteerHour) { return $volunteerHour->load('user'); }
    public function update(Request $request, VolunteerHour $volunteerHour)
    {
        $data = $request->validate([
            'date' => 'nullable|date',
            'hours' => 'nullable|numeric',
            'task' => 'nullable|string'
        ]);
        $volunteerHour->update($data);
        return $volunteerHour->load('user');
    }
    public function destroy(VolunteerHour $volunteerHour)
    {
        $volunteerHour->delete();
        return response()->noContent();
    }
}
