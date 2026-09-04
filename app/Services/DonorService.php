<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donor;
use App\Models\Donation;
use App\Models\ChangeRequest;
use App\Models\BeneficiaryFamilyMember;
use App\Repositories\DonorRepository;
use App\Services\ChangeRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

final readonly class DonorService
{
    public function __construct(
        private DonorRepository $donorRepository
    ) {}

    public function searchDonors(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $q              = $filters['q'] ?? '';
        $type           = $filters['type'] ?? null;
        $classification = $filters['classification'] ?? null;
        $active         = $filters['active'] ?? null;

        $query = Donor::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                      ->orWhere('code', 'like', "%$q%")
                      ->orWhere('phone', 'like', "%$q%");
                });
            })
            ->when($type, fn($query, $t) => $query->where('type', $t))
            ->when($classification, fn($query, $c) => $query->where('classification', $c))
            ->when(!is_null($active) && $active !== '', fn($query, $a) => $query->where('active', (bool)$a))
            ->orderByDesc('id');

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->each(function (Donor $donor) {
            $donor->pendingRequest = \App\Models\ChangeRequest::where('model_type', Donor::class)
                ->where('model_id', $donor->id)
                ->where('status', 'pending')
                ->first();
        });

        return $paginator;
    }

    public function getAllDonors(): Collection
    {
        return $this->donorRepository->all();
    }

    public function findDonorById(int $id): ?Donor
    {
        return $this->donorRepository->findById($id);
    }

    public function createDonor(array $data, bool $forceRequest = false): mixed
    {
        $email = isset($data['email']) ? trim((string)$data['email']) : null;
        $phone = isset($data['phone']) ? trim((string)$data['phone']) : null;

        $existing = $this->donorRepository->findByPhoneOrEmail($phone, $email);

        if ($existing) {
            $updateData = array_filter($data, fn($v) => !is_null($v));
            return $this->updateDonor($existing, $updateData, $forceRequest);
        }

        if (empty($data['code'])) {
            $data['code'] = $this->nextDonorCode();
        }

        $submittedBeneficiaryIds = $data['sponsored_beneficiary_ids']
            ?? (!empty($data['sponsored_beneficiary_id']) ? [$data['sponsored_beneficiary_id']] : []);
        $sponsoredFamilyMemberIds = $this->normaliseBeneficiaryIds($data['sponsored_family_member_ids'] ?? []);
        $sponsoredBeneficiaryIds = $this->beneficiaryIdsForAssignments($submittedBeneficiaryIds, $sponsoredFamilyMemberIds);
        $syncSponsoredBeneficiaries = (bool) ($data['sync_sponsored_beneficiaries'] ?? false);
        $syncSponsoredFamilyMembers = (bool) ($data['sync_sponsored_family_members'] ?? false);
        $modelData = Arr::except($data, [
            'sponsored_beneficiary_ids', 'sync_sponsored_beneficiaries',
            'sponsored_family_member_ids', 'sync_sponsored_family_members',
        ]);

        $this->applySponsorshipDefaults($modelData);
        $modelData['sponsored_beneficiary_id'] = $sponsoredBeneficiaryIds[0] ?? null;
        if (($modelData['classification'] ?? null) === 'recurring'
            && ($modelData['recurring_cycle'] ?? null) === 'monthly') {
            $modelData['allocation_type'] = $sponsoredBeneficiaryIds !== [] ? 'sponsorship' : null;
        }

        $payload = array_merge($modelData, [
            'sponsored_beneficiary_ids' => $sponsoredBeneficiaryIds,
            'sync_sponsored_beneficiaries' => $syncSponsoredBeneficiaries,
            'sponsored_family_member_ids' => $sponsoredFamilyMemberIds,
            'sync_sponsored_family_members' => $syncSponsoredFamilyMembers,
        ]);

        $executor = function () use ($modelData, $sponsoredBeneficiaryIds, $syncSponsoredBeneficiaries, $sponsoredFamilyMemberIds, $syncSponsoredFamilyMembers) {
            $donor = $this->donorRepository->create($modelData);
            if ($syncSponsoredBeneficiaries) {
                $donor->sponsoredBeneficiaries()->sync($sponsoredBeneficiaryIds);
            }
            if ($syncSponsoredFamilyMembers) {
                $donor->sponsoredFamilyMembers()->sync($sponsoredFamilyMemberIds);
            }
            return $donor;
        };

        return ChangeRequestService::handleRequest(
            Donor::class,
            null,
            'create',
            $payload,
            $executor,
            $forceRequest
        );
    }

    public function updateDonor(Donor $donor, array $data, bool $forceRequest = true): mixed
    {
        $syncSponsoredBeneficiaries = (bool) ($data['sync_sponsored_beneficiaries'] ?? false);
        $syncSponsoredFamilyMembers = (bool) ($data['sync_sponsored_family_members'] ?? false);
        $sponsoredFamilyMemberIds = $syncSponsoredFamilyMembers
            ? $this->normaliseBeneficiaryIds($data['sponsored_family_member_ids'] ?? [])
            : null;
        $submittedBeneficiaryIds = $data['sponsored_beneficiary_ids']
            ?? (!empty($data['sponsored_beneficiary_id']) ? [$data['sponsored_beneficiary_id']] : []);
        $sponsoredBeneficiaryIds = $syncSponsoredBeneficiaries
            ? $this->beneficiaryIdsForAssignments($submittedBeneficiaryIds, $sponsoredFamilyMemberIds ?? [])
            : null;
        $modelData = Arr::except($data, [
            'code', 'sponsored_beneficiary_ids', 'sync_sponsored_beneficiaries',
            'sponsored_family_member_ids', 'sync_sponsored_family_members',
        ]);

        if (empty($donor->code)) {
            $modelData['code'] = Donor::nextAvailableCode();
        }

        $this->applySponsorshipDefaults($modelData, $donor);
        if ($syncSponsoredBeneficiaries) {
            $modelData['sponsored_beneficiary_id'] = $sponsoredBeneficiaryIds[0] ?? null;
        }
        if (($modelData['classification'] ?? $donor->classification) === 'recurring'
            && ($modelData['recurring_cycle'] ?? $donor->recurring_cycle) === 'monthly') {
            $currentBeneficiaryIds = $donor->sponsoredBeneficiaries()
                ->pluck('beneficiaries.id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $modelData['allocation_type'] = ($sponsoredBeneficiaryIds ?? $currentBeneficiaryIds) !== []
                ? 'sponsorship'
                : null;
        } elseif (($modelData['allocation_type'] ?? $donor->allocation_type) === 'sponsorship') {
            $modelData['allocation_type'] = null;
        }

        $payload = array_merge($modelData, [
            'sponsored_beneficiary_ids' => $sponsoredBeneficiaryIds ?? [],
            'sync_sponsored_beneficiaries' => $syncSponsoredBeneficiaries,
            'sponsored_family_member_ids' => $sponsoredFamilyMemberIds ?? [],
            'sync_sponsored_family_members' => $syncSponsoredFamilyMembers,
        ]);

        $executor = function () use ($donor, $modelData, $sponsoredBeneficiaryIds, $sponsoredFamilyMemberIds) {
            $this->donorRepository->update($donor, $modelData);
            if (!is_null($sponsoredBeneficiaryIds)) {
                $donor->sponsoredBeneficiaries()->sync($sponsoredBeneficiaryIds);
            }
            if (!is_null($sponsoredFamilyMemberIds)) {
                $donor->sponsoredFamilyMembers()->sync($sponsoredFamilyMemberIds);
            }
            return $donor;
        };

        return ChangeRequestService::handleRequest(
            Donor::class,
            $donor->id,
            'update',
            $payload,
            $executor,
            $forceRequest
        );
    }

    public function deleteDonor(Donor $donor): mixed
    {
        $executor = fn() => $this->donorRepository->delete($donor);

        return ChangeRequestService::handleRequest(
            Donor::class,
            $donor->id,
            'delete',
            ['note' => 'حذف متبرع'],
            $executor,
            true
        );
    }

    public function getDonorStats(int $donorId): array
    {
        $stats = Donation::select(DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->where('donor_id', $donorId)
            ->where('status', '!=', 'cancelled')
            ->first();

        return [
            'count' => (int)($stats->count ?? 0),
            'total' => (float)($stats->total ?? 0.0)
        ];
    }

    public function getPaidThisMonth(Donor $donor): float
    {
        return (float) Donation::where('donor_id', $donor->id)
            ->where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->when($donor->sponsorship_project_id, fn($q) => $q->where('project_id', $donor->sponsorship_project_id))
            ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
    }

    public function getGlobalStats(): array
    {
        return [
            'all'       => Donor::count(),
            'active'    => Donor::where('active', true)->count(),
            'recurring' => Donor::where('classification', 'recurring')->count(),
            'one_time'  => Donor::where('classification', 'one_time')->count(),
        ];
    }

    private function applySponsorshipDefaults(array &$data, ?Donor $donor = null): void
    {
        $classification = $data['classification'] ?? $donor?->classification;
        $cycle = $data['recurring_cycle'] ?? $donor?->recurring_cycle;

        if ($classification === 'recurring' && $cycle === 'monthly') {
            $data['sponsorship_type'] = 'monthly_sponsor';
        } elseif (($data['sponsorship_type'] ?? null) !== 'sadaqa_jariya') {
            $data['sponsorship_type'] = 'none';
        }

        if (array_key_exists('monthly_allocation_target', $data)
            && trim((string) $data['monthly_allocation_target']) === '') {
            $data['monthly_allocation_target'] = null;
        }
    }

    public function syncSponsoredBeneficiaries(Donor $donor, array $data): void
    {
        $familyMemberIds = $this->normaliseBeneficiaryIds($data['sponsored_family_member_ids'] ?? []);

        if ($data['sync_sponsored_family_members'] ?? false) {
            $donor->sponsoredFamilyMembers()->sync($familyMemberIds);
        }

        if ($data['sync_sponsored_beneficiaries'] ?? false) {
            $donor->sponsoredBeneficiaries()->sync($this->beneficiaryIdsForAssignments(
                $data['sponsored_beneficiary_ids'] ?? [],
                $familyMemberIds
            ));
        }
    }

    private function normaliseBeneficiaryIds(array $ids): array
    {
        return array_values(array_unique(array_map('intval', array_filter($ids))));
    }

    private function beneficiaryIdsForAssignments(array $beneficiaryIds, array $familyMemberIds): array
    {
        $derivedIds = $familyMemberIds === []
            ? []
            : BeneficiaryFamilyMember::query()
                ->whereIn('id', $familyMemberIds)
                ->pluck('beneficiary_id')
                ->all();

        return $this->normaliseBeneficiaryIds(array_merge($beneficiaryIds, $derivedIds));
    }

    private function nextDonorCode(): string
    {
        return Donor::nextAvailableCode();
    }
}
