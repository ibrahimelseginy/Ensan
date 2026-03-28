<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

final class EmployeeTaskWebController extends Controller
{
    public function index(Request $request)
    {
        // Tasks assigned to employees
        $query = Task::whereHas('assignee', function ($q) {
            $q->where('is_employee', true);
        })->with(['assignee', 'assigner'])->orderByDesc('id');

        $user = $request->user();
        if (!$user->roles()->whereIn('key', ['admin', 'manager'])->exists()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->paginate(50);

        return view('employee_tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();

        return view('employee_tasks.create', compact('users', 'projects', 'campaigns', 'guestHouses'));
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
        return redirect()->route('employee-tasks.show', $task);
    }

    public function show(\App\Models\Task $employee_task)
    {
        return view('employee_tasks.show', ['task' => $employee_task->load(['assignee', 'assigner', 'project', 'campaign', 'guestHouse'])]);
    }

    public function edit(\App\Models\Task $employee_task)
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();

        return view('employee_tasks.edit', ['task' => $employee_task, 'users' => $users, 'projects' => $projects, 'campaigns' => $campaigns, 'guestHouses' => $guestHouses]);
    }

    public function update(Request $request, \App\Models\Task $employee_task)
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

        $executor = function () use ($employee_task, $data) {
            $employee_task->update($data);
            return $employee_task;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Task::class,
            $employee_task->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المهمة للموافقة');
        }

        return redirect()->route('employee-tasks.show', $employee_task)->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(\App\Models\Task $employee_task)
    {
        $executor = function () use ($employee_task) {
            $employee_task->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Task::class,
            $employee_task->id,
            'delete',
            ['note' => 'حذف مهمة موظف'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المهمة للموافقة');
        }

        return redirect()->route('employee-tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }
}
