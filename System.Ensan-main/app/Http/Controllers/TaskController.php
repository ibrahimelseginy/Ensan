<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() { return Task::with(['assignee','assigner'])->paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in_progress,done'
        ]);
        return Task::create($data);
    }
    public function show(Task $task) { return $task->load(['assignee','assigner']); }
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in_progress,done'
        ]);
        $task->update($data);
        return $task->load(['assignee','assigner']);
    }
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent();
    }
}
