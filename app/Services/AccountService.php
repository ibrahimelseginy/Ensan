<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChangeRequest;
use App\Repositories\AccountRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final readonly class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository
    ) {}

    public function getRootAccounts(): Collection
    {
        return $this->accountRepository->getRootAccounts();
    }

    public function getAllAccountsPaginated(int $perPage = 50): LengthAwarePaginator
    {
        $accounts = $this->accountRepository->paginateAll($perPage);

        $accounts->each(function(Account $account) {
            $account->pendingRequest = ChangeRequest::where('model_type', Account::class)
                ->where('model_id', $account->id)
                ->where('status', 'pending')
                ->first();
        });

        return $accounts;
    }

    public function findAccountById(int $id): ?Account
    {
        return $this->accountRepository->findById($id);
    }

    public function getDashboardData(): array
    {
        $stats = [
            'totalAccounts'     => Account::count(),
            'assetAccounts'     => Account::where('type', 'asset')->count(),
            'liabilityAccounts' => Account::where('type', 'liability')->count(),
            'equityAccounts'    => Account::where('type', 'equity')->count(),
            'revenueAccounts'   => Account::where('type', 'revenue')->count(),
            'expenseAccounts'   => Account::where('type', 'expense')->count(),
            'totalEntries'      => JournalEntry::count(),
            'lockedEntries'     => JournalEntry::where('locked', true)->count(),
            'entriesThisMonth'  => JournalEntry::whereMonth('date', now()->month)
                                     ->whereYear('date', now()->year)
                                     ->count(),
        ];
        
        $stats['unlockedEntries'] = $stats['totalEntries'] - $stats['lockedEntries'];

        // Balances
        $stats['assetBalance']     = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'asset'))->sum(DB::raw('debit - credit'));
        $stats['liabilityBalance'] = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'liability'))->sum(DB::raw('credit - debit'));
        $stats['equityBalance']    = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'equity'))->sum(DB::raw('credit - debit'));
        $stats['revenueBalance']   = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'revenue'))->sum(DB::raw('credit - debit'));
        $stats['expenseBalance']   = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'expense'))->sum(DB::raw('debit - credit'));
        
        $stats['netIncome'] = $stats['revenueBalance'] - $stats['expenseBalance'];

        // Trends
        $trend = $this->calculateTrends(6);
        $stats['trendLabels']      = $trend['labels'];
        $stats['revenueTrendData'] = $trend['revenue'];
        $stats['expenseTrendData'] = $trend['expense'];

        $stats['topAccounts']   = Account::withCount('lines')->orderByDesc('lines_count')->limit(10)->get();
        $stats['latestEntries'] = JournalEntry::with(['lines.account'])->orderByDesc('date')->limit(10)->get();

        $stats['insights'] = $this->generateInsights($stats);

        return $stats;
    }

    private function calculateTrends(int $months): array
    {
        $labels  = [];
        $revenue = [];
        $expense = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('F');

            $revenue[] = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'revenue'))
                ->whereHas('journalEntry', fn($q) => $q->whereMonth('date', $date->month)->whereYear('date', $date->year))
                ->sum(DB::raw('credit - debit'));

            $expense[] = (float) JournalEntryLine::whereHas('account', fn($q) => $q->where('type', 'expense'))
                ->whereHas('journalEntry', fn($q) => $q->whereMonth('date', $date->month)->whereYear('date', $date->year))
                ->sum(DB::raw('debit - credit'));
        }

        return ['labels' => $labels, 'revenue' => $revenue, 'expense' => $expense];
    }

    private function generateInsights(array $stats): array
    {
        $insights = [];

        if ($stats['netIncome'] > 0) {
            $insights[] = ['type' => 'success', 'icon' => 'graph-up-arrow', 'message' => 'صافي الربح: ' . number_format($stats['netIncome']) . ' ج.م'];
        } elseif ($stats['netIncome'] < 0) {
            $insights[] = ['type' => 'danger', 'icon' => 'graph-down-arrow', 'message' => 'صافي الخسارة: ' . number_format(abs($stats['netIncome'])) . ' ج.م'];
        }

        if ($stats['assetBalance'] > $stats['liabilityBalance']) {
            $insights[] = ['type' => 'success', 'icon' => 'shield-check', 'message' => 'الأصول تتجاوز الالتزامات بـ ' . number_format($stats['assetBalance'] - $stats['liabilityBalance']) . ' ج.م'];
        }

        if ($stats['unlockedEntries'] > 10) {
            $insights[] = ['type' => 'warning', 'icon' => 'unlock', 'message' => 'يوجد ' . $stats['unlockedEntries'] . ' قيد غير مقفل'];
        }

        if ($stats['entriesThisMonth'] > 50) {
            $insights[] = ['type' => 'info', 'icon' => 'graph-up', 'message' => 'نشاط مرتفع! ' . $stats['entriesThisMonth'] . ' قيد هذا الشهر'];
        }

        return $insights;
    }

    public function createAccount(array $data): mixed
    {
        $executor = fn() => $this->accountRepository->create($data);

        return ChangeRequestService::handleRequest(
            Account::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateAccount(Account $account, array $data): mixed
    {
        $executor = function () use ($account, $data) {
            $this->accountRepository->update($account, $data);
            return $account;
        };

        return ChangeRequestService::handleRequest(
            Account::class,
            $account->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteAccount(Account $account): mixed
    {
        if ($account->children()->exists() || $account->lines()->exists()) {
            throw new \Exception('لا يمكن حذف حساب لديه أبناء أو قيود');
        }

        $executor = fn() => $this->accountRepository->delete($account);

        return ChangeRequestService::handleRequest(
            Account::class,
            $account->id,
            'delete',
            [
                'note' => 'حذف حساب مالي',
                'name' => $account->name,
                'code' => $account->code
            ],
            $executor,
            true
        );
    }

    public function getSelectableParents(?int $excludeId = null): Collection
    {
        $query = Account::query();
        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }
        return $query->orderBy('code')->get();
    }
}
