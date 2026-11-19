<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Leave;
use App\Models\ChangeRequest;
use App\Repositories\LeaveRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class LeaveService
{
    public function __construct(
        private LeaveRepository $leaveRepository
    ) {}

    public function getFilteredLeaves(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->leaveRepository->paginateFiltered($filters, $perPage);
    }

    public function findLeaveById(int $id): ?Leave
    {
        return $this->leaveRepository->findById($id);
    }

    public function createLeave(array $data, int $userId): Leave
    {
        $data['user_id'] = $userId;
        $data['status']  = 'pending';

        return $this->leaveRepository->create($data);
    }

    public function updateLeave(Leave $leave, array $data, bool $isManager): mixed
    {
        $executor = function () use ($leave, $data) {
            $oldStatus = $leave->status;
            $newStatus = $data['status'] ?? $oldStatus;

            if ($newStatus === 'approved' && $oldStatus !== 'approved') {
                $data['approved_by'] = auth()->id();
            }

            $this->leaveRepository->update($leave, $data);
            $leave->refresh();

            // Sync employee leave_balance automatically on approval/reversal
            $this->syncLeaveBalance($leave, $oldStatus, $newStatus);

            return $leave;
        };

        // Decision logic: If manager changes status of a pending leave, execute immediately
        if ($isManager && isset($data['status']) && $leave->status === 'pending') {
            return $executor();
        }

        return ChangeRequestService::handleRequest(
            Leave::class,
            $leave->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteLeave(Leave $leave): mixed
    {
        $executor = function () use ($leave) {
            if ($leave->status === 'approved') {
                $this->syncLeaveBalance($leave, 'approved', 'deleted');
            }
            return $this->leaveRepository->delete($leave);
        };

        return ChangeRequestService::handleRequest(
            Leave::class,
            $leave->id,
            'delete',
            ['note' => 'طلب حذف/إلغاء إجازة'],
            $executor,
            true
        );
    }

    private function syncLeaveBalance(Leave $leave, string $oldStatus, string $newStatus): void
    {
        if ($oldStatus === $newStatus || ($leave->type ?? '') === 'unpaid') {
            return;
        }

        $user = $leave->user;
        if (!$user || !$leave->start_date || !$leave->end_date) {
            return;
        }

        $days = max(1, (int) $leave->start_date->diffInDays($leave->end_date) + 1);
        $currentBalance = (int) ($user->leave_balance ?? $user->annual_leave_quota ?? 21);

        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $user->update(['leave_balance' => max(0, $currentBalance - $days)]);
        } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
            $user->update(['leave_balance' => $currentBalance + $days]);
        }
    }

    public function bulkDelete(array $ids, int $authUserId, bool $isManager): int
    {
        $count = 0;
        foreach ($ids as $id) {
            $leave = $this->leaveRepository->findById((int)$id);
            if (!$leave) continue;

            if ($leave->user_id !== $authUserId && !$isManager) {
                continue;
            }

            $this->deleteLeave($leave);
            $count++;
        }
        return $count;
    }
}
