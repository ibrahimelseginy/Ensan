<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BeneficiaryFamilyMember;
use App\Models\ChangeRequest;
use App\Repositories\BeneficiaryRepository;
use App\Services\ChangeRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\ValidationException;

final readonly class BeneficiaryService
{
    public function __construct(
        private BeneficiaryRepository $beneficiaryRepository
    ) {}

    public function getFilteredBeneficiaries(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $beneficiaries = $this->beneficiaryRepository->paginateFiltered($filters, $perPage);

        $beneficiaries->each(function(Beneficiary $beneficiary) {
            $beneficiary->pendingRequest = ChangeRequest::where('model_type', Beneficiary::class)
                ->where('model_id', $beneficiary->id)
                ->where('status', 'pending')
                ->first();
        });

        return $beneficiaries;
    }

    public function findBeneficiaryById(int $id): ?Beneficiary
    {
        return $this->beneficiaryRepository->findById($id);
    }

    public function createBeneficiary(array $data): mixed
    {
        $relationKeys = [
            'allocated_beneficiary_ids', 'sponsor_ids', 'family_members',
            'patient_relationship', 'patient_birth_date', 'patient_phone', 'patient_backup_phone',
        ];
        $relationData = Arr::only($data, $relationKeys);
        $modelData = Arr::except($data, $relationKeys);

        $this->mirrorFamilyMembersToLegacyJson($modelData, $relationData);
        $relationData = array_merge($relationData, Arr::only($modelData, [
            'patient_name', 'patient_age', 'patient_code', 'monthly_sponsorship_amount', 'notes_cases',
        ]));

        $modelData['status'] = $modelData['status'] ?? 'new';
        if (empty($modelData['code'])) {
            $modelData['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        $payload = array_merge($modelData, $relationData);
        $executor = function () use ($modelData, $relationData) {
            return DB::transaction(function () use ($modelData, $relationData) {
                $beneficiary = $this->beneficiaryRepository->create($modelData);
                $this->syncAssignments($beneficiary, $relationData);

                return $beneficiary;
            });
        };

        return ChangeRequestService::handleRequest(
            Beneficiary::class,
            null,
            'create',
            $payload,
            $executor,
            true
        );
    }

    public function updateBeneficiary(Beneficiary $beneficiary, array $data): mixed
    {
        $relationKeys = [
            'allocated_beneficiary_ids', 'sponsor_ids', 'family_members',
            'patient_relationship', 'patient_birth_date', 'patient_phone', 'patient_backup_phone',
        ];
        $relationData = Arr::only($data, $relationKeys);
        $modelData = Arr::except($data, $relationKeys);

        $this->mirrorFamilyMembersToLegacyJson($modelData, $relationData);
        $relationData = array_merge($relationData, Arr::only($modelData, [
            'patient_name', 'patient_age', 'patient_code', 'monthly_sponsorship_amount', 'notes_cases',
        ]));

        // Status transition validation
        if (isset($modelData['status'])) {
            $this->validateStatusTransition($beneficiary->status, $modelData['status']);
        }

        // Auto-generate code if missing
        if (empty($modelData['code']) && empty($beneficiary->code)) {
            $modelData['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        $payload = array_merge($modelData, $relationData);
        $executor = function () use ($beneficiary, $modelData, $relationData) {
            return DB::transaction(function () use ($beneficiary, $modelData, $relationData) {
                $this->beneficiaryRepository->update($beneficiary, $modelData);
                $this->syncAssignments($beneficiary, $relationData);

                return $beneficiary;
            });
        };

        return ChangeRequestService::handleRequest(
            Beneficiary::class,
            $beneficiary->id,
            'update',
            $payload,
            $executor,
            true
        );
    }

    public function syncAssignments(Beneficiary $beneficiary, array $data): void
    {
        if (array_key_exists('allocated_beneficiary_ids', $data)) {
            $beneficiary->allocatedBeneficiaries()->sync(
                array_values(array_unique(array_map('intval', $data['allocated_beneficiary_ids'] ?? [])))
            );
        }

        if (array_key_exists('sponsor_ids', $data)) {
            $beneficiary->sponsors()->sync(
                array_values(array_unique(array_map('intval', $data['sponsor_ids'] ?? [])))
            );
        }

        if (array_key_exists('family_members', $data)) {
            $this->syncFamilyMembers($beneficiary, (array) $data['family_members']);
        }

        if (array_key_exists('patient_name', $data)) {
            $this->syncPatientMember($beneficiary, $data);
        }
    }

    private function syncFamilyMembers(Beneficiary $beneficiary, array $members): void
    {
        $savedIds = [];

        foreach ($members as $position => $memberData) {
            $name = trim((string) ($memberData['full_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $relationship = (string) ($memberData['relationship'] ?? 'child');
            if (! in_array($relationship, ['husband', 'wife', 'child', 'other'], true)) {
                $relationship = 'other';
            }

            $memberId = isset($memberData['id']) ? (int) $memberData['id'] : 0;
            $member = $memberId > 0
                ? $beneficiary->familyMembers()->whereKey($memberId)->first()
                : null;

            $code = BeneficiaryFamilyMember::normalizeCode($memberData['code'] ?? null);
            if ($code && BeneficiaryFamilyMember::where('code', $code)
                ->when($member, fn ($query) => $query->whereKeyNot($member->id))
                ->exists()) {
                throw ValidationException::withMessages([
                    'family_members' => "كود فرد الأسرة {$code} مستخدم في ملف آخر.",
                ]);
            }

            $payload = [
                'relationship' => $relationship,
                'full_name' => $name,
                'birth_date' => $memberData['birth_date'] ?? null,
                'age' => $memberData['age'] ?? null,
                'code' => $code ?: $member?->code,
                'national_id' => $memberData['national_id'] ?? null,
                'phone' => $memberData['phone'] ?? null,
                'backup_phone' => $memberData['backup_phone'] ?? null,
                'sponsorship_amount' => $memberData['sponsorship_amount'] ?? null,
                'education_level' => $memberData['education_level'] ?? null,
                'case_details' => $memberData['case_details'] ?? null,
                'is_patient' => false,
                'active' => true,
                'sort_order' => $relationship === 'child' ? ((int) $position + 1) : 0,
            ];

            if ($member) {
                $member->update($payload);
            } else {
                $member = $beneficiary->familyMembers()->create($payload);
            }

            $savedIds[] = $member->id;
        }

        $beneficiary->familyMembers()
            ->where('is_patient', false)
            ->when($savedIds !== [], fn ($query) => $query->whereNotIn('id', $savedIds))
            ->update(['active' => false]);
    }

    private function syncPatientMember(Beneficiary $beneficiary, array $data): void
    {
        $name = trim((string) ($data['patient_name'] ?? ''));
        $member = $beneficiary->familyMembers()->where('is_patient', true)->first();

        if ($name === '') {
            $member?->update(['active' => false]);
            return;
        }

        $code = BeneficiaryFamilyMember::normalizeCode($data['patient_code'] ?? null);
        if ($code && BeneficiaryFamilyMember::where('code', $code)
            ->when($member, fn ($query) => $query->whereKeyNot($member->id))
            ->exists()) {
            throw ValidationException::withMessages([
                'patient_code' => 'كود المريض مستخدم في ملف فرد أسرة آخر.',
            ]);
        }

        $payload = [
            'relationship' => $data['patient_relationship'] ?? 'patient',
            'full_name' => $name,
            'birth_date' => $data['patient_birth_date'] ?? null,
            'age' => $data['patient_age'] ?? null,
            'code' => $code ?: $member?->code,
            'phone' => $data['patient_phone'] ?? null,
            'backup_phone' => $data['patient_backup_phone'] ?? null,
            'sponsorship_amount' => $data['monthly_sponsorship_amount'] ?? null,
            'case_details' => $data['notes_cases'] ?? null,
            'is_patient' => true,
            'active' => true,
            'sort_order' => 20,
        ];

        if ($member) {
            $member->update($payload);
        } else {
            $beneficiary->familyMembers()->create($payload);
        }
    }

    private function mirrorFamilyMembersToLegacyJson(array &$modelData, array $relationData): void
    {
        if (! array_key_exists('family_members', $relationData)) {
            return;
        }

        $modelData['family_members_data'] = collect($relationData['family_members'] ?? [])
            ->filter(fn ($member) => ($member['relationship'] ?? null) === 'child' && trim((string) ($member['full_name'] ?? '')) !== '')
            ->map(fn ($member) => [
                'name' => $member['full_name'],
                'age_dob' => $member['birth_date'] ?? ($member['age'] ?? null),
                'code' => $member['code'] ?? null,
                'amount' => $member['sponsorship_amount'] ?? null,
                'education' => $member['education_level'] ?? null,
            ])
            ->values()
            ->all();
    }

    public function deleteBeneficiary(Beneficiary $beneficiary): mixed
    {
        $executor = function () use ($beneficiary) {
            return $this->beneficiaryRepository->delete($beneficiary);
        };

        return ChangeRequestService::handleRequest(
            Beneficiary::class,
            $beneficiary->id,
            'delete',
            [],
            $executor,
            true
        );
    }

    public function exportToCsv(array $filters): StreamedResponse
    {
        $filters['per_page'] = 10000; // Large number for export
        $rows = $this->beneficiaryRepository->paginateFiltered($filters, 10000);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="beneficiaries.csv"'
        ];

        return response()->stream(function () use ($rows) {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                '#', 'الكود', 'اسم ولي الأمر', 'الرقم القومي', 'رقم الفيزا', 'الهاتف الأساسي',
                'الهاتف الإضافي', 'اسم المريض', 'الحالة', 'نوع المساعدة', 'المشروع',
                'عدد أفراد الأسرة', 'أسماء أفراد الأسرة', 'تاريخ الإنشاء',
            ]);
            
            foreach ($rows as $b) {
                fputcsv($out, [
                    $b->id,
                    $b->code,
                    $b->full_name,
                    $b->national_id,
                    $b->visa_card_number,
                    $b->phone,
                    $b->backup_phone,
                    $b->patient_name,
                    $b->status,
                    $b->assistance_type,
                    optional($b->project)->name,
                    $b->familyMembers->where('active', true)->count(),
                    $b->familyMembers->where('active', true)->pluck('full_name')->implode(' | '),
                    optional($b->created_at)->format('Y-m-d')
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function bulkUpdate(array $ids, string $action): int
    {
        $count = 0;
        switch ($action) {
            case 'status_under_review':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'under_review']);
                break;
            case 'status_accepted':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'accepted']);
                break;
            case 'status_rejected':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'rejected']);
                break;
            case 'delete':
                $count = Beneficiary::whereIn('id', $ids)->delete();
                break;
        }
        return (int)$count;
    }

    public function getDashboardStats(): array
    {
        return array_merge(
            $this->beneficiaryRepository->getGlobalStats(),
            [
                'assistDist' => $this->beneficiaryRepository->getAssistanceDistribution(),
                'last7'      => (int)Beneficiary::whereBetween('created_at', [now()->subDays(7), now()])->count(),
                'dupPhones'  => $this->beneficiaryRepository->getDuplicatePhones(),
                'dupNids'    => $this->beneficiaryRepository->getDuplicateNids(),
            ]
        );
    }

    public function checkDuplicates(Beneficiary $beneficiary): array
    {
        return $this->beneficiaryRepository->getDuplicates($beneficiary);
    }

    public function getBeneficiaryDonations(int $beneficiaryId): Collection
    {
        return $this->beneficiaryRepository->getDonations($beneficiaryId);
    }

    private function validateStatusTransition(string $current, string $next): void
    {
        if ($current === $next) return;
        if (in_array($next, ['rejected', 'archived_improved', 'archived_deceased'], true)) return;
        if (in_array($current, ['rejected', 'archived_improved', 'archived_deceased'], true) && $next === 'under_review') return;

        $allowed = [
            'new'          => ['under_review', 'rejected'],
            'pending'      => ['under_review', 'rejected'],
            'under_review' => ['accepted', 'rejected'],
            'accepted'     => ['rejected'],
            'rejected'     => ['new', 'under_review']
        ];

        if (!in_array($next, $allowed[$current] ?? [], true)) {
            throw new \Exception('انتقال حالة غير مسموح من ' . $current . ' إلى ' . $next);
        }
    }
}
