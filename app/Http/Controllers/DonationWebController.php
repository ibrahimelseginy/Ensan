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
use App\Services\DonorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DonationWebController extends Controller
{
    public function __construct(
        private DonationService $donationService,
        private DonorService $donorService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->filled('channel') || $request->input('type') === 'cash') {
            return redirect()->route('donations.cash', $request->query());
        }
        if ($request->input('type') === 'in_kind' || $request->filled('warehouse_id')) {
            return redirect()->route('donations.in-kind', $request->query());
        }
        if ($request->filled('q') || $request->filled('month') || $request->filled('year')) {
            return redirect()->route('donations.cash', $request->query());
        }

        // Overall Totals
        $cashTotal = (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->sum('amount');
        $cashCount = (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->count();

        $inKindTotal = (float) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->sum('estimated_value');
        $inKindCount = (int) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->count();

        // Channel Totals (All, Cash, Vodafone Cash, InstaPay, Delegate, In-Kind)
        $channelMetrics = [
            'all' => [
                'total' => $cashTotal,
                'count' => $cashCount,
            ],
            'cash' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')
                    ->where(fn($q) => $q->where('cash_channel', 'cash')->orWhereNull('cash_channel')->orWhere('cash_channel', ''))->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')
                    ->where(fn($q) => $q->where('cash_channel', 'cash')->orWhereNull('cash_channel')->orWhere('cash_channel', ''))->count(),
            ],
            'vodafone_cash' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'vodafone_cash')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'vodafone_cash')->count(),
            ],
            'instapay' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'instapay')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'instapay')->count(),
            ],
            'delegate' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'delegate')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'delegate')->count(),
            ],
            'in_kind' => [
                'total' => $inKindTotal,
                'count' => $inKindCount,
            ],
        ];

        // Today Stats
        $dashboardStats = $this->donationService->getDashboardStats([]);
        $todayByChannel = [
            'cash'          => ['count' => 0, 'total' => 0.0],
            'instapay'      => ['count' => 0, 'total' => 0.0],
            'vodafone_cash' => ['count' => 0, 'total' => 0.0],
            'delegate'      => ['count' => 0, 'total' => 0.0],
        ];
        foreach ($dashboardStats['todayByChannel'] as $r) {
            $key = $r->cash_channel ?: 'cash';
            $todayByChannel[$key] = [
                'count' => (int) $r->count,
                'total' => (float) $r->total
            ];
        }
        $inKindToday = $dashboardStats['todayInKind'];

        // Helpers for Quick Receipt Modal
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

        $allDonors = Donor::query()->orderBy('name')->get(['id', 'name', 'code', 'phone']);

        return view('donations.index', compact(
            'cashTotal', 'cashCount', 'inKindTotal', 'inKindCount',
            'channelMetrics', 'todayByChannel', 'inKindToday',
            'receiptDonors', 'receiptProjects', 'allFamilyMembers', 'receiptTreasuries', 'allDonors'
        ));
    }

    public function cashIndex(Request $request): View
    {
        $q               = (string) $request->input('q', '');
        $month           = $request->input('month');
        $year            = $request->input('year');
        $projectId       = $request->input('project_id');
        $campaignId      = $request->input('campaign_id');
        $guestHouseId    = $request->input('guest_house_id');
        $channel         = $request->input('channel');
        if ($channel === 'all' || $channel === '') {
            $channel = null;
        }

        $filters = [
            'q'              => $q,
            'month'          => $month,
            'year'           => $year,
            'project_id'     => $projectId,
            'campaign_id'    => $campaignId,
            'guest_house_id' => $guestHouseId,
            'channel'        => $channel,
            'type'           => 'cash',
        ];

        $cashDonations = $this->donationService->searchDonations($filters, 20, 'page');

        // Filtered totals
        $totalAmount = (float) Donation::where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->when($channel, function($qr) use ($channel) {
                if ($channel === 'cash') {
                    $qr->where(function($sub) {
                        $sub->where('cash_channel', 'cash')
                            ->orWhereNull('cash_channel')
                            ->orWhere('cash_channel', '');
                    });
                } else {
                    $qr->where('cash_channel', $channel);
                }
            })
            ->when($month, fn($qr) => $qr->whereMonth('received_at', $month))
            ->when($year, fn($qr) => $qr->whereYear('received_at', $year))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->sum('amount');

        $totalCount = (int) Donation::where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->when($channel, function($qr) use ($channel) {
                if ($channel === 'cash') {
                    $qr->where(function($sub) {
                        $sub->where('cash_channel', 'cash')
                            ->orWhereNull('cash_channel')
                            ->orWhere('cash_channel', '');
                    });
                } else {
                    $qr->where('cash_channel', $channel);
                }
            })
            ->when($month, fn($qr) => $qr->whereMonth('received_at', $month))
            ->when($year, fn($qr) => $qr->whereYear('received_at', $year))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->count();

        // Channel Totals (All, Cash, Vodafone Cash, InstaPay, Delegate, In-Kind)
        $channelMetrics = [
            'all' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->count(),
            ],
            'cash' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')
                    ->where(fn($q) => $q->where('cash_channel', 'cash')->orWhereNull('cash_channel')->orWhere('cash_channel', ''))->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')
                    ->where(fn($q) => $q->where('cash_channel', 'cash')->orWhereNull('cash_channel')->orWhere('cash_channel', ''))->count(),
            ],
            'vodafone_cash' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'vodafone_cash')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'vodafone_cash')->count(),
            ],
            'instapay' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'instapay')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'instapay')->count(),
            ],
            'delegate' => [
                'total' => (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'delegate')->sum('amount'),
                'count' => (int) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->where('cash_channel', 'delegate')->count(),
            ],
            'in_kind' => [
                'total' => (float) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->sum('estimated_value'),
                'count' => (int) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->count(),
            ],
        ];

        // Today stats
        $dashboardStats = $this->donationService->getDashboardStats([]);
        $todayByChannel = [
            'cash'          => ['count' => 0, 'total' => 0.0],
            'instapay'      => ['count' => 0, 'total' => 0.0],
            'vodafone_cash' => ['count' => 0, 'total' => 0.0],
            'delegate'      => ['count' => 0, 'total' => 0.0],
        ];
        foreach ($dashboardStats['todayByChannel'] as $r) {
            $key = $r->cash_channel ?: 'cash';
            $todayByChannel[$key] = [
                'count' => (int) $r->count,
                'total' => (float) $r->total
            ];
        }

        $allDonors = Donor::query()->orderBy('name')->get(['id', 'name', 'code', 'phone']);
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
        $receiptCampaigns = \App\Models\Campaign::where('status', 'active')->orderBy('name')->get();

        return view('donations.cash', compact(
            'cashDonations', 'totalAmount', 'totalCount', 'channelMetrics', 'todayByChannel',
            'q', 'month', 'year', 'channel', 'projectId', 'campaignId',
            'allDonors', 'receiptDonors', 'receiptProjects', 'receiptCampaigns', 'allFamilyMembers', 'receiptTreasuries'
        ));
    }

    public function inKindIndex(Request $request): View
    {
        $q               = (string) $request->input('q', '');
        $month           = $request->input('month');
        $year            = $request->input('year');
        $projectId       = $request->input('project_id');
        $campaignId      = $request->input('campaign_id');
        $warehouseId     = $request->input('warehouse_id');

        $filters = [
            'q'              => $q,
            'month'          => $month,
            'year'           => $year,
            'project_id'     => $projectId,
            'campaign_id'    => $campaignId,
            'warehouse_id'   => $warehouseId,
            'type'           => 'in_kind',
        ];

        $inKindDonations = $this->donationService->searchDonations($filters, 20, 'page');

        // Filtered totals
        $totalEstimated = (float) Donation::where('type', 'in_kind')
            ->where('status', '!=', 'cancelled')
            ->when($warehouseId, fn($qr) => $qr->where('warehouse_id', $warehouseId))
            ->when($month, fn($qr) => $qr->whereMonth('received_at', $month))
            ->when($year, fn($qr) => $qr->whereYear('received_at', $year))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->sum('estimated_value');

        $totalCount = (int) Donation::where('type', 'in_kind')
            ->where('status', '!=', 'cancelled')
            ->when($warehouseId, fn($qr) => $qr->where('warehouse_id', $warehouseId))
            ->when($month, fn($qr) => $qr->whereMonth('received_at', $month))
            ->when($year, fn($qr) => $qr->whereYear('received_at', $year))
            ->when($projectId, fn($qr) => $qr->where('project_id', $projectId))
            ->when($campaignId, fn($qr) => $qr->where('campaign_id', $campaignId))
            ->count();

        $dashboardStats = $this->donationService->getDashboardStats([]);
        $inKindToday    = $dashboardStats['todayInKind'];

        $warehouses = Warehouse::orderBy('name')->get();
        $allDonors = Donor::query()->orderBy('name')->get(['id', 'name', 'code', 'phone']);
        $receiptProjects = Project::orderBy('name')->get();

        return view('donations.in_kind', compact(
            'inKindDonations', 'totalEstimated', 'totalCount', 'inKindToday',
            'q', 'month', 'year', 'warehouseId', 'projectId', 'campaignId',
            'warehouses', 'allDonors', 'receiptProjects'
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
        $activeCampaigns = \App\Models\Campaign::where('status', 'active')->orderBy('name')->get();
        return view('donations.create', compact(
            'donors', 'projects', 'allFamilyMembers', 'warehouses', 'treasuries', 'items', 'activeCampaigns'
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
