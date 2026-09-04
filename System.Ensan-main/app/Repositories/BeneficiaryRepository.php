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
        $sort           = $filters['sort'] ?? 'id';
        $dir            = $filters['dir'] ?? 'desc';

        return Beneficiary::query()
            ->with(['project', 'campaign', 'familyMembers' => fn ($query) => $query->where('active', true)])
            ->withCount('attachments')
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('full_name', 'like', '%' . $q . '%')
                      ->orWhere('code', 'like', '%' . $q . '%')
                      ->orWhere('phone', 'like', '%' . $q . '%')
                      ->orWhere('national_id', 'like', '%' . $q . '%')
                      ->orWhere('visa_card_number', 'like', '%' . $q . '%')
                      ->orWhereHas('familyMembers', fn ($members) => $members
                          ->where('full_name', 'like', '%' . $q . '%')
                          ->orWhere('code', 'like', '%' . $q . '%'));
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
            ->when($hasAttachments == '1', fn($qr) => $qr->having('attachments_count', '>', 0))
            ->orderBy($sort, $dir)
            ->paginate($perPage);
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
        if (!empty($beneficiary->phone)) {
            $duplicates['phone'] = Beneficiary::where('phone', $beneficiary->phone)->where('id', '<>', $beneficiary->id)->count() > 0;
        }
        if (!empty($beneficiary->national_id)) {
            $duplicates['national_id'] = Beneficiary::where('national_id', $beneficiary->national_id)->where('id', '<>', $beneficiary->id)->count() > 0;
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
            ->groupBy('national_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('national_id')
            ->all();
    }

    public function getDonations(int $beneficiaryId): Collection
    {
        return \App\Models\Donation::whereRaw("allocation_note REGEXP 'beneficiary_id={$beneficiaryId}([^0-9]|$)'")
            ->with(['donor', 'project', 'campaign', 'guestHouse'])
            ->orderByDesc('received_at')
            ->get();
    }
}
