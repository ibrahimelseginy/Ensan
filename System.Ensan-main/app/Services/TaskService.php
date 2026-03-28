<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\ChangeRequest;
use App\Repositories\TaskRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    public function getEmployeeTasks(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->taskRepository->paginateEmployeeTasks($filters, $perPage);
    }

    public function getVolunteerTasks(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->taskRepository->paginateVolunteerTasks($filters, $perPage);
    }

    public function findTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    public function createTask(array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function updateTask(Task $task, array $data): mixed
    {
        $executor = function () use ($task, $data) {
            $this->taskRepository->update($task, $data);
            return $task;
        };

        return ChangeRequestService::handleRequest(
            Task::class,
            $task->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteTask(Task $task): mixed
    {
        $executor = function () use ($task) {
            return $this->taskRepository->delete($task);
        };

        return ChangeRequestService::handleRequest(
            Task::class,
            $task->id,
            'delete',
            ['note' => 'حذف مهمة موظف'],
            $executor,
            true
        );
    }
}
