<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payroll;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Repositories\PayrollRepository;
use App\Services\PayrollAccountingService;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

final readonly class PayrollService
{
    public function __construct(
        private PayrollRepository $payrollRepository,
        private PayrollAccountingService $payrollAccountingService
    ) {}

    public function getAllPayrolls(int $perPage = 50): LengthAwarePaginator
    {
        return $this->payrollRepository->paginate($perPage);
    }

    public function findPayrollById(int $id): ?Payroll
    {
        return $this->payrollRepository->findById($id);
    }

    public function createPayroll(array $data, bool $forceRequest = true): mixed
    {
        $data['status'] = !empty($data['paid_at']) ? 'paid' : 'pending';

        $executor = function () use ($data) {
            $payroll = $this->payrollRepository->create($data);
            $payroll->calculateNetAmount();
            $payroll->save();

            if (!empty($data['create_journal_entry'])) {
                try {
                    $this->payrollAccountingService->createJournalEntry($payroll);
                } catch (\Exception $e) {
                    // Fail silently or log? Base code failed silently.
                }
            }
            return $payroll;
        };

        return ChangeRequestService::handleRequest(
            Payroll::class,
            null,
            'create',
            $data,
            $executor,
            $forceRequest
        );
    }

    public function updatePayroll(Payroll $payroll, array $data, bool $forceRequest = true): mixed
    {
        $executor = function () use ($payroll, $data) {
            $oldPaidAt = $payroll->paid_at;
            $newPaidAt = $data['paid_at'] ?? $oldPaidAt;

            if ($newPaidAt && !$oldPaidAt && $payroll->journal_entry_id) {
                try {
                    $this->payrollAccountingService->markAsPaid($payroll);
                } catch (\Exception $e) {}
            }

            $this->payrollRepository->update($payroll, $data);
            $payroll->calculateNetAmount();
            $payroll->save();

            return $payroll;
        };

        return ChangeRequestService::handleRequest(
            Payroll::class,
            $payroll->id,
            'update',
            $data,
            $executor,
            $forceRequest
        );
    }

    public function deletePayroll(Payroll $payroll): mixed
    {
        $executor = function () use ($payroll) {
            if ($payroll->journal_entry_id) {
                try {
                    $this->payrollAccountingService->reverseJournalEntry($payroll);
                } catch (\Exception $e) {}
            }
            return $this->payrollRepository->delete($payroll);
        };

        return ChangeRequestService::handleRequest(
            Payroll::class,
            $payroll->id,
            'delete',
            ['note' => 'حذف راتب'],
            $executor,
            true
        );
    }

    public function getDashboardData(): array
    {
        $currentMonth = now()->format('Y-m');

        $stats = [
            'totalPayrolls'      => Payroll::count(),
            'paidPayrolls'       => Payroll::where('status', 'paid')->count(),
            'pendingPayrolls'    => Payroll::where('status', 'pending')->count(),
            'totalPaidAmount'    => (float)Payroll::where('status', 'paid')->sum('net_amount'),
            'totalPendingAmount' => (float)Payroll::where('status', 'pending')->sum('net_amount'),
            'payrollsThisMonth'  => Payroll::where('month', 'LIKE', $currentMonth . '%')->count(),
            'amountThisMonth'    => (float)Payroll::where('month', 'LIKE', $currentMonth . '%')->sum('net_amount'),
        ];

        $trendLabels = [];
        $trendData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $date          = now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('F');
            $monthKey      = $date->format('Y-m');
            $trendData[]   = (float)Payroll::where('month', 'LIKE', $monthKey . '%')->sum('net_amount');
        }

        $latestPayrolls = Payroll::with(['user', 'journalEntry'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $employeesWithoutPayroll = User::where('is_employee', true)
            ->where('active', true)
            ->whereDoesntHave('payrolls', fn($q) => $q->where('month', 'LIKE', $currentMonth . '%'))
            ->count();

        $insights = $this->generateInsights($stats, $employeesWithoutPayroll);

        return array_merge($stats, [
            'trendLabels'             => $trendLabels,
            'trendData'               => $trendData,
            'latestPayrolls'          => $latestPayrolls,
            'employeesWithoutPayroll' => $employeesWithoutPayroll,
            'insights'                => $insights
        ]);
    }

    private function generateInsights(array $stats, int $employeesWithoutPayroll): array
    {
        $insights = [];

        if ($stats['pendingPayrolls'] > 0) {
            $insights[] = [
                'type'    => 'warning',
                'icon'    => 'clock',
                'message' => "{$stats['pendingPayrolls']} راتب معلق بقيمة " . number_format($stats['totalPendingAmount']) . " ج.م"
            ];
        }

        if ($employeesWithoutPayroll > 0) {
            $insights[] = [
                'type'    => 'info',
                'icon'    => 'people',
                'message' => "{$employeesWithoutPayroll} موظف لم يتم صرف راتبه هذا الشهر"
            ];
        }

        if ($stats['payrollsThisMonth'] > 0) {
            $insights[] = [
                'type'    => 'success',
                'icon'    => 'cash-stack',
                'message' => "إجمالي رواتب هذا الشهر: " . number_format($stats['amountThisMonth']) . " ج.م"
            ];
        }

        return $insights;
    }

    public function createAccountingEntry(Payroll $payroll): void
    {
        if ($payroll->journal_entry_id) {
            throw new \Exception('يوجد قيد محاسبي مرتبط بالفعل');
        }
        $this->payrollAccountingService->createJournalEntry($payroll);
    }
}
