<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\VolunteerHour;
use App\Models\Task;
use App\Models\Campaign;

final class VolunteerWebController extends Controller
{
    public function index() { 
        $volunteers = User::where('is_volunteer',true)->orderBy('name')->paginate(20);
        
        $totalVolunteers = User::where('is_volunteer', true)->count();
        $activeVolunteers = User::where('is_volunteer', true)->where('active', true)->count();
        
        // Count volunteers per project (based on main affiliation)
        $projectCounts = User::where('is_volunteer', true)
            ->whereNotNull('project_id')
            ->select('project_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('project_id')
            ->pluck('total', 'project_id');
            
        $projectsWithStats = \App\Models\Project::orderBy('name')->get()->map(function($p) use ($projectCounts) {
            $p->volunteers_count = $projectCounts[$p->id] ?? 0;
            return $p;
        });

        return view('volunteers.index', compact('volunteers', 'totalVolunteers', 'activeVolunteers', 'projectsWithStats')); 
    }
    public function create() { 
        $projects = \App\Models\Project::all();
        $campaigns = \App\Models\Campaign::all();
        $guestHouses = \App\Models\GuestHouse::all();
        return view('volunteers.create', compact('projects','campaigns','guestHouses')); 
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'active' => 'boolean',
            'college' => 'nullable|string',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'project_role' => 'nullable|string',
            'volunteer_hours' => 'nullable|numeric',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'join_date' => 'nullable|date',
            'profile_photo' => 'nullable|file'
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['is_volunteer'] = true;
        $data['is_employee'] = false;
        
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        unset($data['profile_photo']);

        $user = User::create($data);
        return redirect()->route('volunteers.show',$user);
    }
    public function show(User $volunteer) { return view('volunteers.show', compact('volunteer')); }
    public function edit(User $volunteer) { 
        $projects = \App\Models\Project::all();
        $campaigns = \App\Models\Campaign::all();
        $guestHouses = \App\Models\GuestHouse::all();
        return view('volunteers.edit', compact('volunteer','projects','campaigns','guestHouses')); 
    }
    public function update(Request $request, User $volunteer)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$volunteer->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string',
            'active' => 'boolean',
            'college' => 'nullable|string',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'project_role' => 'nullable|string',
            'volunteer_hours' => 'nullable|numeric',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'join_date' => 'nullable|date',
            'profile_photo' => 'nullable|file'
        ]);
        if (!empty($data['password'])) { $data['password'] = Hash::make($data['password']); }
        
        if ($request->hasFile('profile_photo')) {
            if ($volunteer->profile_photo_path) {
                Storage::disk('public')->delete($volunteer->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        unset($data['profile_photo']);

        $executor = function () use ($volunteer, $data) {
            $volunteer->update($data);
            return $volunteer;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\User::class,
            $volunteer->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل بيانات المتطوع للموافقة');
        }

        return redirect()->route('volunteers.show', $volunteer)->with('success', 'تم تحديث بيانات المتطوع بنجاح');
    }
    public function destroy(User $volunteer) {
        $executor = function () use ($volunteer) {
            $volunteer->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\User::class,
            $volunteer->id,
            'delete',
            [
                'note' => 'حذف متطوع',
                'name' => $volunteer->name,
                'phone' => $volunteer->phone,
                'email' => $volunteer->email
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المتطوع للموافقة');
        }

        return redirect()->route('volunteers.index')->with('success', 'تم حذف المتطوع بنجاح');
    }
    public function reports(Request $request)
    {
        $volunteers = User::where('is_volunteer',true)->orderBy('name')->get();
        $userId = (int) $request->get('user_id', 0);
        $selected = $userId ? User::where('is_volunteer',true)->find($userId) : null;
        $assignments = collect();
        $summary = ['projects' => 0, 'hours' => 0, 'tasks_done' => 0];
        $campaignMap = collect();
        if ($selected) {
            $assignments = $selected->projects()->with(['manager','deputy'])->orderBy('name')->get();
            $summary['projects'] = $assignments->count();
            $summary['hours'] = (float) VolunteerHour::where('user_id',$selected->id)->sum('hours');
            $summary['tasks_done'] = (int) Task::where('assigned_to',$selected->id)->where('status','done')->count();
            $campaignIds = $assignments->pluck('pivot.campaign_id')->filter()->unique()->values();
            if ($campaignIds->count() > 0) {
                $campaignMap = Campaign::whereIn('id',$campaignIds)->get()->keyBy('id');
            }
        }
        return view('volunteers.reports', compact('volunteers','selected','assignments','summary','campaignMap','userId'));
    }
}
