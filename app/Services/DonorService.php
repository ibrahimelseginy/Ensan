<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\ArabicSearchHelper;
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
        $donationStatus = $filters['donation_status'] ?? null;

        $query = Donor::query()
            ->when($q !== '', function ($query) use ($q) {
                $parsed = ArabicSearchHelper::parseQuery($q);
                $numbers = $parsed['numbers'];

                $query->where(function ($w) use ($q, $numbers) {
                    ArabicSearchHelper::applySmartSearch(
                        $w,
                        $q,
                        ['name', 'address'],
                        ['code', 'phone', 'national_id', 'email']
                    );
                    $w->orWhereHas('sponsoredBeneficiaries', function ($sb) use ($q) {
                        ArabicSearchHelper::applySmartSearch(
                            $sb,
                            $q,
                            ['full_name', 'patient_name'],
                            ['code', 'national_id', 'phone']
                        );
                    });
                    $w->orWhereHas('sponsoredFamilyMembers', function ($sfm) use ($q) {
                        ArabicSearchHelper::applySmartSearch(
                            $sfm,
                            $q,
                            ['full_name'],
                            ['code', 'national_id', 'phone']
                        );
                    });
                    foreach ($numbers as $num) {
                        if (strlen($num) <= 7) {
                            $w->orWhere('id', (int) $num);
                        }
                    }
                });
            })
            ->when($type, fn($query, $t) => $query->where('type', $t))
            ->when($classification, fn($query, $c) => $query->where('classification', $c))
            ->when(!is_null($active) && $active !== '', fn($query, $a) => $query->where('active', (bool)$a))
            ->when($donationStatus === 'never', fn($query) => $query->whereDoesntHave('donations', fn($d) => $d->where('status', '!=', 'cancelled')))
            ->when($donationStatus === 'donated', fn($query) => $query->whereHas('donations', fn($d) => $d->where('status', '!=', 'cancelled')))
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

        $submittedBeneficiaryIds = $this->normaliseBeneficiaryIds($data['sponsored_beneficiary_ids']
            ?? (!empty($data['sponsored_beneficiary_id']) ? [$data['sponsored_beneficiary_id']] : []));
        $sponsoredFamilyMemberIds = $this->normaliseBeneficiaryIds($data['sponsored_family_member_ids'] ?? []);
        $beneficiaryAmounts = is_array($data['sponsorship_beneficiary_amounts'] ?? null) ? $data['sponsorship_beneficiary_amounts'] : [];
        $familyMemberAmounts = is_array($data['sponsorship_family_member_amounts'] ?? null) ? $data['sponsorship_family_member_amounts'] : [];
        $sponsoredBeneficiaryIds = $this->beneficiaryIdsForAssignments($submittedBeneficiaryIds, $sponsoredFamilyMemberIds);
        $syncSponsoredBeneficiaries = (bool) ($data['sync_sponsored_beneficiaries'] ?? false);
        $syncSponsoredFamilyMembers = (bool) ($data['sync_sponsored_family_members'] ?? false);
        $modelData = Arr::except($data, [
            'sponsored_beneficiary_ids', 'sync_sponsored_beneficiaries',
            'sponsorship_beneficiary_amounts',
            'sponsored_family_member_ids', 'sync_sponsored_family_members',
            'sponsorship_family_member_amounts',
            'phone_country', '_phone_national',
        ]);

        $calculatedTotal = $this->calculateTotalFromCaseAmounts(
            $submittedBeneficiaryIds,
            $beneficiaryAmounts,
            $sponsoredFamilyMemberIds,
            $familyMemberAmounts
        );
        // الأولوية دائماً للمبلغ الذي كتبه المستخدم يدوياً إذا وُجد (مثلاً متبرع بـ 1500 وباقي الحالات لم تُسجل بعد)
        $userEnteredAmount = (float) ($modelData['sponsorship_monthly_amount'] ?? 0);
        if ($userEnteredAmount > 0) {
            $finalAmount = $userEnteredAmount;
        } elseif ($calculatedTotal > 0) {
            $finalAmount = $calculatedTotal;
        } else {
            $finalAmount = 0.0;
        }
        if ($finalAmount > 0) {
            $modelData['sponsorship_monthly_amount'] = $finalAmount;
        }

        $this->applySponsorshipDefaults($modelData);
        $modelData['sponsored_beneficiary_id'] = $sponsoredBeneficiaryIds[0] ?? null;
        if (($modelData['classification'] ?? null) === 'recurring'
            && ($modelData['recurring_cycle'] ?? null) === 'monthly') {
            $modelData['allocation_type'] = $sponsoredBeneficiaryIds !== [] ? 'sponsorship' : null;
        }

        $payload = array_merge($modelData, [
            'sponsored_beneficiary_ids' => $sponsoredBeneficiaryIds,
            'direct_sponsored_beneficiary_ids' => $submittedBeneficiaryIds,
            'sponsorship_beneficiary_amounts' => $beneficiaryAmounts,
            'sync_sponsored_beneficiaries' => $syncSponsoredBeneficiaries,
            'sponsored_family_member_ids' => $sponsoredFamilyMemberIds,
            'sponsorship_family_member_amounts' => $familyMemberAmounts,
            'sync_sponsored_family_members' => $syncSponsoredFamilyMembers,
        ]);

        $executor = function () use (
            $modelData,
            $sponsoredBeneficiaryIds,
            $submittedBeneficiaryIds,
            $beneficiaryAmounts,
            $syncSponsoredBeneficiaries,
            $sponsoredFamilyMemberIds,
            $familyMemberAmounts,
            $syncSponsoredFamilyMembers
        ) {
            $donor = $this->donorRepository->create($modelData);
            $totalMonthly = (float) ($modelData['sponsorship_monthly_amount'] ?? 0);
            if ($syncSponsoredFamilyMembers) {
                $this->syncFamilyMembersWithAmounts($donor, $sponsoredFamilyMemberIds, $familyMemberAmounts, $submittedBeneficiaryIds, $totalMonthly);
            }
            if ($syncSponsoredBeneficiaries) {
                $this->syncBeneficiariesWithAmounts($donor, $sponsoredBeneficiaryIds, $submittedBeneficiaryIds, $beneficiaryAmounts, $sponsoredFamilyMemberIds, $totalMonthly);
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
        $submittedBeneficiaryIds = $this->normaliseBeneficiaryIds($data['sponsored_beneficiary_ids']
            ?? (!empty($data['sponsored_beneficiary_id']) ? [$data['sponsored_beneficiary_id']] : []));
        $beneficiaryAmounts = is_array($data['sponsorship_beneficiary_amounts'] ?? null) ? $data['sponsorship_beneficiary_amounts'] : [];
        $familyMemberAmounts = is_array($data['sponsorship_family_member_amounts'] ?? null) ? $data['sponsorship_family_member_amounts'] : [];
        $sponsoredBeneficiaryIds = $syncSponsoredBeneficiaries
            ? $this->beneficiaryIdsForAssignments($submittedBeneficiaryIds, $sponsoredFamilyMemberIds ?? [])
            : null;
        $modelData = Arr::except($data, [
            'code', 'sponsored_beneficiary_ids', 'sync_sponsored_beneficiaries',
            'sponsorship_beneficiary_amounts',
            'sponsored_family_member_ids', 'sync_sponsored_family_members',
            'sponsorship_family_member_amounts',
            'phone_country', '_phone_national',
        ]);

        if (empty($donor->code)) {
            $modelData['code'] = Donor::nextAvailableCode();
        }

        $calculatedTotal = $this->calculateTotalFromCaseAmounts(
            $submittedBeneficiaryIds,
            $beneficiaryAmounts,
            $sponsoredFamilyMemberIds ?? [],
            $familyMemberAmounts
        );
        // الأولوية دائماً للمبلغ الذي كتبه المستخدم يدوياً إذا وُجد
        $userEnteredAmount = (float) ($modelData['sponsorship_monthly_amount'] ?? 0);
        if ($userEnteredAmount > 0) {
            $finalAmount = $userEnteredAmount;
        } elseif ($calculatedTotal > 0) {
            $finalAmount = $calculatedTotal;
        } else {
            $finalAmount = (float) ($donor->sponsorship_monthly_amount ?? 0);
        }
        if ($finalAmount > 0) {
            $modelData['sponsorship_monthly_amount'] = $finalAmount;
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
            if (($sponsoredBeneficiaryIds ?? $currentBeneficiaryIds) !== [] || !empty($modelData['sponsorship_monthly_amount'])) {
                $modelData['allocation_type'] = 'sponsorship';
                $modelData['sponsorship_type'] = 'monthly_sponsor';
            }
        }

        $payload = array_merge($modelData, [
            'sponsored_beneficiary_ids' => $sponsoredBeneficiaryIds ?? [],
            'direct_sponsored_beneficiary_ids' => $submittedBeneficiaryIds,
            'sponsorship_beneficiary_amounts' => $beneficiaryAmounts,
            'sync_sponsored_beneficiaries' => $syncSponsoredBeneficiaries,
            'sponsored_family_member_ids' => $sponsoredFamilyMemberIds ?? [],
            'sponsorship_family_member_amounts' => $familyMemberAmounts,
            'sync_sponsored_family_members' => $syncSponsoredFamilyMembers,
        ]);

        $executor = function () use (
            $donor,
            $modelData,
            $sponsoredBeneficiaryIds,
            $submittedBeneficiaryIds,
            $beneficiaryAmounts,
            $sponsoredFamilyMemberIds,
            $familyMemberAmounts
        ) {
            $this->donorRepository->update($donor, $modelData);
            $totalMonthly = (float) ($modelData['sponsorship_monthly_amount'] ?? $donor->sponsorship_monthly_amount ?? 0);
            if (!is_null($sponsoredFamilyMemberIds)) {
                $this->syncFamilyMembersWithAmounts($donor, $sponsoredFamilyMemberIds, $familyMemberAmounts, $submittedBeneficiaryIds, $totalMonthly);
            }
            if (!is_null($sponsoredBeneficiaryIds)) {
                $this->syncBeneficiariesWithAmounts($donor, $sponsoredBeneficiaryIds, $submittedBeneficiaryIds, $beneficiaryAmounts, $sponsoredFamilyMemberIds ?? [], $totalMonthly);
            }
            return $donor;
        };

        $user = request()->user() ?? auth()->user();
        if ($user && ($user->hasRole('admin') || $user->hasRole('manager'))) {
            $forceRequest = false;
        }

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
        $user = request()->user() ?? auth()->user();
        $forceRequest = true;
        if ($user && ($user->hasRole('admin') || $user->hasRole('manager'))) {
            $forceRequest = false;
        }

        return ChangeRequestService::handleRequest(
            Donor::class,
            $donor->id,
            'delete',
            ['note' => 'حذف متبرع'],
            $executor,
            $forceRequest
        );
    }

    public function getDonorStats(int $donorId): object
    {
        $stats = Donation::select(DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->where('donor_id', $donorId)
            ->where('status', '!=', 'cancelled')
            ->first();

        return (object) [
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
            'all'           => Donor::count(),
            'active'        => Donor::where('active', true)->count(),
            'recurring'     => Donor::where('classification', 'recurring')->count(),
            'one_time'      => Donor::where('classification', 'one_time')->count(),
            'never_donated' => Donor::whereDoesntHave('donations', fn($q) => $q->where('status', '!=', 'cancelled'))->count(),
        ];
    }

    private function applySponsorshipDefaults(array &$data, ?Donor $donor = null): void
    {
        $classification = $data['classification'] ?? $donor?->classification;
        $cycle = $data['recurring_cycle'] ?? $donor?->recurring_cycle;

        if ($classification === 'recurring' && $cycle === 'monthly') {
            $data['sponsorship_type'] = 'monthly_sponsor';
        } elseif (!empty($data['sponsorship_monthly_amount']) || ($data['allocation_type'] ?? null) === 'sponsorship') {
            $data['sponsorship_type'] = 'monthly_sponsor';
        } elseif (($data['sponsorship_type'] ?? null) !== 'sadaqa_jariya') {
            $data['sponsorship_type'] = $data['sponsorship_type'] ?? $donor?->sponsorship_type ?? 'none';
        }

        if (array_key_exists('monthly_allocation_target', $data)
            && trim((string) $data['monthly_allocation_target']) === '') {
            $data['monthly_allocation_target'] = null;
        }
    }

    public function syncSponsoredBeneficiaries(Donor $donor, array $data): void
    {
        $familyMemberIds = $this->normaliseBeneficiaryIds($data['sponsored_family_member_ids'] ?? []);
        $directBeneficiaryIds = $this->normaliseBeneficiaryIds($data['direct_sponsored_beneficiary_ids'] ?? $data['sponsored_beneficiary_ids'] ?? []);
        $beneficiaryAmounts = is_array($data['sponsorship_beneficiary_amounts'] ?? null) ? $data['sponsorship_beneficiary_amounts'] : [];
        $familyMemberAmounts = is_array($data['sponsorship_family_member_amounts'] ?? null) ? $data['sponsorship_family_member_amounts'] : [];
        $totalMonthly = (float) ($data['sponsorship_monthly_amount'] ?? $donor->sponsorship_monthly_amount ?? 0);

        if ($data['sync_sponsored_family_members'] ?? false) {
            $this->syncFamilyMembersWithAmounts($donor, $familyMemberIds, $familyMemberAmounts, $directBeneficiaryIds, $totalMonthly);
        }

        if ($data['sync_sponsored_beneficiaries'] ?? false) {
            $allBeneficiaryIds = $this->beneficiaryIdsForAssignments($directBeneficiaryIds, $familyMemberIds);
            $this->syncBeneficiariesWithAmounts($donor, $allBeneficiaryIds, $directBeneficiaryIds, $beneficiaryAmounts, $familyMemberIds, $totalMonthly);
        }
    }

    private function calculateTotalFromCaseAmounts(
        array $directBeneficiaryIds,
        array $beneficiaryAmounts,
        array $familyMemberIds,
        array $familyMemberAmounts
    ): float {
        $sum = 0.0;
        foreach ($directBeneficiaryIds as $bId) {
            $val = isset($beneficiaryAmounts[$bId]) ? (float) $beneficiaryAmounts[$bId] : 0.0;
            if ($val > 0) {
                $sum += $val;
            }
        }
        foreach ($familyMemberIds as $fmId) {
            $val = isset($familyMemberAmounts[$fmId]) ? (float) $familyMemberAmounts[$fmId] : 0.0;
            if ($val > 0) {
                $sum += $val;
            }
        }

        return round($sum, 2);
    }

    private function syncFamilyMembersWithAmounts(
        Donor $donor,
        array $familyMemberIds,
        array $familyMemberAmounts,
        array $directBeneficiaryIds,
        float $totalMonthly
    ): void {
        if ($familyMemberIds === []) {
            $donor->sponsoredFamilyMembers()->sync([]);
            return;
        }

        $existingPivot = $donor->sponsoredFamilyMembers()
            ->pluck('family_member_sponsors.monthly_amount', 'beneficiary_family_members.id')
            ->all();

        $syncPayload = [];
        $count = count($familyMemberIds);
        foreach ($familyMemberIds as $fmId) {
            $explicitAmt = isset($familyMemberAmounts[$fmId]) && $familyMemberAmounts[$fmId] !== '' && $familyMemberAmounts[$fmId] !== null
                ? (float) $familyMemberAmounts[$fmId]
                : null;

            if ($explicitAmt === null || $explicitAmt <= 0) {
                if (isset($existingPivot[$fmId]) && (float) $existingPivot[$fmId] > 0) {
                    $explicitAmt = (float) $existingPivot[$fmId];
                } elseif ($count === 1 && $directBeneficiaryIds === [] && $totalMonthly > 0) {
                    $explicitAmt = $totalMonthly;
                }
            }

            $syncPayload[$fmId] = [
                'monthly_amount' => ($explicitAmt !== null && $explicitAmt > 0) ? round($explicitAmt, 2) : null,
            ];

            // If the family member has no required sponsorship_amount set at all yet, initialize it without overwriting existing required amounts
            if ($explicitAmt !== null && $explicitAmt > 0) {
                $member = BeneficiaryFamilyMember::find($fmId);
                if ($member && ((float) ($member->sponsorship_amount ?? 0)) <= 0) {
                    $member->update(['sponsorship_amount' => round($explicitAmt, 2)]);
                }
            }
        }

        $donor->sponsoredFamilyMembers()->sync($syncPayload);
    }

    private function syncBeneficiariesWithAmounts(
        Donor $donor,
        array $allBeneficiaryIds,
        array $directBeneficiaryIds,
        array $beneficiaryAmounts,
        array $familyMemberIds,
        float $totalMonthly
    ): void {
        if ($allBeneficiaryIds === []) {
            $donor->sponsoredBeneficiaries()->sync([]);
            return;
        }

        if (!Donor::hasBeneficiarySponsorPivotColumns()) {
            $donor->sponsoredBeneficiaries()->sync($allBeneficiaryIds);
            return;
        }

        $existingPivot = $donor->sponsoredBeneficiaries()
            ->pluck('beneficiary_sponsors.monthly_amount', 'beneficiaries.id')
            ->all();

        $syncPayload = [];
        $directCount = count($directBeneficiaryIds);
        foreach ($allBeneficiaryIds as $bId) {
            $isDirect = in_array((int) $bId, $directBeneficiaryIds, true);
            $explicitAmt = isset($beneficiaryAmounts[$bId]) && $beneficiaryAmounts[$bId] !== '' && $beneficiaryAmounts[$bId] !== null
                ? (float) $beneficiaryAmounts[$bId]
                : null;

            if (($explicitAmt === null || $explicitAmt <= 0) && $isDirect) {
                if (isset($existingPivot[$bId]) && (float) $existingPivot[$bId] > 0) {
                    $explicitAmt = (float) $existingPivot[$bId];
                } elseif ($directCount === 1 && $familyMemberIds === [] && $totalMonthly > 0) {
                    $explicitAmt = $totalMonthly;
                }
            }

            $syncPayload[$bId] = [
                'monthly_amount' => ($explicitAmt !== null && $explicitAmt > 0) ? round($explicitAmt, 2) : null,
            ];

            if ($explicitAmt !== null && $explicitAmt > 0) {
                $ben = \App\Models\Beneficiary::find($bId);
                if ($ben && ((float) ($ben->monthly_sponsorship_amount ?? 0)) <= 0) {
                    $ben->update([
                        'monthly_sponsorship_amount' => round($explicitAmt, 2),
                        'sponsorship_type' => $ben->sponsorship_type ?: 'monthly_sponsor',
                    ]);
                }
            }
        }

        $donor->sponsoredBeneficiaries()->sync($syncPayload);
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

    public function getUpcomingDueDonors(int $daysAhead = 7)
    {
        $currentDay = (int) now()->day;
        $upcomingDays = [];
        for ($i = 0; $i < $daysAhead; $i++) {
            $upcomingDays[] = (($currentDay + $i - 1) % 31) + 1;
        }

        return Donor::where('active', true)
            ->where(function ($q) {
                $q->where('classification', 'recurring')
                  ->orWhereNotNull('monthly_donation_day');
            })
            ->whereIn('monthly_donation_day', $upcomingDays)
            ->orderByRaw("CASE 
                WHEN monthly_donation_day = {$currentDay} THEN 0 
                WHEN monthly_donation_day > {$currentDay} THEN monthly_donation_day - {$currentDay} 
                ELSE (monthly_donation_day + 31) - {$currentDay} 
            END ASC")
            ->get()
            ->map(function ($donor) use ($currentDay) {
                $day = (int) $donor->monthly_donation_day;
                if ($day === $currentDay) {
                    $donor->due_status = 'اليوم !';
                    $donor->due_badge = 'danger';
                    $donor->due_color = '#ef4444';
                    $donor->days_left = 0;
                } elseif ($day === $currentDay + 1 || ($currentDay === 31 && $day === 1)) {
                    $donor->due_status = 'غداً';
                    $donor->due_badge = 'warning';
                    $donor->due_color = '#f59e0b';
                    $donor->days_left = 1;
                } else {
                    $diff = $day > $currentDay ? ($day - $currentDay) : ($day + 31 - $currentDay);
                    $donor->due_status = "بعد {$diff} أيام";
                    $donor->due_badge = 'info';
                    $donor->due_color = '#3b82f6';
                    $donor->days_left = $diff;
                }

                $amountVal = $donor->sponsorship_monthly_amount ?: 2500;
                $amountText = number_format((float) $amountVal, 0) . ' ج.م';
                $waMsg = "السلام عليكم ورحمة الله وبركاته، أستاذ/ " . $donor->name . "، تحية طيبة من مؤسسة إنسان الخيرية. نود تذكير سيادتكم بموعد استحقاق التبرع/الكفالة الشهرية وقدرها " . $amountText . ". نسأل الله أن يبارك لكم ويجعلها في ميزان حسناتكم.";
                $cleanPhone = preg_replace('/[^0-9]/', '', (string) $donor->phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '20' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '20') && strlen($cleanPhone) === 10) {
                    $cleanPhone = '20' . $cleanPhone;
                }
                $donor->whatsapp_url = $cleanPhone ? ('https://wa.me/' . $cleanPhone . '?text=' . urlencode($waMsg)) : null;

                return $donor;
            });
    }
}
