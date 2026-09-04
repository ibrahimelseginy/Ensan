<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class UserRepository
{
    public function paginateFilteredUsers(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $active    = $filters['active'] ?? null;
        $projectId = $filters['project_id'] ?? null;
        $type      = $filters['type'] ?? null;

        return User::where('is_volunteer', false)
            ->with('roles')
            ->when(!is_null($active) && $active !== '', fn($q) => $q->where('active', (bool)$active))
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($type === 'employee', fn($q) => $q->where('is_employee', true))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with(['roles', 'project'])->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
