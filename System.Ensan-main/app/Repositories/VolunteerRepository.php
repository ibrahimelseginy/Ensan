<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class VolunteerRepository
{
    public function paginateVolunteers(int $perPage = 20): LengthAwarePaginator
    {
        return User::where('is_volunteer', true)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::where('is_volunteer', true)
            ->with(['projects', 'campaign', 'guestHouse'])
            ->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $volunteer, array $data): bool
    {
        return $volunteer->update($data);
    }

    public function delete(User $volunteer): bool
    {
        return $volunteer->delete();
    }
}
