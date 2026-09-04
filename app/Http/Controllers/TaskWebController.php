<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

final class TaskWebController extends Controller
{
    public function index() 
    { 
        $user = auth()->user();
        $query = Task::with(['assignee','assigner'])->orderByDesc('id');
        
        if ($user && !$user->roles()->whereIn('key', ['admin', 'manager'])->exists()) {
            $query->where('assigned_to', $user->id);
        }
        
        $tasks = $query->paginate(50); 
        return view('tasks.index', compact('tasks')); 
    }
    public function create() { $users = User::orderBy('name')->get(); $projects = \App\Models\Project::orderBy('name')->get(); $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get(); $guestHouses = \App\Models\GuestHouse::orderBy('name')->get(); return view('tasks.create', compact('users','projects','campaigns','guestHouses')); }
    public function store(Request $request) { $data = $request->validate(['title' => 'required|string','volunteer_activity_name' => 'nullable|string','description' => 'nullable|string','assigned_to' => 'nullable|exists:users,id','assigned_by' => 'nullable|exists:users,id','due_date' => 'nullable|date','status' => 'in:pending,in_progress,done','project_id' => 'nullable|exists:projects,id','campaign_id' => 'nullable|exists:campaigns,id','guest_house_id' => 'nullable|exists:guest_houses,id']); $task = Task::create($data); return redirect()->route('tasks.show',$task); }
    public function show(Task $task) { return view('tasks.show', ['task' => $task->load(['assignee','assigner','project','campaign','guestHouse'])]); }
    public function edit(Task $task) { $users = User::orderBy('name')->get(); $projects = \App\Models\Project::orderBy('name')->get(); $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get(); $guestHouses = \App\Models\GuestHouse::orderBy('name')->get(); return view('tasks.edit', ['task' => $task, 'users' => $users, 'projects' => $projects, 'campaigns' => $campaigns, 'guestHouses' => $guestHouses]); }
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
            'guest_house_id' => 'nullable|exists:guest_houses,id'
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

        return redirect()->route('tasks.show', $task)->with('success', 'تم تحديث المهمة بنجاح');
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
            [
                'note' => 'حذف مهمة',
                'title' => $task->title,
                'assignee' => $task->assignee->name ?? 'غير مكلف'
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المهمة للموافقة');
        }

        return redirect()->route('tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }
}

