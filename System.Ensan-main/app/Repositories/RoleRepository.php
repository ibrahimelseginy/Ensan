<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class RoleRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = $filters['q'] ?? null;

        return Role::query()
            ->withCount(['users', 'permissions'])
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                      ->orWhere('key', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Role
    {
        return Role::with(['permissions'])->withCount('users')->find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }
}
