<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\TravelRoute;
use App\Models\ChangeRequest;
use App\Repositories\TripRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Response;

final readonly class TripService
{
    public function __construct(
        private TripRepository $tripRepository
    ) {}

    public function getFilteredTrips(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->tripRepository->paginateFilteredTrips($filters, $perPage);
    }

    public function getTripStats(array $filters): array
    {
        return $this->tripRepository->getTripStats($filters);
    }

    public function findTripById(int $id): ?Donation
    {
        return $this->tripRepository->findById($id);
    }

    public function createTrip(array $data): mixed
    {
        $normalizedPhone = $this->normalizePhone($data['donor_phone']);
        
        $donor = Donor::query()
            ->where(function($qq) use ($data, $normalizedPhone) {
                $qq->where('name', $data['donor_name']);
                if ($normalizedPhone !== '') { $qq->orWhere('phone', $normalizedPhone); }
            })
            ->first();

        if (!$donor) {
            $donor = Donor::create([
                'name'   => $data['donor_name'],
                'phone'  => ($normalizedPhone !== '' ? $normalizedPhone : null),
                'type'   => 'individual',
                'active' => true
            ]);
        } elseif ($normalizedPhone !== '' && ($donor->phone ?? '') !== $normalizedPhone) {
            $donor->update(['phone' => $normalizedPhone]);
        }

        $allocationNote = $this->generateAllocationNote($data);
        
        $executor = function () use ($donor, $data, $allocationNote) {
            $donation = new Donation();
            $donation->donor_id        = $donor->id;
            $donation->type            = $data['donation_type'];
            $donation->amount          = $data['donation_type'] === 'cash' ? (float) ($data['amount'] ?? 0) : null;
            $donation->estimated_value = $data['donation_type'] === 'in_kind' ? (float) ($data['estimated_value'] ?? 0) : null;
            $donation->delegate_id     = $data['delegate_id'] ?? null;
            $donation->route_id        = $data['route_id'];
            $donation->allocation_note = $allocationNote;
            $donation->received_at     = $data['trip_date'];

            if ($data['donation_type'] === 'in_kind') {
                $donation->warehouse_id = $data['warehouse_id'] ?? null;
            }

            $allocType = $data['alloc_type'] ?? '';
            if ($allocType === 'project' || $allocType === 'sadaqa_jariya') {
                $donation->project_id = $data['project_id'] ?? null;
            } elseif ($allocType === 'campaign') {
                $donation->campaign_id = $data['campaign_id'] ?? null;
            } elseif ($allocType === 'guest_house') {
                if (Schema::hasColumn('donations', 'guest_house_id')) {
                    $donation->guest_house_id = $data['guest_house_id'] ?? null;
                }
            }

            $donation->save();
            return $donation;
        };

        return ChangeRequestService::handleRequest(
            Donation::class,
            null,
            'create',
            array_merge($data, ['donor_id' => $donor->id, 'allocation_note' => $allocationNote]),
            $executor,
            true
        );
    }

    private function normalizePhone(string $phone): string
    {
        $map = ['٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
        $normalized = strtr($phone, $map);
        return preg_replace('/[^\d\+]/', '', $normalized);
    }

    private function generateAllocationNote(array $data): string
    {
        $noteParts   = [];
        $noteParts[] = 'مشوار: ' . $data['trip_title'];
        if (!empty($data['trip_description'])) { $noteParts[] = 'وصف: ' . $data['trip_description']; }
        if (!empty($data['trip_location'])) { $noteParts[] = 'مكان: ' . $data['trip_location']; }
        if (!empty($data['donation_currency'])) { $noteParts[] = 'عملة التبرع: ' . $data['donation_currency']; }
        if ($data['donation_type'] === 'cash') {
            $noteParts[] = 'قناة الاستلام: ' . ($data['delegate_id'] ? 'مندوب' : 'المركز');
        }
        if (($data['alloc_type'] ?? '') === 'sponsorship') {
            $noteParts[] = 'نوع الكفالة: ' . ($data['sponsorship_type'] ?? '—');
        }
        return implode("\n", $noteParts);
    }

    public function exportCsv(Collection $trips): Response
    {
        $filename = 'trips_'.now()->format('Ymd_His').'.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ];
        
        $content = "\xEF\xBB\xBF";
        $cols    = ['title', 'description', 'date', 'route', 'type', 'amount_or_value', 'currency', 'donor', 'donor_phone', 'delegate', 'delegate_phone'];
        $content .= implode(',', $cols) . "\n";

        foreach ($trips as $t) {
            $parsed = $this->parseAllocationNote($t->allocation_note ?? '');
            $amtVal = $t->type === 'cash' ? ($t->amount ?? 0) : ($t->estimated_value ?? 0);
            
            $line = [
                str_replace(["\r", "\n"], ' ', (string)($parsed['title'] ?? '')),
                str_replace(["\r", "\n"], ' ', (string)($parsed['description'] ?? '')),
                optional($t->received_at)->format('Y-m-d'),
                str_replace(["\r", "\n"], ' ', (string)($t->route->name ?? '')),
                $t->type,
                number_format((float)$amtVal, 2, '.', ''),
                (string) ($parsed['currency'] ?? ''),
                str_replace(["\r", "\n"], ' ', (string)($t->donor->name ?? '')),
                (string) ($t->donor->phone ?? ''),
                str_replace(["\r", "\n"], ' ', (string)($t->delegate->name ?? '')),
                (string) ($t->delegate->phone ?? ''),
            ];
            $content .= implode(',', array_map(fn($v) => (string) $v, $line)) . "\n";
        }

        return response($content, 200, $headers);
    }

    private function parseAllocationNote(string $note): array
    {
        $result = ['title' => '', 'description' => '', 'currency' => ''];
        foreach (explode("\n", $note) as $line) {
            if (str_starts_with($line, 'مشوار')) { $result['title'] = trim(explode(':', $line, 2)[1] ?? ''); }
            elseif (str_starts_with($line, 'وصف')) { $result['description'] = trim(explode(':', $line, 2)[1] ?? ''); }
            elseif (str_starts_with($line, 'عملة التبرع')) { $result['currency'] = trim(explode(':', $line, 2)[1] ?? ''); }
        }
        return $result;
    }
}
