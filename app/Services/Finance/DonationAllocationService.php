<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Donation;
use App\Models\Finance\DonationAllocation;
use App\Models\Finance\Fund;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Normalized replacement for donations.split_allocations JSON. */
final readonly class DonationAllocationService
{
    public function __construct(private FundBalanceService $fundBalances) {}

    /** Draft allocations can be edited before confirmation; their exact total must equal the donation. */
    public function replaceDrafts(Donation $donation, array $lines, int $actorId): void
    {
        DB::transaction(function () use ($donation, $lines, $actorId): void {
            $donation = Donation::query()->lockForUpdate()->findOrFail($donation->id);
            $sum = round(array_sum(array_map(fn ($line) => (float) ($line['amount'] ?? 0), $lines)), 4);
            if ($sum !== round((float) $donation->amount, 4)) {
                throw ValidationException::withMessages(['allocations' => 'إجمالي التخصيصات يجب أن يساوي مبلغ التبرع تماماً.']);
            }
            DonationAllocation::query()->where('donation_id', $donation->id)->where('status', 'draft')->delete();
            foreach (array_values($lines) as $index => $line) {
                if ((float) ($line['amount'] ?? 0) <= 0) {
                    throw ValidationException::withMessages(["allocations.$index" => 'مبلغ التخصيص يجب أن يكون موجباً.']);
                }
                DonationAllocation::create([
                    'donation_id' => $donation->id, 'line_no' => $index + 1, 'amount' => $line['amount'],
                    'currency' => $donation->currency ?? 'EGP', 'fund_id' => $line['fund_id'] ?? null,
                    'project_id' => $line['project_id'] ?? null, 'campaign_id' => $line['campaign_id'] ?? null,
                    'beneficiary_id' => $line['beneficiary_id'] ?? null, 'status' => 'draft',
                    'created_by' => $actorId, 'note' => $line['note'] ?? null,
                ]);
            }
        }, 3);
    }

    /** Call only in the same workflow that confirms the incoming cash and posts its ledger entry. */
    public function confirm(Donation $donation, int $actorId, ?int $ledgerEntryId = null): void
    {
        DB::transaction(function () use ($donation, $actorId, $ledgerEntryId): void {
            $lines = DonationAllocation::query()->where('donation_id', $donation->id)->where('status', 'draft')->lockForUpdate()->get();
            if ($lines->isEmpty()) {
                throw ValidationException::withMessages(['allocations' => 'لا توجد تخصيصات مسودة لاعتمادها.']);
            }
            foreach ($lines as $line) {
                if ($line->fund_id) {
                    $fund = Fund::query()->lockForUpdate()->findOrFail($line->fund_id);
                    $this->fundBalances->move($fund, 'allocate', (float) $line->amount, $line->currency, $actorId, [
                        'ledger_entry_id' => $ledgerEntryId, 'source_type' => Donation::class, 'source_id' => $donation->id,
                        'note' => 'تخصيص تبرع #' . $donation->id,
                    ]);
                }
                $line->update(['status' => 'allocated']);
            }
        }, 3);
    }
}
