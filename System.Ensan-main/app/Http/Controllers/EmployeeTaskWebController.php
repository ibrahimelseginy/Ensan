<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\ChangeRequest;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EmployeeTaskWebController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index(Request $request): View
    {
        $user    = $request->user();
        $filters = $request->only(['assigned_to']);

        if ($user && !$user->roles()->whereIn('key', ['admin', 'manager'])->exists()) {
            $filters['assigned_to'] = $user->id;
        }

        $tasks = $this->taskService->getEmployeeTasks($filters, 50);

        return view('employee_tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $users       = User::where('is_employee', true)->orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('employee_tasks.create', compact('users', 'projects', 'campaigns', 'guestHouses'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $this->taskService->createTask($request->validated());
        return redirect()->route('employee-tasks.show', $task);
    }

    public function show(Task $employee_task): View
    {
        return view('employee_tasks.show', [
            'task' => $employee_task->load(['assignee', 'assigner', 'project', 'campaign', 'guestHouse'])
        ]);
    }

    public function edit(Task $employee_task): View
    {
        $users       = User::where('is_employee', true)->orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('employee_tasks.edit', [
            'task'        => $employee_task,
            'users'       => $users,
            'projects'    => $projects,
            'campaigns'   => $campaigns,
            'guestHouses' => $guestHouses
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $employee_task): RedirectResponse
    {
        $result = $this->taskService->updateTask($employee_task, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المهمة للموافقة');
        }

        return redirect()->route('employee-tasks.show', $employee_task)->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(Task $employee_task): RedirectResponse
    {
        $result = $this->taskService->deleteTask($employee_task);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المهمة للموافقة');
        }

        return redirect()->route('employee-tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }
}
