<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donor;
use App\Models\Donation;
use App\Models\Project;
use App\Repositories\DonorRepository;
use App\Services\ChangeRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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

        $this->applySponsorshipDefaults($data);

        $executor = fn() => $this->donorRepository->create($data);

        return ChangeRequestService::handleRequest(
            Donor::class,
            null,
            'create',
            $data,
            $executor,
            $forceRequest
        );
    }

    public function updateDonor(Donor $donor, array $data, bool $forceRequest = true): mixed
    {
        $this->applySponsorshipDefaults($data, $donor);

        $executor = function () use ($donor, $data) {
            $this->donorRepository->update($donor, $data);
            return $donor;
        };

        return ChangeRequestService::handleRequest(
            Donor::class,
            $donor->id,
            'update',
            $data,
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
        $sponsorshipType = $data['sponsorship_type'] ?? ($donor?->sponsorship_type ?? 'none');
        
        if ($sponsorshipType !== 'none' && empty($data['sponsorship_project_id']) && (!$donor || empty($donor->sponsorship_project_id))) {
            $defaultProjId = Project::where('name', 'like', '%بعثاء%')->value('id');
            if ($defaultProjId) {
                $data['sponsorship_project_id'] = $defaultProjId;
            }
        }
    }
}
