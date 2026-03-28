<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class TaskRepository
{
    public function paginateEmployeeTasks(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $assignedTo = $filters['assigned_to'] ?? null;

        return Task::whereHas('assignee', fn($q) => $q->where('is_employee', true))
            ->with(['assignee', 'assigner'])
            ->when($assignedTo, fn($q) => $q->where('assigned_to', $assignedTo))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Task
    {
        return Task::with(['assignee', 'assigner', 'project', 'campaign', 'guestHouse'])->find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
