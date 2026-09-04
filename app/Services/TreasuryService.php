<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use App\Models\Account;
use App\Repositories\TreasuryRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

final readonly class TreasuryService
{
    public function __construct(
        private TreasuryRepository $treasuryRepository
    ) {}

    public function getAllTreasuries(): Collection
    {
        return $this->treasuryRepository->all();
    }

    public function getTreasuryById(int $id): ?Treasury
    {
        return $this->treasuryRepository->findById($id);
    }

    public function createTreasury(array $data): Treasury
    {
        $data['current_balance'] = $data['opening_balance'];
        return $this->treasuryRepository->create($data);
    }

    public function updateTreasury(Treasury $treasury, array $data): bool
    {
        return $this->treasuryRepository->update($treasury, $data);
    }

    public function deleteTreasury(Treasury $treasury): bool
    {
        if ($treasury->transactions()->exists()) {
            throw new \Exception('لا يمكن حذف الخزينة لأنها تحتوي على حركات مالية. يمكنك تجميدها بدلاً من ذلك.');
        }
        return $this->treasuryRepository->delete($treasury);
    }

    public function addTransaction(Treasury $treasury, array $data, int $userId): TreasuryTransaction
    {
        $data['treasury_id'] = $treasury->id;
        $data['created_by']  = $userId;
        $data['currency']    = $treasury->currency;

        $transaction = TreasuryTransaction::create($data);
        $treasury->updateBalance();

        return $transaction;
    }

    public function getMonthlyTrends(): array
    {
        $labels      = [];
        $inData      = [];
        $outData     = [];
        $balanceData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('F');

            $monthIn = TreasuryTransaction::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'in')
                ->sum('amount');

            $monthOut = TreasuryTransaction::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'out')
                ->sum('amount');

            $inData[]      = $monthIn;
            $outData[]     = $monthOut;
            $balanceData[] = $monthIn - $monthOut;
        }

        return [
            'labels'  => $labels,
            'in'      => $inData,
            'out'     => $outData,
            'balance' => $balanceData
        ];
    }

    public function getTreasuryMonthlyData(int $treasuryId): array
    {
        $labels  = [];
        $inData  = [];
        $outData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('F');

            $monthIn = TreasuryTransaction::where('treasury_id', $treasuryId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'in')
                ->sum('amount');

            $monthOut = TreasuryTransaction::where('treasury_id', $treasuryId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'out')
                ->sum('amount');

            $inData[]  = $monthIn;
            $outData[] = $monthOut;
        }

        return [
            'labels' => $labels,
            'in'     => $inData,
            'out'    => $outData
        ];
    }

    public function generateInsights(Collection $treasuries): array
    {
        $insights = [];

        foreach ($treasuries as $treasury) {
            if ($treasury->current_balance < 1000 && $treasury->is_active) {
                $insights[] = [
                    'type'    => 'warning',
                    'icon'    => 'exclamation-triangle',
                    'message' => "رصيد {$treasury->name} منخفض: " . number_format((float)$treasury->current_balance, 2) . " {$treasury->currency}"
                ];
            }

            if ($treasury->current_balance > 100000) {
                $insights[] = [
                    'type'    => 'info',
                    'icon'    => 'info-circle',
                    'message' => "رصيد {$treasury->name} مرتفع: " . number_format((float)$treasury->current_balance, 2) . " {$treasury->currency}"
                ];
            }
        }

        $inactiveTreasuries = $treasuries->where('is_active', false)->count();
        if ($inactiveTreasuries > 0) {
            $insights[] = [
                'type'    => 'info',
                'icon'    => 'pause-circle',
                'message' => "يوجد {$inactiveTreasuries} خزينة غير نشطة"
            ];
        }

        $todayTransactions = TreasuryTransaction::whereDate('transaction_date', Carbon::today())->count();
        if ($todayTransactions > 10) {
            $insights[] = [
                'type'    => 'success',
                'icon'    => 'chart-line',
                'message' => "نشاط مرتفع اليوم: {$todayTransactions} حركة"
            ];
        }

        return $insights;
    }

    public function syncAccounts(): int
    {
        $treasuries   = Treasury::all();
        $createdCount = 0;

        foreach ($treasuries as $treasury) {
            $accountCode = '110' . str_pad((string)$treasury->id, 3, '0', STR_PAD_LEFT);
            $account     = Account::where('code', $accountCode)->first();
            
            if (!$account) {
                Account::create([
                    'name'        => $treasury->name,
                    'code'        => $accountCode,
                    'type'        => 'asset',
                    'is_active'   => true,
                    'description' => 'حساب تلقائي للخزينة: ' . $treasury->code
                ]);
                $createdCount++;
            }
        }

        return $createdCount;
    }
}
