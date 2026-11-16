<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Leave;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class LeaveRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $userId = $filters['user_id'] ?? null;
        $status = $filters['status'] ?? null;
        $type   = $filters['type'] ?? null;

        return Leave::with(['user', 'changeRequests' => fn($q) => $q->where('status', 'pending')])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($type, function ($q) use ($type) {
                if ($type === 'leaves_only') {
                    $q->where('type', '!=', 'permission');
                } else {
                    $q->where('type', $type);
                }
            })
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Leave
    {
        return Leave::with(['user', 'changeRequests'])->find($id);
    }

    public function create(array $data): Leave
    {
        return Leave::create($data);
    }

    public function update(Leave $leave, array $data): bool
    {
        return $leave->update($data);
    }

    public function delete(Leave $leave): bool
    {
        return $leave->delete();
    }
}
