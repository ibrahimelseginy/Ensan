<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\ChangeRequest;
use App\Repositories\BeneficiaryRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $data['status'] = $data['status'] ?? 'new';
        if (empty($data['code'])) {
            $data['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        $executor = fn() => $this->beneficiaryRepository->create($data);

        return ChangeRequestService::handleRequest(
            Beneficiary::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function updateBeneficiary(Beneficiary $beneficiary, array $data): mixed
    {
        // Status transition validation
        if (isset($data['status'])) {
            $this->validateStatusTransition($beneficiary->status, $data['status']);
        }

        // Auto-generate code if missing
        if (empty($data['code']) && empty($beneficiary->code)) {
            $data['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        $executor = function () use ($beneficiary, $data) {
            $this->beneficiaryRepository->update($beneficiary, $data);
            return $beneficiary;
        };

        return ChangeRequestService::handleRequest(
            Beneficiary::class,
            $beneficiary->id,
            'update',
            $data,
            $executor,
            true
        );
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
            fputcsv($out, ['#', 'الاسم', 'الرقم القومي', 'الهاتف', 'الحالة', 'نوع المساعدة', 'المشروع', 'الحملة', 'تاريخ الإنشاء']);
            
            foreach ($rows as $b) {
                fputcsv($out, [
                    $b->id,
                    $b->full_name,
                    $b->national_id,
                    $b->phone,
                    $b->status,
                    $b->assistance_type,
                    optional($b->project)->name,
                    optional($b->campaign)->name,
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
        if ($next === 'rejected') return; // Allow rejection from any state

        $allowed = [
            'new'          => ['under_review', 'rejected'],
            'under_review' => ['accepted', 'rejected'],
            'accepted'     => ['rejected'],
            'rejected'     => ['new', 'under_review']
        ];

        if (!in_array($next, $allowed[$current] ?? [], true)) {
            throw new \Exception('انتقال حالة غير مسموح من ' . $current . ' إلى ' . $next);
        }
    }
}
