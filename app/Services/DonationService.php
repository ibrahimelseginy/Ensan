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

        if (empty($familyMemberIds) && in_array($data['purpose'] ?? '', ['kafalat_aytam', 'kafalat_awram'], true)) {
            $allocations = (array) ($data['beneficiary_allocations'] ?? []);
            $beneficiaryIds = [];
            foreach ($allocations as $alloc) {
                if (!empty($alloc['family_member_id'])) {
                    $familyMemberIds[] = (int) $alloc['family_member_id'];
                }
                if (!empty($alloc['beneficiary_id'])) {
                    $beneficiaryIds[] = (int) $alloc['beneficiary_id'];
                }
            }
            if (!empty($data['beneficiary_id'])) {
                $beneficiaryIds[] = (int) $data['beneficiary_id'];
            }
            if (!empty($data['beneficiary_ids']) && is_array($data['beneficiary_ids'])) {
                foreach ($data['beneficiary_ids'] as $bId) {
                    if (!empty($bId)) {
                        $beneficiaryIds[] = (int) $bId;
                    }
                }
            }
            $beneficiaryIds = array_values(array_unique(array_filter($beneficiaryIds)));

            if (empty($familyMemberIds) && !empty($beneficiaryIds)) {
                $query = BeneficiaryFamilyMember::query()
                    ->whereIn('beneficiary_id', $beneficiaryIds)
                    ->where('active', true);

                if (($data['purpose'] ?? '') === 'kafalat_awram') {
                    $familyMemberIds = (clone $query)->where('is_patient', true)->pluck('id')->all();
                    if (empty($familyMemberIds)) {
                        $familyMemberIds = $query->pluck('id')->all();
                    }
                } else {
                    $familyMemberIds = (clone $query)->where('is_patient', false)->where('relationship', 'child')->pluck('id')->all();
                    if (empty($familyMemberIds)) {
                        $familyMemberIds = (clone $query)->where('relationship', 'child')->pluck('id')->all();
                    }
                    if (empty($familyMemberIds)) {
                        $familyMemberIds = $query->pluck('id')->all();
                    }
                }
            }
            $familyMemberIds = array_values(array_unique(array_filter($familyMemberIds)));
        }

        // 1. Handle Inline Donor Creation
        if (empty($data['donor_id']) && !empty($data['new_donor_name'])) {
            $classification = $data['new_donor_classification'] ?? 'one_time';
            $donor = $this->donorRepository->create([
                'code'                       => $data['new_donor_code'] ?? null,
                'name'                       => $data['new_donor_name'],
                'type'                       => 'individual',
                'phone'                      => $data['new_donor_phone'] ?? null,
                'address'                    => $data['new_donor_address'] ?? null,
                'classification'             => $classification,
                'recurring_cycle'            => $classification === 'recurring' ? ($data['new_donor_cycle'] ?? 'monthly') : null,
                'monthly_donation_day'       => !empty($data['new_donor_monthly_day']) ? (int)$data['new_donor_monthly_day'] : null,
                'sponsorship_monthly_amount' => !empty($data['new_donor_sponsorship_amount']) ? (float)$data['new_donor_sponsorship_amount'] : null,
                'sponsored_beneficiary_id'   => !empty($data['sponsorship_beneficiary_id']) ? (int)$data['sponsorship_beneficiary_id'] : (!empty($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : null),
                'active'                     => true
            ]);
            $data['donor_id'] = $donor->id;
        }

        // Cleanup temporary donor fields
        unset(
            $data['new_donor_code'],
            $data['new_donor_name'],
            $data['new_donor_phone'],
            $data['new_donor_phone_country'],
            $data['_new_donor_phone_national'],
            $data['new_donor_national_id'],
            $data['new_donor_address'],
            $data['new_donor_classification'],
            $data['new_donor_cycle'],
            $data['new_donor_monthly_day'],
            $data['new_donor_sponsorship_amount'],
            $data['sponsorship_beneficiary_id']
        );

        // Process Split Allocations (Projects, Campaigns, Guest Houses, Beneficiaries, General)
        if (!empty($data['split_allocations']) && is_array($data['split_allocations'])) {
            $formattedSplits = [];
            $splitBenAllocations = [];
            $firstProjectId = null;
            $firstCampaignId = null;
            $firstGuestHouseId = null;

            foreach ($data['split_allocations'] as $split) {
                $type = $split['type'] ?? 'general';
                $amt = !empty($split['amount']) ? (float)$split['amount'] : 0;
                if ($amt <= 0) continue;

                $targetId = !empty($split['target_id']) ? (int)$split['target_id'] : null;
                $purpose = !empty($split['purpose']) ? (string)$split['purpose'] : ($data['purpose'] ?? null);
                $notes = !empty($split['notes']) ? (string)$split['notes'] : null;

                $targetName = null;
                $typeLabel = match ($type) {
                    'project'     => 'مشروع',
                    'campaign'    => 'حملة نشطة',
                    'guest_house' => 'دار ضيافة',
                    'beneficiary' => 'حالة مستفيدة',
                    default       => 'عام / غير مخصص'
                };

                if ($type === 'project' && $targetId) {
                    $proj = \App\Models\Project::find($targetId);
                    $targetName = $proj?->name ?? ('مشروع #' . $targetId);
                    if (!$firstProjectId) $firstProjectId = $targetId;
                } elseif ($type === 'campaign' && $targetId) {
                    $camp = \App\Models\Campaign::find($targetId);
                    $targetName = $camp?->name ?? ('حملة #' . $targetId);
                    if (!$firstCampaignId) $firstCampaignId = $targetId;
                } elseif ($type === 'guest_house' && $targetId) {
                    $gh = \App\Models\GuestHouse::find($targetId);
                    $targetName = $gh?->name ?? ('دار ضيافة #' . $targetId);
                    if (!$firstGuestHouseId) $firstGuestHouseId = $targetId;
                } elseif ($type === 'beneficiary' && $targetId) {
                    $ben = Beneficiary::find($targetId);
                    $targetName = $ben?->full_name ?? ('مستفيد #' . $targetId);
                    $splitBenAllocations[] = [
                        'beneficiary_id' => $targetId,
                        'name'           => $ben?->full_name,
                        'code'           => $ben?->code ?? ('BEN-' . $targetId),
                        'phone'          => $ben?->phone,
                        'amount'         => $amt,
                        'notes'          => $notes,
                    ];
                }

                $formattedSplits[] = [
                    'type'        => $type,
                    'type_label'  => $typeLabel,
                    'target_id'   => $targetId,
                    'target_name' => $targetName,
                    'purpose'     => $purpose,
                    'amount'      => $amt,
                    'notes'       => $notes,
                ];
            }

            if (!empty($formattedSplits)) {
                $data['split_allocations'] = $formattedSplits;

                if (empty($data['project_id']) && $firstProjectId) {
                    $data['project_id'] = $firstProjectId;
                }
                if (empty($data['campaign_id']) && $firstCampaignId) {
                    $data['campaign_id'] = $firstCampaignId;
                }
                if (empty($data['guest_house_id']) && $firstGuestHouseId) {
                    $data['guest_house_id'] = $firstGuestHouseId;
                }

                if (!empty($splitBenAllocations) && empty($data['beneficiary_allocations'])) {
                    $data['beneficiary_allocations'] = $splitBenAllocations;
                    if (empty($data['beneficiary_id'])) {
                        $data['beneficiary_id'] = $splitBenAllocations[0]['beneficiary_id'];
                    }
                }
            } else {
                $data['split_allocations'] = null;
            }
        }

        // Process Multi-Beneficiary Allocations
        if (!empty($data['beneficiary_allocations']) && is_array($data['beneficiary_allocations'])) {
            $formattedAllocations = [];
            $allocBenIds = [];
            $allocMemberIds = [];
            foreach ($data['beneficiary_allocations'] as $alloc) {
                if (empty($alloc['beneficiary_id'])) continue;
                $bId = (int)$alloc['beneficiary_id'];
                $mId = !empty($alloc['family_member_id']) ? (int)$alloc['family_member_id'] : null;
                $allocBenIds[] = $bId;
                if ($mId) {
                    $allocMemberIds[] = $mId;
                }
                $formattedAllocations[] = [
                    'beneficiary_id'   => $bId,
                    'family_member_id' => $mId,
                    'amount'           => !empty($alloc['amount']) ? (float)$alloc['amount'] : 0,
                    'notes'            => !empty($alloc['notes']) ? (string)$alloc['notes'] : null,
                ];
            }

            if (!empty($formattedAllocations)) {
                $bens = Beneficiary::whereIn('id', array_unique($allocBenIds))->get()->keyBy('id');
                $members = !empty($allocMemberIds)
                    ? BeneficiaryFamilyMember::whereIn('id', array_unique($allocMemberIds))->get()->keyBy('id')
                    : collect();

                $finalAllocations = [];
                foreach ($formattedAllocations as $item) {
                    $bId = $item['beneficiary_id'];
                    $mId = $item['family_member_id'];
                    $ben = $bens->get($bId);
                    $member = $mId ? $members->get($mId) : null;

                    $displayName = $member ? $member->full_name . ' (أسرة: ' . ($ben?->full_name ?? '') . ')' : ($ben?->full_name ?? ('مستفيد #' . $bId));
                    $displayCode = $member?->code ?: ($ben?->code ?: ('BEN-' . $bId));
                    $displayPhone = $member?->phone ?: $ben?->phone;

                    $finalAllocations[] = [
                        'beneficiary_id'   => $bId,
                        'family_member_id' => $mId,
                        'name'             => $displayName,
                        'code'             => $displayCode,
                        'phone'            => $displayPhone,
                        'amount'           => $item['amount'],
                        'notes'            => $item['notes'],
                    ];
                }
                $data['beneficiary_allocations'] = $finalAllocations;

                if (empty($data['beneficiary_id'])) {
                    $data['beneficiary_id'] = $allocBenIds[0];
                }

                $existingBenIds = !empty($data['beneficiary_ids']) && is_array($data['beneficiary_ids']) ? $data['beneficiary_ids'] : [];
                $data['beneficiary_ids'] = array_values(array_unique(array_merge($existingBenIds, $allocBenIds)));
            } else {
                $data['beneficiary_allocations'] = null;
            }
        }

        // Link explicit beneficiary_id to donationable polymorphic fields if empty
        if (!empty($data['beneficiary_id'])) {
            $data['beneficiary_id'] = (int)$data['beneficiary_id'];
            if (empty($data['donationable_id'])) {
                $data['donationable_type'] = Beneficiary::class;
                $data['donationable_id'] = $data['beneficiary_id'];
            }
        }

        if (array_key_exists('add_to_inventory', $data)) {
            $data['auto_added_to_inventory'] = (bool) $data['add_to_inventory'];
            unset($data['add_to_inventory']);
        }

        if (isset($data['transfer_receipt']) && $data['transfer_receipt'] instanceof \Illuminate\Http\UploadedFile) {
            $data['transfer_receipt_path'] = $data['transfer_receipt']->store('donation_receipts', 'public');
            unset($data['transfer_receipt']);
        }

        // 2. Handle Allocation Note Magic Strings
        $this->processAllocationNotes($data);

        if (empty($data['received_at'])) {
            $data['received_at'] = now();
        }
        if (empty($data['created_by']) && auth()->check()) {
            $data['created_by'] = auth()->id();
        }
        if (empty($data['user_id']) && auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $executor = function () use ($data, $familyMemberIds) {
            $donation = $this->donationRepository->create($data);
            
            if ($donation->type === 'cash' && !empty($data['treasury_id'])) {
                $this->treasuryIntegrationService->processDonationToTreasury($donation, (int)$data['treasury_id']);
                $this->linkDonorToBeneficiaryFromDonation($donation);
            } else {
                $this->processPostCreate($donation);
            }

            // Sync direct beneficiary linking with donor (supports multi-case allocations)
            if (!empty($donation->donor_id)) {
                $allAllocBenIds = [];
                if (!empty($donation->beneficiary_allocations) && is_array($donation->beneficiary_allocations)) {
                    foreach ($donation->beneficiary_allocations as $alloc) {
                        if (!empty($alloc['beneficiary_id'])) {
                            $allAllocBenIds[] = (int)$alloc['beneficiary_id'];
                        }
                    }
                }
                if (!empty($donation->beneficiary_id)) {
                    $allAllocBenIds[] = (int)$donation->beneficiary_id;
                }
                $allAllocBenIds = array_values(array_unique($allAllocBenIds));

                if (!empty($allAllocBenIds)) {
                    $donor = Donor::find($donation->donor_id);
                    if ($donor) {
                        $donor->sponsoredBeneficiaries()->syncWithoutDetaching($allAllocBenIds);
                        if (!$donor->sponsored_beneficiary_id) {
                            $donor->forceFill(['sponsored_beneficiary_id' => $allAllocBenIds[0]])->save();
                        }
                    }
                }
            }

            // If paid months are specified, calculate and advance the next due date for the donor
            if (!empty($data['paid_months']) && is_array($data['paid_months']) && !empty($donation->donor_id)) {
                $donor = Donor::find($donation->donor_id);
                if ($donor) {
                    $sortedMonths = array_values(array_filter($data['paid_months']));
                    sort($sortedMonths);
                    $latestMonth = end($sortedMonths);
                    if ($latestMonth && preg_match('/^\d{4}-\d{2}$/', $latestMonth)) {
                        $day = (int)($donor->monthly_donation_day ?: ($donation->received_at ? $donation->received_at->day : 1));
                        $nextDate = \Carbon\Carbon::parse($latestMonth . '-01')->addMonth();
                        $targetDay = min(max($day, 1), $nextDate->daysInMonth);
                        $nextDate->day($targetDay)->startOfDay();
                        $donor->next_due_date = $nextDate->toDateString();
                        $donor->save();
                    }
                }
            }

            $this->linkDonationToFamilyMembers($donation, $familyMemberIds);

            if (!empty($donation->split_allocations) && is_array($donation->split_allocations)) {
                foreach ($donation->split_allocations as $split) {
                    if (($split['type'] ?? '') === 'campaign' && !empty($split['target_id'])) {
                        $camp = \App\Models\Campaign::find($split['target_id']);
                        if ($camp) {
                            $camp->increment('current_amount', (float)($split['amount'] ?? 0));
                        }
                    }
                }
            } elseif (!empty($donation->campaign_id)) {
                $campaign = \App\Models\Campaign::find($donation->campaign_id);
                if ($campaign) {
                    $val = $donation->type === 'cash' ? (float)$donation->amount : (float)$donation->estimated_value;
                    $campaign->increment('current_amount', $val);
                }
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

            if (!empty($donation->split_allocations) && is_array($donation->split_allocations)) {
                foreach ($donation->split_allocations as $split) {
                    if (($split['type'] ?? '') === 'campaign' && !empty($split['target_id'])) {
                        $camp = \App\Models\Campaign::find($split['target_id']);
                        if ($camp) {
                            $dec = (float)($split['amount'] ?? 0);
                            $camp->decrement('current_amount', min((float)$camp->current_amount, $dec));
                        }
                    }
                }
            } elseif (!empty($donation->campaign_id)) {
                $campaign = \App\Models\Campaign::find($donation->campaign_id);
                if ($campaign) {
                    $val = $donation->type === 'cash' ? (float)$donation->amount : (float)$donation->estimated_value;
                    $campaign->decrement('current_amount', min((float)$campaign->current_amount, $val));
                }
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
