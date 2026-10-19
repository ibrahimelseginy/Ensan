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

            return app(FinancialPeriodWriter::class)->execute('create', $data, null, $userId);
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
        app(FinancialPeriodWriter::class)->execute('toggle', [], $closure->id, $userId);
    }

    public function updateClosure(FinancialClosure $closure, array $data): mixed
    {
        $executor = function () use ($closure, $data) {
            return app(FinancialPeriodWriter::class)->execute('update', $data, $closure->id, request()->user()?->id);
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
            return app(FinancialPeriodWriter::class)->execute('delete', [], $closure->id, request()->user()?->id);
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

}
