<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamilyMember;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\InventoryTransaction;
use App\Repositories\DonationRepository;
use App\Repositories\DonorRepository;
use App\Services\ChangeRequestService;
use App\Services\TreasuryIntegrationService;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $familyMemberIds = array_values(array_unique(array_map(
            'intval',
            array_filter((array) ($data['family_member_ids'] ?? []))
        )));
        unset($data['family_member_ids']);

        // 1. Handle Inline Donor Creation
        if (empty($data['donor_id']) && !empty($data['new_donor_name'])) {
            $donor = $this->donorRepository->create([
                'code'            => $data['new_donor_code'] ?? null,
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
        unset($data['new_donor_code'], $data['new_donor_name'], $data['new_donor_phone'], $data['new_donor_address'], $data['new_donor_classification'], $data['new_donor_cycle']);

        if (array_key_exists('add_to_inventory', $data)) {
            $data['auto_added_to_inventory'] = (bool) $data['add_to_inventory'];
            unset($data['add_to_inventory']);
        }

        // 2. Handle Allocation Note Magic Strings
        $this->processAllocationNotes($data);

        // 3. Prepare default values
        if (empty($data['received_at'])) {
            $data['received_at'] = now();
        }

        $executor = function () use ($data, $familyMemberIds) {
            $donation = $this->donationRepository->create($data);
            
            if ($donation->type === 'cash' && !empty($data['treasury_id'])) {
                $this->treasuryIntegrationService->processDonationToTreasury($donation, (int)$data['treasury_id']);
                $this->linkDonorToBeneficiaryFromDonation($donation);
            } else {
                $this->processPostCreate($donation);
            }

            $this->linkDonationToFamilyMembers($donation, $familyMemberIds);
            
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
            $this->linkDonorToBeneficiaryFromDonation($donation->fresh());
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

        $benIds = [];
        if (!empty($data['beneficiary_ids']) && is_array($data['beneficiary_ids'])) {
            $benIds = array_map('intval', array_filter($data['beneficiary_ids']));
        } elseif (!empty($data['beneficiary_id'])) {
            $benIds = [(int)$data['beneficiary_id']];
        }

        $benString = !empty($benIds) ? implode(',', $benIds) : null;
        $familyMemberId = !empty($data['family_member_id']) ? (int) $data['family_member_id'] : null;

        if ($allocType === 'sadaqa_jariya' && !str_contains($currentNote, 'sponsorship=')) {
            $data['allocation_note'] = trim("sponsorship=sadaqa_jariya\n" . $currentNote);
        } elseif ($allocType === 'sponsorship' && !str_contains($currentNote, 'sponsorship=')) {
            $sType = $data['sponsorship_type'] ?? 'طفل';
            $magic = "sponsorship={$sType}" . ($benString ? ";beneficiary_ids={$benString}" : "");
            if ($familyMemberId) {
                $magic .= ";family_member_id={$familyMemberId}";
            }
            $data['allocation_note'] = trim($magic . "\n" . $currentNote);
        }

        if (!empty($data['sponsorship_kind']) && !str_contains($currentNote, 'sponsorship=')) {
            $sKind = $data['sponsorship_kind'];
            $note  = "sponsorship=" . $sKind;
            if ($benString) {
                $note .= ";beneficiary_ids=" . $benString;
            }
            if ($familyMemberId) {
                $note .= ";family_member_id=" . $familyMemberId;
            }
            $data['allocation_note'] = trim($note . "\n" . $currentNote);
        }

        unset($data['allocation_type'], $data['sponsorship_kind'], $data['sponsorship_type'], $data['beneficiary_ids'], $data['family_member_id']);
    }

    public function processPostCreate(Donation $donation): void
    {
        $this->linkDonorToBeneficiaryFromDonation($donation);

        if ($donation->type === 'cash') {
            $this->createCashJournal($donation);
        } else {
            $this->createInKindJournal($donation);
            $this->addInKindDonationToInventory($donation);
        }
    }

    private function linkDonorToBeneficiaryFromDonation(Donation $donation): void
    {
        if (! $donation->donor_id || ! $donation->allocation_note) {
            return;
        }

        $beneficiaryIds = [];
        if (preg_match('/(?:^|;)beneficiary_ids=([\d,]+)/m', (string) $donation->allocation_note, $matches)) {
            $beneficiaryIds = array_map('intval', explode(',', $matches[1]));
        } elseif (preg_match('/(?:^|;)beneficiary_id=(\d+)/m', (string) $donation->allocation_note, $matches)) {
            $beneficiaryIds = [(int) $matches[1]];
        }

        $beneficiaryIds = array_filter($beneficiaryIds, fn($id) => $id > 0 && Beneficiary::whereKey($id)->exists());
        if (empty($beneficiaryIds)) {
            return;
        }

        $donor = Donor::find($donation->donor_id);
        if (! $donor) {
            return;
        }

        $donor->sponsoredBeneficiaries()->syncWithoutDetaching($beneficiaryIds);

        if (preg_match('/(?:^|;)family_member_id=(\d+)/m', (string) $donation->allocation_note, $memberMatches)) {
            $familyMember = BeneficiaryFamilyMember::query()
                ->whereKey((int) $memberMatches[1])
                ->whereIn('beneficiary_id', $beneficiaryIds)
                ->first();

            $familyMember?->sponsors()->syncWithoutDetaching([$donor->id]);
        }

        if (! $donor->sponsored_beneficiary_id) {
            $donor->forceFill(['sponsored_beneficiary_id' => reset($beneficiaryIds)])->save();
        }
    }

    private function linkDonationToFamilyMembers(Donation $donation, array $familyMemberIds): void
    {
        if ($familyMemberIds === [] || ! $donation->donor_id) {
            return;
        }

        $members = BeneficiaryFamilyMember::query()
            ->whereIn('id', $familyMemberIds)
            ->where('active', true)
            ->get();

        if ($members->isEmpty()) {
            return;
        }

        $donation->familyMembers()->sync($members->modelKeys());

        $donor = Donor::find($donation->donor_id);
        if (! $donor) {
            return;
        }

        $donor->sponsoredFamilyMembers()->syncWithoutDetaching($members->modelKeys());
        $donor->sponsoredBeneficiaries()->syncWithoutDetaching(
            $members->pluck('beneficiary_id')->unique()->values()->all()
        );
    }

    private function addInKindDonationToInventory(Donation $donation): void
    {
        if (! $donation->auto_added_to_inventory || ! $donation->warehouse_id || ! $donation->item_id || ! $donation->quantity) {
            return;
        }

        InventoryTransaction::firstOrCreate(
            ['source_donation_id' => $donation->id, 'type' => 'in'],
            [
                'item_id' => $donation->item_id,
                'warehouse_id' => $donation->warehouse_id,
                'guest_house_id' => $donation->guest_house_id,
                'quantity' => $donation->quantity,
                'unit_cost' => $donation->quantity > 0 ? ((float) $donation->estimated_value / (float) $donation->quantity) : 0,
                'total_cost' => $donation->estimated_value,
                'reference' => 'GH-DON-' . $donation->id,
                'notes' => 'تبرع عيني مضاف تلقائياً إلى مخزون دار الضيافة',
                'transaction_date' => optional($donation->received_at)->toDateString() ?: now()->toDateString(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'user_id' => auth()->id(),
            ]
        );
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
