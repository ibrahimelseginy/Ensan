<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Repositories\DonationRepository;
use App\Repositories\DonorRepository;
use App\Services\ChangeRequestService;
use App\Services\TreasuryIntegrationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

final readonly class DonationService
{
    public function __construct(
        private DonationRepository $donationRepository,
        private DonorRepository $donorRepository,
        private TreasuryIntegrationService $treasuryIntegrationService
    ) {}

    public function searchDonations(array $filters, int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        return $this->donationRepository->paginateFiltered($filters, $perPage, $pageName);
    }

    public function createDonation(array $data, bool $forceRequest = false): mixed
    {
        // 1. Handle Inline Donor Creation
        if (empty($data['donor_id']) && !empty($data['new_donor_name'])) {
            $donor = $this->donorRepository->create([
                'name'            => $data['new_donor_name'],
                'type'            => 'individual',
                'phone'           => $data['new_donor_phone'] ?? null,
                'address'         => $data['new_donor_address'] ?? null,
                'classification'  => $data['new_donor_classification'] ?? 'one_time',
                'recurring_cycle' => $data['new_donor_cycle'] ?? null,
                'active'          => true
            ]);
            $data['donor_id'] = $donor->id;
        }

        // Cleanup temporary donor fields
        unset($data['new_donor_name'], $data['new_donor_phone'], $data['new_donor_address'], $data['new_donor_classification'], $data['new_donor_cycle']);

        // 2. Handle Allocation Note Magic Strings
        $this->processAllocationNotes($data);

        // 3. Prepare default values
        if (empty($data['received_at'])) {
            $data['received_at'] = now();
        }

        $executor = function () use ($data) {
            $donation = $this->donationRepository->create($data);
            
            if ($donation->type === 'cash' && !empty($data['treasury_id'])) {
                $this->treasuryIntegrationService->processDonationToTreasury($donation, (int)$data['treasury_id']);
            } else {
                $this->processPostCreate($donation);
            }
            
            return $donation;
        };

        return ChangeRequestService::handleRequest(
            Donation::class,
            null,
            'create',
            $data,
            $executor,
            $forceRequest
        );
    }

    public function updateDonation(Donation $donation, array $data, bool $forceRequest = true): mixed
    {
        $this->processAllocationNotes($data);

        $executor = function () use ($donation, $data) {
            $this->donationRepository->update($donation, $data);
            return $donation;
        };

        return ChangeRequestService::handleRequest(
            Donation::class,
            $donation->id,
            'update',
            $data,
            $executor,
            $forceRequest
        );
    }

    public function cancelDonation(Donation $donation, string $reason): mixed
    {
        $executor = function () use ($donation, $reason) {
            $donation->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancelled_by'        => (int)auth()->id(),
                'cancellation_reason' => $reason
            ]);

            if ($donation->type === 'cash' && $donation->treasury_id) {
                $this->treasuryIntegrationService->cancelDonationTransaction($donation, $reason);
            }

            return true;
        };

        return ChangeRequestService::handleRequest(
            Donation::class,
            $donation->id,
            'cancel',
            ['reason' => $reason],
            $executor,
            true
        );
    }

    public function getDashboardStats(array $filters = []): array
    {
        return [
            'dailySummary'   => $this->donationRepository->getDailySummary(7, $filters),
            'todayByChannel' => $this->donationRepository->getTodaySummaryByChannel($filters),
            'todayInKind'    => $this->donationRepository->getTodayInKindSummary($filters)
        ];
    }

    private function processAllocationNotes(array &$data): void
    {
        $allocType   = $data['allocation_type'] ?? null;
        $currentNote = $data['allocation_note'] ?? '';

        if ($allocType === 'sadaqa_jariya' && !str_contains($currentNote, 'sponsorship=')) {
            $data['allocation_note'] = trim("sponsorship=sadaqa_jariya\n" . $currentNote);
        } elseif ($allocType === 'sponsorship' && !str_contains($currentNote, 'sponsorship=')) {
            $sType = $data['sponsorship_type'] ?? 'طفل';
            $benId = $data['beneficiary_id'] ?? null;
            $magic = "sponsorship={$sType}" . ($benId ? ";beneficiary_id={$benId}" : "");
            $data['allocation_note'] = trim($magic . "\n" . $currentNote);
        }

        if (!empty($data['sponsorship_kind']) && !str_contains($currentNote, 'sponsorship=')) {
            $sKind = $data['sponsorship_kind'];
            $benId = $data['beneficiary_id'] ?? null;
            $note  = "sponsorship=" . $sKind;
            if ($benId && str_starts_with($sKind, 'kafalat_')) {
                $note .= ";beneficiary_id=" . $benId;
            }
            $data['allocation_note'] = trim($note . "\n" . $currentNote);
        }

        unset($data['allocation_type'], $data['sponsorship_kind'], $data['sponsorship_type']);
    }

    private function processPostCreate(Donation $donation): void
    {
        if ($donation->type === 'cash') {
            $this->createCashJournal($donation);
        } else {
            $this->createInKindJournal($donation);
        }
    }

    private function createCashJournal(Donation $donation): void
    {
        $assetAccount = match ($donation->cash_channel) {
            'vodafone_cash' => $this->getOrCreateAccount('10202', 'donation_Vcash', 'asset'),
            'instapay'      => $this->getOrCreateAccount('10203', 'donation_instapay', 'asset'),
            'delegate'      => $this->getOrCreateAccount('10201', 'Logistics_Delivery cash', 'asset'),
            default         => $this->getOrCreateAccount('102', 'donation_cash', 'asset'),
        };

        $donationsRevenue = $this->getOrCreateAccount('401', 'Donations Revenue', 'revenue');
        
        $entry = JournalEntry::create([
            'date'       => $donation->received_at ? $donation->received_at->toDateString() : now()->toDateString(),
            'entry_type' => $assetAccount->name,
            'gate'       => 'donation',
            'locked'     => false,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $assetAccount->id,
            'debit'            => $donation->amount ?? 0,
            'credit'           => 0,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $donationsRevenue->id,
            'debit'            => 0,
            'credit'           => $donation->amount ?? 0,
        ]);
    }

    private function createInKindJournal(Donation $donation): void
    {
        $inventory        = $this->getOrCreateAccount('120', 'Inventory - In Kind', 'asset');
        $donationsRevenue = $this->getOrCreateAccount('401', 'Donations Revenue', 'revenue');

        $entry = JournalEntry::create([
            'date'       => $donation->received_at ? $donation->received_at->toDateString() : now()->toDateString(),
            'entry_type' => $inventory->name,
            'gate'       => 'donation',
            'locked'     => false,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $inventory->id,
            'debit'            => $donation->estimated_value ?? 0,
            'credit'           => 0,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $donationsRevenue->id,
            'debit'            => 0,
            'credit'           => $donation->estimated_value ?? 0,
        ]);
    }

    private function getOrCreateAccount(string $code, string $name, string $type): Account
    {
        $acc = Account::where('code', $code)->first();
        if (!$acc) {
            $acc = Account::create(['code' => $code, 'name' => $name, 'type' => $type]);
        }
        return $acc;
    }
}
