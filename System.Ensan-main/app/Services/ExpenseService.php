<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Expense;
use App\Models\ChangeRequest;
use App\Repositories\ExpenseRepository;
use App\Services\ChangeRequestService;
use App\Services\TreasuryIntegrationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

final readonly class ExpenseService
{
    public function __construct(
        private ExpenseRepository $expenseRepository
    ) {}

    public function getFilteredExpenses(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->expenseRepository->paginateFiltered($filters, $perPage);
    }

    public function findExpenseById(int $id): ?Expense
    {
        return $this->expenseRepository->findById($id);
    }

    public function getFinancialAnalysis(): array
    {
        $currentMonth  = now()->month;
        $currentYear   = now()->year;
        $lastMonthDate = now()->subMonth();
        
        $totalCurrentMonth = (float) Expense::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');
            
        $totalLastMonth = (float) Expense::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)
            ->sum('amount');
            
        $growth = 0;
        if ($totalLastMonth > 0) {
            $growth = (($totalCurrentMonth - $totalLastMonth) / $totalLastMonth) * 100;
        }

        $categoryBreakdown = Expense::where('status', '!=', 'cancelled')
            ->groupBy('type')
            ->selectRaw('type, sum(amount) as total')
            ->get()
            ->pluck('total', 'type');

        $monthlyComparison = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyComparison[] = [
                'label' => $date->translatedFormat('F'),
                'total' => (float) Expense::where('status', '!=', 'cancelled')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount')
            ];
        }

        return [
            'totalCurrentMonth' => $totalCurrentMonth,
            'growth'            => $growth,
            'categoryBreakdown' => $categoryBreakdown,
            'monthlyComparison' => $monthlyComparison,
            'insights'          => $this->generateInsights($growth, $categoryBreakdown)
        ];
    }

    private function generateInsights(float $growth, $categoryBreakdown): array
    {
        $insights = [];
        if ($growth > 10) {
            $insights[] = ['type' => 'warning', 'msg' => "ارتفاع ملحوظ في مصروفات الشهر الحالي بنسبة " . round($growth, 1) . "%"];
        } elseif ($growth < 0) {
            $insights[] = ['type' => 'success', 'msg' => "انخفاض جيد في المصروفات بنسبة " . abs(round($growth, 1)) . "% مقارنة بالشهر السابق"];
        }

        $highestCategory = $categoryBreakdown->sortDesc()->keys()->first();
        if ($highestCategory) {
            $insights[] = ['type' => 'info', 'msg' => "التصنيف الأكثر استهلاكاً هو: $highestCategory"];
        }

        return $insights;
    }

    public function createExpense(array $data): mixed
    {
        if (empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        $executor = function () use ($data) {
            return DB::transaction(function() use ($data) {
                $treasuryId = $data['treasury_id'];
                unset($data['treasury_id']);

                $expense = $this->expenseRepository->create($data);
                app(TreasuryIntegrationService::class)->processExpenseFromTreasury($expense, $treasuryId);
                
                return $expense;
            });
        };

        return ChangeRequestService::handleRequest(
            Expense::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateExpense(Expense $expense, array $data): mixed
    {
        $executor = function () use ($expense, $data) {
            $this->expenseRepository->update($expense, $data);
            return $expense;
        };

        return ChangeRequestService::handleRequest(
            Expense::class,
            $expense->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function cancelExpense(Expense $expense, string $reason): mixed
    {
        $executor = function () use ($expense, $reason) {
            return DB::transaction(function() use ($expense, $reason) {
                $expense->update([
                    'status'              => 'cancelled',
                    'cancelled_at'        => now(),
                    'cancelled_by'        => auth()->id(),
                    'cancellation_reason' => $reason
                ]);

                app(TreasuryIntegrationService::class)->cancelExpenseTransaction($expense, $reason);
                return true;
            });
        };

        return ChangeRequestService::handleRequest(
            Expense::class,
            $expense->id,
            'cancel',
            ['reason' => $reason],
            $executor,
            true
        );
    }

    public function exportToCsv(array $filters): StreamedResponse
    {
        $query = Expense::with(['beneficiary', 'project', 'campaign', 'workspace'])
            ->when($filters['project_id'] ?? null, fn($q, $id) => $q->where('project_id', $id))
            ->when($filters['campaign_id'] ?? null, fn($q, $id) => $q->where('campaign_id', $id))
            ->when($filters['workspace_id'] ?? null, fn($q, $id) => $q->where('workspace_id', $id))
            ->when($filters['month'] ?? null, fn($q, $m) => $q->whereMonth('created_at', $m))
            ->when($filters['year'] ?? null, fn($q, $y) => $q->whereYear('created_at', $y))
            ->orderByDesc('id');

        $filename = "expenses_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['ID', 'الوصف', 'المبلغ', 'العملة', 'التصنيف', 'النوع', 'المشروع', 'الحملة', 'المسكن', 'التاريخ']);
            $query->chunk(100, function ($expenses) use ($handle) {
                foreach ($expenses as $e) {
                    fputcsv($handle, [
                        $e->id,
                        $e->description ?? '—',
                        $e->amount,
                        $e->currency,
                        $e->category ?? '—',
                        $e->type,
                        $e->project->name ?? '—',
                        $e->campaign->name ?? '—',
                        $e->workspace->name ?? '—',
                        $e->created_at?->format('Y-m-d') ?? '—'
                    ]);
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
