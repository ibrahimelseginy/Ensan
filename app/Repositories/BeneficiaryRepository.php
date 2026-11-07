<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Beneficiary;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class BeneficiaryRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q              = $filters['q'] ?? null;
        $status         = $filters['status'] ?? null;
        $atype          = $filters['assistance_type'] ?? null;
        $projectId      = $filters['project_id'] ?? null;
        $campaignId     = $filters['campaign_id'] ?? null;
        $guestHouseId   = $filters['guest_house_id'] ?? null;
        $dateFrom       = $filters['date_from'] ?? null;
        $dateTo         = $filters['date_to'] ?? null;
        $hasPhone       = $filters['has_phone'] ?? null;
        $hasAttachments = $filters['has_attachments'] ?? null;
        $addressLike    = $filters['address_like'] ?? null;
        $codeYear       = $filters['code_year'] ?? null;
        $sort           = $filters['sort'] ?? 'id';
        $dir            = $filters['dir'] ?? 'desc';

        return Beneficiary::query()
            ->with(['project', 'campaign', 'creator', 'updater', 'patientProfile', 'familyMembers' => fn ($query) => $query->where('active', true)])
            ->withCount('attachments')
            ->when($codeYear, function ($qr) use ($codeYear) {
                $cy = trim((string) $codeYear);
                $cy = str_replace(
                    ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'],
                    ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                    $cy
                );
                if (preg_match('/^(?:19|20)?(\d{2})$/', $cy, $m)) {
                    $yr = $m[1];
                    $qr->where(function ($w) use ($yr) {
                        $w->where('code', 'like', $yr . '%')
                          ->orWhere('code', 'like', 'G-' . $yr . '%')
                          ->orWhere('patient_code', 'like', $yr . '%')
                          ->orWhereHas('familyMembers', fn ($members) => $members->where('code', 'like', $yr . '%'));
                    });
                }
            })
            ->when($q, function ($qr) use ($q) {
                $rawQ = trim((string) $q);
                $cleanQ = str_replace(
                    ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'],
                    ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                    $rawQ
                );

                // إذا كان البحث برقمين فقط (مثل 26 أو 25 أو 24) -> تصفية بسنة الدخول (أول رقمين من الكود)
                if (preg_match('/^\d{2}$/', $cleanQ)) {
                    $qr->where(function ($w) use ($cleanQ, $rawQ) {
                        $w->where('code', 'like', $cleanQ . '%')
                          ->orWhere('code', 'like', 'G-' . $cleanQ . '%')
                          ->orWhere('patient_code', 'like', $cleanQ . '%')
                          ->orWhereHas('familyMembers', fn ($members) => $members->where('code', 'like', $cleanQ . '%'))
                          ->orWhere('full_name', 'like', '%' . $rawQ . '%');
                    });
                    return;
                }

                // إذا كتب السنة كاملة 4 أرقام (مثل 2026 أو 2025)
                if (preg_match('/^(?:19|20)(\d{2})$/', $cleanQ, $matches)) {
                    $yr = $matches[1];
                    $qr->where(function ($w) use ($yr, $cleanQ, $rawQ) {
                        $w->where('code', 'like', $yr . '%')
                          ->orWhere('code', 'like', 'G-' . $yr . '%')
                          ->orWhere('code', 'like', '%' . $cleanQ . '%')
                          ->orWhere('patient_code', 'like', $yr . '%')
                          ->orWhereHas('familyMembers', fn ($members) => $members->where('code', 'like', $yr . '%'))
                          ->orWhere('full_name', 'like', '%' . $rawQ . '%');
                    });
                    return;
                }

                // البحث الشامل الذكي بالاسم (ومطابقة الكلمات المتعددة والحروف المتشابهة)، الكود، الهاتف، أو الرقم القومي، وأفراد الأسرة
                $qr->where(function ($w) use ($cleanQ) {
                    \App\Helpers\ArabicSearchHelper::applySmartSearch(
                        $w,
                        $cleanQ,
                        ['full_name', 'patient_name', 'guardian_name', 'mother_name', 'deceased_husband_name', 'address'],
                        ['code', 'patient_code', 'national_id', 'phone', 'backup_phone', 'visa_card_number', 'reference_number']
                    );

                    $w->orWhereHas('familyMembers', function ($members) use ($cleanQ) {
                        \App\Helpers\ArabicSearchHelper::applySmartSearch(
                            $members,
                            $cleanQ,
                            ['full_name'],
                            ['code', 'national_id', 'phone']
                        );
                    });
                });
            })
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($atype, fn($qr) => $qr->where('assistance_type', $atype))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn($qr) => $qr->where('guest_house_id', $guestHouseId))
            ->when($dateFrom && $dateTo, fn($qr) => $qr->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when($dateFrom && !$dateTo, fn($qr) => $qr->whereDate('created_at', '>=', $dateFrom))
            ->when(!$dateFrom && $dateTo, fn($qr) => $qr->whereDate('created_at', '<=', $dateTo))
            ->when($hasPhone == '1', fn($qr) => $qr->whereNotNull('phone')->where('phone', '<>', ''))
            ->when($addressLike, fn($qr) => $qr->where('address', 'like', '%' . $addressLike . '%'))
            ->when($hasAttachments == '1', function ($qr) {
                $qr->where(function ($w) {
                    $w->has('attachments')
                      ->orWhereHas('patientProfile', function ($p) {
                          $p->whereNotNull('patient_id_front_path')
                            ->orWhereNotNull('patient_id_back_path')
                            ->orWhereNotNull('companion_id_front_path')
                            ->orWhereNotNull('companion_id_back_path')
                            ->orWhereNotNull('followup_card_path')
                            ->orWhereNotNull('referral_letter_path');
                      });
                });
            })
            ->orderBy($sort, $dir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Beneficiary
    {
        return Beneficiary::with(['project', 'campaign', 'attachments', 'familyMembers.sponsors'])->find($id);
    }

    public function create(array $data): Beneficiary
    {
        return Beneficiary::create($data);
    }

    public function update(Beneficiary $beneficiary, array $data): bool
    {
        return $beneficiary->update($data);
    }

    public function delete(Beneficiary $beneficiary): bool
    {
        return $beneficiary->delete();
    }

    public function getDuplicates(Beneficiary $beneficiary): array
    {
        $duplicates = [];
        $phone = trim((string)$beneficiary->phone);
        $invalidPhoneValues = ['-', '--', '—', 'لا يوجد', 'لايوجد', 'بدون', '0', '00000000000', '0000000000', 'none', 'null'];
        if ($phone !== '' && strlen($phone) >= 7 && !in_array($phone, $invalidPhoneValues, true) && preg_match('/\d{7,}/', $phone)) {
            $duplicates['phone'] = Beneficiary::where('phone', $phone)->where('id', '<>', $beneficiary->id)->count() > 0;
        }

        $nid = trim((string)$beneficiary->national_id);
        $invalidNidValues = ['-', '--', '—', 'لا يوجد', 'لايوجد', 'بدون', '0', '00000000000000', 'none', 'null'];
        if ($nid !== '' && strlen($nid) === 14 && !in_array($nid, $invalidNidValues, true) && ctype_digit($nid)) {
            $duplicates['national_id'] = Beneficiary::where('national_id', $nid)->where('id', '<>', $beneficiary->id)->count() > 0;
        }
        return $duplicates;
    }

    public function getGlobalStats(): array
    {
        return [
            'total'        => Beneficiary::count(),
            'new'          => Beneficiary::whereIn('status', ['pending', 'new'])->count(),
            'under_review' => Beneficiary::where('status', 'under_review')->count(),
            'accepted'     => Beneficiary::where('status', 'accepted')->count(),
            'rejected'     => Beneficiary::where('status', 'rejected')->count(),
        ];
    }

    public function getAssistanceDistribution(): array
    {
        return [
            'financial' => (int)Beneficiary::where('assistance_type', 'financial')->count(),
            'in_kind'   => (int)Beneficiary::where('assistance_type', 'in_kind')->count(),
            'service'   => (int)Beneficiary::where('assistance_type', 'service')->count(),
        ];
    }

    public function getDuplicatePhones(): array
    {
        return DB::table('beneficiaries')
            ->select('phone')
            ->whereNotNull('phone')
            ->where('phone', '<>', '')
            ->whereRaw("LENGTH(TRIM(phone)) >= 7")
            ->whereNotIn('phone', ['-', '--', '—', 'لا يوجد', 'لايوجد', 'بدون', '0', '00000000000', '0000000000', 'none', 'null'])
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone')
            ->all();
    }

    public function getDuplicateNids(): array
    {
        return DB::table('beneficiaries')
            ->select('national_id')
            ->whereNotNull('national_id')
            ->where('national_id', '<>', '')
            ->whereRaw("LENGTH(TRIM(national_id)) = 14")
            ->whereNotIn('national_id', ['-', '--', '—', 'لا يوجد', 'لايوجد', 'بدون', '0', '00000000000000', 'none', 'null'])
            ->groupBy('national_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('national_id')
            ->all();
    }

    public function getDonations(int $beneficiaryId): Collection
    {
        return \App\Models\Donation::where(function ($query) use ($beneficiaryId) {
                $query->where('beneficiary_id', $beneficiaryId)
                    ->orWhere(function ($q) use ($beneficiaryId) {
                        $q->where('donationable_type', \App\Models\Beneficiary::class)
                          ->where('donationable_id', $beneficiaryId);
                    })
                    ->orWhereRaw("beneficiary_allocations LIKE ?", ['%"beneficiary_id":' . (int) $beneficiaryId . '%'])
                    ->orWhereRaw("beneficiary_allocations LIKE ?", ['%"beneficiary_id":"' . (int) $beneficiaryId . '"%'])
                    ->orWhereRaw("allocation_note REGEXP ?", ['beneficiary_id=' . (int) $beneficiaryId . '([^0-9]|$)'])
                    ->orWhereRaw("allocation_note REGEXP ?", ['beneficiary_ids=[0-9,]*' . (int) $beneficiaryId . '([^0-9]|$)']);
            })
            ->where('status', '!=', 'cancelled')
            ->with(['donor', 'project', 'campaign', 'guestHouse'])
            ->orderByDesc('received_at')
            ->get();
    }
}
