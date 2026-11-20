<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\Fund;
use App\Models\Finance\FundBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Fund availability is a locked projection; movements provide the audit trail. */
final class FundBalanceService
{
    public function move(Fund $fund, string $movement, float $amount, string $currency, int $actorId, array $context = []): FundBalance
    {
        if ($amount <= 0 || !in_array($movement, ['allocate', 'consume', 'release', 'reserve', 'unreserve', 'adjustment'], true)) {
            throw ValidationException::withMessages(['fund' => 'حركة الصندوق أو مبلغها غير صالح.']);
        }
        return DB::transaction(function () use ($fund, $movement, $amount, $currency, $actorId, $context): FundBalance {
            $balance = FundBalance::firstOrCreate(['fund_id' => $fund->id, 'currency' => $currency]);
            $balance = FundBalance::query()->lockForUpdate()->findOrFail($balance->id);
            $available = (float) $balance->available_amount;
            $reserved = (float) $balance->reserved_amount;
            if ($movement === 'allocate' || $movement === 'release' || $movement === 'unreserve') $available += $amount;
            if ($movement === 'consume' || $movement === 'reserve') $available -= $amount;
            if ($movement === 'reserve') $reserved += $amount;
            if ($movement === 'unreserve') $reserved = max(0, $reserved - $amount);
            if (!$fund->allow_negative && $available < -0.00005) {
                throw ValidationException::withMessages(['fund' => 'الرصيد المتاح للصندوق غير كافٍ.']);
            }
            $balance->update(['available_amount' => $available, 'reserved_amount' => $reserved, 'lock_version' => $balance->lock_version + 1]);
            DB::table('fund_movements')->insert([
                'fund_id' => $fund->id, 'fund_balance_id' => $balance->id, 'ledger_entry_id' => $context['ledger_entry_id'] ?? null,
                'movement_type' => $movement, 'amount' => $amount, 'currency' => $currency,
                'source_type' => $context['source_type'] ?? null, 'source_id' => $context['source_id'] ?? null,
                'note' => $context['note'] ?? null, 'created_by' => $actorId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            return $balance->refresh();
        }, 3);
    }
}
