<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Warehouse;
use App\Models\Delegate;
use App\Models\Beneficiary;
use App\Models\TravelRoute;
use App\Models\ChangeRequest;
use App\Services\DonationService;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DonationWebController extends Controller
{
    public function __construct(
        private DonationService $donationService
    ) {}

    public function index(Request $request): View
    {
        $q               = (string) $request->input('q', '');
        $projectId       = $request->input('project_id');
        $campaignId      = $request->input('campaign_id');
        $guestHouseId    = $request->input('guest_house_id');
        $channel         = $request->input('channel');

        $filters = [
            'q'              => $q,
            'project_id'     => $projectId,
            'campaign_id'    => $campaignId,
            'guest_house_id' => $guestHouseId,
            'channel'        => $channel,
        ];

        // Cash Donations Paginator
        $cashFilters = array_merge($filters, ['type' => 'cash']);
        $cashDonations = $this->donationService->searchDonations($cashFilters, 10, 'cash_page');

        // In-Kind Donations Paginator
        $inKindDonations = collect();
        if (!$channel) {
            $inKindFilters = array_merge($filters, ['type' => 'in_kind']);
            $inKindDonations = $this->donationService->searchDonations($inKindFilters, 10, 'inkind_page');
        }

        // Statistics
        $stats = [
            'dailyCashSummary' => collect(),
            'todayByChannel'   => [
                'cash'          => ['count' => 0, 'total' => 0.0],
                'instapay'      => ['count' => 0, 'total' => 0.0],
                'vodafone_cash' => ['count' => 0, 'total' => 0.0],
                'delegate'      => ['count' => 0, 'total' => 0.0],
            ],
            'inKindToday'      => (object)['count' => 0, 'total' => 0]
        ];

        if (!$channel && !$q) {
            $dashboardStats = $this->donationService->getDashboardStats($filters);
            $stats['dailyCashSummary'] = $dashboardStats['dailySummary'];
            
            foreach ($dashboardStats['todayByChannel'] as $r) {
                $key = $r->cash_channel ?: 'cash';
                $stats['todayByChannel'][$key] = [
                    'count' => (int) $r->count,
                    'total' => (float) $r->total
                ];
            }
            
            $stats['inKindToday'] = $dashboardStats['todayInKind'];
        }

        $dailyCashSummary = $stats['dailyCashSummary'];
        $todayByChannel   = $stats['todayByChannel'];
        $inKindToday      = $stats['inKindToday'];

        $receiptDonors = Donor::query()
            ->with(['sponsoredFamilyMembers' => fn ($query) => $query->where('active', true), 'sponsoredFamilyMembers.beneficiary'])
            ->orderBy('name')
            ->get();
        $receiptProjects = Project::orderBy('name')->get();
        $allFamilyMembers = \App\Models\BeneficiaryFamilyMember::with('beneficiary')
            ->where('active', true)
            ->orderBy('full_name')
            ->get();
        $receiptTreasuries = Schema::hasTable('treasuries')
            ? \App\Models\Treasury::query()->when(Schema::hasColumn('treasuries', 'is_active'), fn ($query) => $query->where('is_active', true))->orderBy('name')->get()
            : collect();

        return view('donations.index', compact(
            'cashDonations', 'inKindDonations', 'dailyCashSummary', 
            'todayByChannel', 'inKindToday', 'q', 'receiptDonors', 'receiptProjects', 'allFamilyMembers', 'receiptTreasuries'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $infoType = $request->get('type', 'cash');
        $filters  = $request->only(['q', 'project_id', 'campaign_id', 'month', 'year']);
        $filters['type'] = $infoType;

        $query = Donation::with(['donor', 'warehouse', 'familyMembers'])
            ->where('type', $infoType)
            ->when($filters['project_id'] ?? null, fn($qr, $id) => $qr->where('project_id', $id))
            ->when($filters['campaign_id'] ?? null, fn($qr, $id) => $qr->where('campaign_id', $id))
            ->when($request->get('month'), fn($qr, $m) => $qr->whereMonth('received_at', $m))
            ->when($request->get('year'), fn($qr, $y) => $qr->whereYear('received_at', $y))
            ->orderByDesc('received_at');

        $filename = "donations_{$infoType}_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($query, $infoType) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($infoType === 'cash') {
                fputcsv($handle, ['ID', 'المتبرع', 'المبلغ', 'وذلك قيمة', 'الأطفال / المرضى', 'الملاحظات', 'التاريخ', 'رقم الإيصال']);
                $query->chunk(100, function ($donations) use ($handle) {
                    foreach ($donations as $d) {
                        fputcsv($handle, [
                            $d->id, $d->donor->name ?? '—', $d->amount,
                            $d->purpose_label, $d->familyMembers->pluck('full_name')->implode(' | '),
                            $d->display_allocation_note, optional($d->received_at)->format('Y-m-d'), $d->receipt_number
                        ]);
                    }
                });
            } else {
                fputcsv($handle, ['ID', 'المتبرع', 'القيمة التقديرية', 'وذلك قيمة', 'الأطفال / المرضى', 'الملاحظات', 'المخزن', 'التاريخ']);
                $query->chunk(100, function ($donations) use ($handle) {
                    foreach ($donations as $d) {
                        fputcsv($handle, [
                            $d->id, $d->donor->name ?? '—', $d->estimated_value,
                            $d->purpose_label, $d->familyMembers->pluck('full_name')->implode(' | '),
                            $d->display_allocation_note, $d->warehouse->name ?? '—', optional($d->received_at)->format('Y-m-d')
                        ]);
                    }
                });
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create(): View
    {
        $donors      = Donor::query()
            ->with(['sponsoredFamilyMembers' => fn ($query) => $query->where('active', true), 'sponsoredFamilyMembers.beneficiary'])
            ->orderBy('name')
            ->get();
        $projects    = Project::orderBy('name')->get();
        $allFamilyMembers = \App\Models\BeneficiaryFamilyMember::with('beneficiary')
            ->where('active', true)
            ->orderBy('full_name')
            ->get();
        $warehouses  = Warehouse::when(Schema::hasColumn('warehouses', 'is_active'), fn($q) => $q->where('is_active', true))->orderBy('name')->get();
        $treasuries  = collect();
        if (Schema::hasTable('treasuries')) {
            $treasuries = \App\Models\Treasury::when(Schema::hasColumn('treasuries', 'is_active'), fn($q) => $q->where('is_active', true))->orderBy('name')->get();
        }
        $items         = \App\Models\Item::when(Schema::hasColumn('items', 'is_active'), fn($q) => $q->where('is_active', true))->orderBy('name')->get();
        return view('donations.create', compact(
            'donors', 'projects', 'allFamilyMembers', 'warehouses', 'treasuries', 'items'
        ));
    }

    public function store(StoreDonationRequest $request): RedirectResponse
    {
        $result = $this->donationService->createDonation($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة التبرع للموافقة.');
        }

        $donation = $result;
        return redirect()->route('donations.show', $donation)->with('success', 'تم حفظ التبرع. يمكنك مراجعة الإيصال وطباعته الآن.');
    }

    public function show(Donation $donation): View
    {
        $donation->load(['donor', 'project', 'campaign', 'warehouse', 'delegate', 'route', 'familyMembers.beneficiary']);
        return view('donations.show', compact('donation'));
    }

    public function edit(Donation $donation): View
    {
        $donors      = Donor::orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $warehouses  = Warehouse::orderBy('name')->get();
        $delegates   = Delegate::orderBy('name')->get();
        $routes      = TravelRoute::orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::where(fn($q) => $q->where('location', 'like', '%كفر%')->orWhere('location', 'like', '%طنطا%')->orWhere('name', 'like', '%كفر%')->orWhere('name', 'like', '%طنطا%'))->orderBy('name')->get();
        $ghKafr      = $guestHouses->first(fn($gh) => str_contains((string)($gh->location ?? ''), 'كفر') || str_contains((string)($gh->name ?? ''), 'كفر'));
        $ghTanta     = $guestHouses->first(fn($gh) => str_contains((string)($gh->location ?? ''), 'طنطا') || str_contains((string)($gh->name ?? ''), 'طنطا'));
        $beneficiaries = Beneficiary::select('id', 'full_name')->orderBy('full_name')->get();

        return view('donations.edit', compact(
            'donation', 'donors', 'projects', 'campaigns', 'warehouses', 
            'delegates', 'routes', 'guestHouses', 'ghKafr', 'ghTanta', 'beneficiaries'
        ));
    }

    public function update(UpdateDonationRequest $request, Donation $donation): RedirectResponse
    {
        $result = $this->donationService->updateDonation($donation, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل التبرع للموافقة.');
        }

        return redirect()->route('donations.show', $donation)->with('success', 'تم تعديل التبرع بنجاح.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:donations,id'
        ]);

        Donation::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف التبرعات المحددة بنجاح');
    }

    public function destroy(Donation $donation, Request $request): RedirectResponse
    {
        $reason = (string) $request->input('cancellation_reason', 'لم يتم تحديد سبب');
        $result = $this->donationService->cancelDonation($donation, $reason);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء التبرع للموافقة.');
        }

        return back()->with('success', 'تم إلغاء التبرع وعكس العمليات المالية بنجاح.');
    }
}
