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
        $hasTotalIn  = array_key_exists('total_in', $data) && $data['total_in'] !== null && $data['total_in'] !== '';
        $hasTotalOut = array_key_exists('total_out', $data) && $data['total_out'] !== null && $data['total_out'] !== '';
        $hasBalance  = array_key_exists('current_balance', $data) && $data['current_balance'] !== null && $data['current_balance'] !== '';

        $targetIn      = $hasTotalIn ? (float) $data['total_in'] : null;
        $targetOut     = $hasTotalOut ? (float) $data['total_out'] : null;
        $targetBalance = $hasBalance ? (float) $data['current_balance'] : null;

        unset($data['total_in'], $data['total_out']);

        if ($targetIn !== null || $targetOut !== null) {
            $this->syncManualTotals($treasury, $targetIn, $targetOut, $targetBalance, (int) (auth()->id() ?: 1));
            $data['opening_balance'] = $treasury->opening_balance;
            $data['current_balance'] = $treasury->current_balance;
        } elseif ($targetBalance !== null && $targetBalance !== (float) $treasury->current_balance) {
            $netTransactions = (float) $treasury->getTotalIn() - (float) $treasury->getTotalOut();
            $data['opening_balance'] = $targetBalance - $netTransactions;
        }

        return $this->treasuryRepository->update($treasury, $data);
    }

    public function syncManualTotals(
        Treasury $treasury,
        ?float $targetTotalIn,
        ?float $targetTotalOut,
        ?float $targetCurrentBalance,
        ?int $userId = null
    ): void {
        if ($targetTotalIn !== null) {
            $this->syncTransactionTypeTotal($treasury, 'in', max(0.0, $targetTotalIn), $userId);
        }

        if ($targetTotalOut !== null) {
            $this->syncTransactionTypeTotal($treasury, 'out', max(0.0, $targetTotalOut), $userId);
        }

        if ($targetCurrentBalance !== null) {
            $treasury->updateBalance();
            $currentComputed = (float) $treasury->current_balance;
            $diff = round($targetCurrentBalance - $currentComputed, 2);

            if (abs($diff) >= 0.01) {
                TreasuryTransaction::create([
                    'treasury_id'      => $treasury->id,
                    'type'             => $diff > 0 ? 'in' : 'out',
                    'amount'           => abs($diff),
                    'currency'         => $treasury->currency ?? 'EGP',
                    'description'      => abs($targetCurrentBalance) < 0.005
                        ? 'تصفير رصيد الخزينة يدوياً (قيد تسوية جردية مع حفظ السجل التاريخي)'
                        : ($diff > 0 ? 'تسوية جردية بالزيادة (تعديل رصيد الخزينة)' : 'تسوية جردية بالنقص (تعديل رصيد الخزينة)'),
                    'reference'        => 'ADJ-BAL-' . now()->format('YmdHis'),
                    'transaction_date' => now()->toDateString(),
                    'created_by'       => $userId ?: auth()->id(),
                ]);
            }
        }

        $treasury->updateBalance();
    }

    private function syncTransactionTypeTotal(Treasury $treasury, string $type, float $targetTotal, ?int $userId = null): void
    {
        $currentTotal = (float) $treasury->transactions()->where('type', $type)->sum('amount');
        if (abs($targetTotal - $currentTotal) < 0.005) {
            return;
        }

        if ($targetTotal > $currentTotal) {
            $diff = round($targetTotal - $currentTotal, 2);
            TreasuryTransaction::create([
                'treasury_id'      => $treasury->id,
                'type'             => $type,
                'amount'           => $diff,
                'currency'         => $treasury->currency ?? 'EGP',
                'description'      => $type === 'in' ? 'تسوية وارد (قيد تسوية جردية)' : 'تسوية صادر (قيد تسوية جردية)',
                'reference'        => 'ADJ-' . strtoupper($type) . '-' . now()->format('YmdHis'),
                'transaction_date' => now()->toDateString(),
                'created_by'       => $userId ?: auth()->id(),
            ]);
            return;
        }

        // بدلاً من حذف الحركات التاريخية، يتم تسجيل حركة تسوية معاكسة تحفظ السجل المحاسبي
        $diff = round($currentTotal - $targetTotal, 2);
        $oppositeType = $type === 'in' ? 'out' : 'in';
        TreasuryTransaction::create([
            'treasury_id'      => $treasury->id,
            'type'             => $oppositeType,
            'amount'           => $diff,
            'currency'         => $treasury->currency ?? 'EGP',
            'description'      => $type === 'in'
                ? 'تسوية تخفيض وارد (قيد عكسي مع حفظ السجل التاريخي)'
                : 'تسوية تخفيض منصرف (قيد عكسي مع حفظ السجل التاريخي)',
            'reference'        => 'REV-' . strtoupper($type) . '-' . now()->format('YmdHis'),
            'transaction_date' => now()->toDateString(),
            'created_by'       => $userId ?: auth()->id(),
        ]);
    }

    public function updateTransaction(
        Treasury $treasury,
        TreasuryTransaction $transaction,
        array $data,
        bool $recalculateBalance = true
    ): TreasuryTransaction {
        $oldType   = (string) $transaction->type;
        $oldAmount = (float) $transaction->amount;
        $oldEffect = $oldType === 'in' ? $oldAmount : -$oldAmount;

        $transaction->update([
            'type'             => $data['type'],
            'amount'           => (float) $data['amount'],
            'description'      => $data['description'],
            'transaction_date' => $data['transaction_date'],
        ]);

        $newType   = (string) $transaction->type;
        $newAmount = (float) $transaction->amount;
        $newEffect = $newType === 'in' ? $newAmount : -$newAmount;

        if ($recalculateBalance) {
            $treasury->current_balance = (float) $treasury->current_balance - $oldEffect + $newEffect;
        }

        $netTransactions = (float) $treasury->getTotalIn() - (float) $treasury->getTotalOut();
        $treasury->opening_balance = (float) $treasury->current_balance - $netTransactions;
        $treasury->save();

        return $transaction;
    }

    public function deleteTransaction(
        Treasury $treasury,
        TreasuryTransaction $transaction,
        bool $recalculateBalance = true
    ): void {
        $oldType   = (string) $transaction->type;
        $oldAmount = (float) $transaction->amount;
        $oldEffect = $oldType === 'in' ? $oldAmount : -$oldAmount;

        $transaction->delete();

        if ($recalculateBalance) {
            $treasury->current_balance = (float) $treasury->current_balance - $oldEffect;
        }

        $netTransactions = (float) $treasury->getTotalIn() - (float) $treasury->getTotalOut();
        $treasury->opening_balance = (float) $treasury->current_balance - $netTransactions;
        $treasury->save();
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
