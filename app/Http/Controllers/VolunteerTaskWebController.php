<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

final class VolunteerTaskWebController extends Controller
{
    public function index(Request $request)
    {
        // Tasks assigned to volunteers
        $query = Task::whereHas('assignee', function ($q) {
            $q->where('is_volunteer', true);
        })->with(['assignee', 'assigner'])->orderByDesc('id');

        $user = $request->user();
        if (!$user->roles()->whereIn('key', ['admin', 'manager'])->exists()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->paginate(50);

        return view('volunteer_tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::where('is_volunteer', true)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();

        return view('volunteer_tasks.create', compact('users', 'projects', 'campaigns', 'guestHouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'volunteer_activity_name' => 'nullable|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in_progress,done',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'evaluation_notes' => 'nullable|string'
        ]);

        $task = Task::create($data);
        return redirect()->route('volunteer-tasks.show', $task);
    }

    public function show(Task $task)
    {
        return view('volunteer_tasks.show', ['task' => $task->load(['assignee', 'assigner', 'project', 'campaign', 'guestHouse'])]);
    }

    public function edit(Task $task)
    {
        $users = User::where('is_volunteer', true)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();

        return view('volunteer_tasks.edit', ['task' => $task, 'users' => $users, 'projects' => $projects, 'campaigns' => $campaigns, 'guestHouses' => $guestHouses]);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'volunteer_activity_name' => 'nullable|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in_progress,done',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'evaluation_notes' => 'nullable|string'
        ]);

        $executor = function () use ($task, $data) {
            $task->update($data);
            return $task;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Task::class,
            $task->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المهمة للموافقة');
        }

        return redirect()->route('volunteer-tasks.show', $task)->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(Task $task)
    {
        $executor = function () use ($task) {
            $task->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Task::class,
            $task->id,
            'delete',
            ['note' => 'حذف مهمة متطوع'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المهمة للموافقة');
        }

        return redirect()->route('volunteer-tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }
}
