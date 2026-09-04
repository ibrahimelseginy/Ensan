<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EmployeeAttendance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class EmployeeAttendanceRepository
{
    public function paginateFiltered(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $userId = $filters['user_id'] ?? null;

        return EmployeeAttendance::with('user')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('date')
            ->orderByDesc('check_in_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?EmployeeAttendance
    {
        return EmployeeAttendance::with('user')->find($id);
    }

    public function findTodayRecord(int $userId): ?EmployeeAttendance
    {
        return EmployeeAttendance::where('user_id', $userId)
            ->where('date', now()->toDateString())
            ->first();
    }

    public function create(array $data): EmployeeAttendance
    {
        return EmployeeAttendance::create($data);
    }

    public function update(EmployeeAttendance $attendance, array $data): bool
    {
        return $attendance->update($data);
    }

    public function delete(EmployeeAttendance $attendance): bool
    {
        return $attendance->delete();
    }
}
