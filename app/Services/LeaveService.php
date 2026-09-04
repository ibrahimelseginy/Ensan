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

    public function createLeave(array $data, int $userId): mixed
    {
        $data['user_id'] = $userId;
        $data['status']  = 'pending';

        $executor = fn() => $this->leaveRepository->create($data);

        return ChangeRequestService::handleRequest(
            Leave::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function updateLeave(Leave $leave, array $data, bool $isManager): mixed
    {
        $executor = function () use ($leave, $data) {
            if (isset($data['status']) && $data['status'] === 'approved') {
                $data['approved_by'] = auth()->id();
            }
            $this->leaveRepository->update($leave, $data);
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
