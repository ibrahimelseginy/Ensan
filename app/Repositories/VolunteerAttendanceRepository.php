<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\VolunteerAttendance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class VolunteerAttendanceRepository
{
    public function paginateFiltered(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return VolunteerAttendance::with('user')
            ->when($filters['user_id'] ?? null, fn($q, $id) => $q->where('user_id', $id))
            ->orderByDesc('date')
            ->paginate($perPage);
    }

    public function findById(int $id): ?VolunteerAttendance
    {
        return VolunteerAttendance::with('user')->find($id);
    }

    public function create(array $data): VolunteerAttendance
    {
        return VolunteerAttendance::create($data);
    }

    public function update(VolunteerAttendance $attendance, array $data): bool
    {
        return $attendance->update($data);
    }

    public function delete(VolunteerAttendance $attendance): bool
    {
        return $attendance->delete();
    }
}
