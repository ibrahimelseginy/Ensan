<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FinancialClosure;
use App\Models\JournalEntry;
use App\Repositories\FinancialClosureRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final readonly class FinancialClosureService
{
    public function __construct(
        private FinancialClosureRepository $financialClosureRepository
    ) {}

    public function getAllClosures(int $perPage = 20): LengthAwarePaginator
    {
        return $this->financialClosureRepository->paginate($perPage);
    }

    public function createClosure(array $data, ?int $userId, bool $shouldApproveImmediately = false): mixed
    {
        $executor = function () use ($data, $userId, $shouldApproveImmediately) {
            $data['closed_by'] = $userId;
            $data['approved'] = $shouldApproveImmediately;
            if ($shouldApproveImmediately) {
                $data['approved_by'] = $userId;
            }

            $closure = $this->financialClosureRepository->create($data);

            if ($closure->approved) {
                $this->lockJournalEntries($closure->date->toDateString(), $closure->branch);
            }

            return $closure;
        };

        return ChangeRequestService::handleRequest(
            FinancialClosure::class,
            null,
            'create',
            $data,
            $executor,
            !$shouldApproveImmediately
        );
    }

    public function approveClosure(FinancialClosure $closure, int $userId): void
    {
        $newState = !$closure->approved;
        $this->financialClosureRepository->update($closure, [
            'approved'    => $newState,
            'approved_by' => $newState ? $userId : null
        ]);

        if ($newState) {
            $this->lockJournalEntries($closure->date->toDateString(), $closure->branch);
        }
    }

    public function updateClosure(FinancialClosure $closure, array $data): mixed
    {
        $executor = function () use ($closure, $data) {
            $this->financialClosureRepository->update($closure, $data);
            return $closure;
        };

        return ChangeRequestService::handleRequest(
            FinancialClosure::class,
            $closure->id,
            'update',
            $data,
            $executor,
            true // Force Request as per existing logic
        );
    }

    public function deleteClosure(FinancialClosure $closure): mixed
    {
        $executor = function () use ($closure) {
            return $this->financialClosureRepository->delete($closure);
        };

        return ChangeRequestService::handleRequest(
            FinancialClosure::class,
            $closure->id,
            'delete',
            ['note' => 'حذف إغلاق مالي'],
            $executor,
            true // Force Request as per existing logic
        );
    }

    private function lockJournalEntries(string $date, ?string $branch): void
    {
        JournalEntry::where('date', '<=', $date)
            ->when($branch, function ($q, $b) {
                $q->where('branch', $b);
            })
            ->update(['locked' => true]);
    }
}
