<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Donation;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Warehouse;
use App\Models\GuestHouse;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamilyMember;
use App\Services\DonorService;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

final class DonorWebController extends Controller
{
    public function __construct(
        private DonorService $donorService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'type', 'classification', 'active']);
        $donors  = $this->donorService->searchDonors($filters, 12);

        $donorIds = $donors->pluck('id');
        $donStats = Donation::select('donor_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereIn('donor_id', $donorIds)
            ->where('status', '!=', 'cancelled')
            ->groupBy('donor_id')
            ->get()
            ->keyBy('donor_id');

        $totals = $this->donorService->getGlobalStats();

        $allDonors        = $this->donorService->getAllDonors();
        $selectedDonorId  = $request->input('selected_donor_id');
        $selectedDonor    = null;
        $donationsHistory = collect();
        $paidThisMonth    = 0.0;

        if ($selectedDonorId) {
            $selectedDonor = $this->donorService->findDonorById((int)$selectedDonorId);
            if ($selectedDonor) {
                $donationsHistory = Donation::with(['project', 'campaign'])
                    ->where('donor_id', $selectedDonor->id)
                    ->where('status', '!=', 'cancelled')
                    ->orderByDesc('received_at')->orderByDesc('id')
                    ->limit(10)->get();
                $paidThisMonth = $this->donorService->getPaidThisMonth($selectedDonor);
            }
        }

        $warehouses = Warehouse::orderBy('name')->get();
        $projects   = Project::where(fn($q) => $q->where('name', 'like', '%بعثاء%')->orWhere('name', 'like', '%زاد%')->orWhere('name', 'like', '%مدرار%')->orWhere('name', 'like', '%كسو%'))
            ->where(fn($q) => $q->where('name', 'not like', '%ضياف%')->where('name', 'not like', '%دار الضيا%')->where('name', 'not like', '%Guest%'))
            ->orderBy('name')->get();
        $campaigns     = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses   = GuestHouse::where(fn($q) => $q->where('location', 'like', '%كفر%')->orWhere('location', 'like', '%طنطا%')->orWhere('name', 'like', '%كفر%')->orWhere('name', 'like', '%طنطا%'))->orderBy('name')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();

        $q              = $filters['q'] ?? '';
        $type           = $filters['type'] ?? '';
        $classification = $filters['classification'] ?? '';
        $active         = $filters['active'] ?? '';

        return view('donors.index', compact(
            'donors', 'donStats', 'totals', 'allDonors', 
            'selectedDonor', 'donationsHistory', 'selectedDonorId', 
            'warehouses', 'paidThisMonth', 'projects', 'campaigns', 
            'guestHouses', 'beneficiaries',
            'q', 'type', 'classification', 'active'
        ));
    }

    public function create(): View
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $familyMembers = BeneficiaryFamilyMember::query()
            ->where('active', true)
            ->where(fn ($query) => $query->where('relationship', 'child')->orWhere('is_patient', true))
            ->with('beneficiary')
            ->orderBy('full_name')
            ->get();
        $projects      = Project::where(fn($q) => $q->where('name', 'like', '%بعثاء%')->orWhere('name', 'like', '%زاد%')->orWhere('name', 'like', '%مدرار%')->orWhere('name', 'like', '%كسو%'))
            ->orderBy('name')->get();
        return view('donors.create', compact('beneficiaries', 'familyMembers', 'projects'));
    }

    public function store(StoreDonorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle quick form inputs (alloc_type, alloc_id) to model attributes if provided via quick form
        if ($request->filled('alloc_type')) {
            $data['allocation_type'] = $request->input('alloc_type');
            $aid = $request->input('alloc_id');

            if ($data['allocation_type'] === 'project' || $data['allocation_type'] === 'sadaqa_jariya') {
                $data['sponsorship_project_id'] = $aid;
            } elseif ($data['allocation_type'] === 'campaign') {
                $data['campaign_id'] = $aid;
            } elseif ($data['allocation_type'] === 'guest_house') {
                $data['guest_house_id'] = $aid;
            }
        }

        $result = $this->donorService->createDonor($data);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المتبرع للموافقة.');
        }

        $donor = $result;

        if ($request->input('return_to') === 'donations.create') {
            $allocType = $request->input('alloc_type');
            $allocId   = $request->input('alloc_id');
            $params    = ['donor_id' => $donor->id];

            if ($allocType && $allocId) {
                if ($allocType === 'project') {
                    $params['project_id'] = $allocId;
                } elseif ($allocType === 'guest_house') {
                    $params['guest_house_id'] = $allocId;
                } elseif ($allocType === 'campaign') {
                    $params['campaign_id'] = $allocId;
                }
            } else {
                if ($request->filled('guest_house_id')) {
                    $params['guest_house_id'] = $request->input('guest_house_id');
                } elseif ($request->filled('campaign_id')) {
                    $params['campaign_id'] = $request->input('campaign_id');
                }
            }

            if ($request->filled('sponsorship_type') && $request->input('sponsorship_type') !== 'none') {
                $params['sponsorship_type'] = $request->input('sponsorship_type');
                if ($request->filled('sponsored_beneficiary_id')) {
                    $params['beneficiary_id'] = $request->input('sponsored_beneficiary_id');
                }
            }

            return redirect()->route('donations.create', $params);
        }
        
        return redirect()->route('donors.show', $donor);
    }

    public function show(Donor $donor): View|RedirectResponse
    {
        if ($this->hasPendingRequest($donor)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $donor->load(['sponsoredBeneficiaries', 'sponsoredFamilyMembers.beneficiary']);
        $stats         = $this->donorService->getDonorStats($donor->id);
        $paidThisMonth = $this->donorService->getPaidThisMonth($donor);
        $history       = Donation::with(['project', 'campaign', 'warehouse'])
            ->where('donor_id', $donor->id)
            ->orderByDesc('received_at')->orderByDesc('id')
            ->get();
            
        return view('donors.show', compact('donor', 'stats', 'paidThisMonth', 'history'));
    }

    public function edit(Donor $donor): View|RedirectResponse
    {
        if ($this->hasPendingRequest($donor)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $donor->load(['sponsoredBeneficiaries', 'sponsoredFamilyMembers']);
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $familyMembers = BeneficiaryFamilyMember::query()
            ->where('active', true)
            ->where(fn ($query) => $query->where('relationship', 'child')->orWhere('is_patient', true))
            ->with('beneficiary')
            ->orderBy('full_name')
            ->get();
        $projects      = Project::where(fn($q) => $q->where('name', 'like', '%بعثاء%')->orWhere('name', 'like', '%زاد%')->orWhere('name', 'like', '%مدرار%')->orWhere('name', 'like', '%كسو%'))->orderBy('name')->get();
        $campaigns     = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses   = GuestHouse::orderBy('name')->get();
        
        return view('donors.edit', compact('donor', 'beneficiaries', 'familyMembers', 'projects', 'campaigns', 'guestHouses'));
    }

    public function update(UpdateDonorRequest $request, Donor $donor): RedirectResponse
    {
        if ($this->hasPendingRequest($donor)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $result = $this->donorService->updateDonor($donor, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المتبرع للموافقة.');
        }

        return redirect()->route('donors.show', $donor)->with('success', 'تم تعديل المتبرع بنجاح.');
    }

    public function printReport(Request $request): View
    {
        $selectedIds = $request->input('ids');
        $filters = $request->only(['q', 'type', 'classification', 'active']);

        if (!empty($selectedIds)) {
            $ids = is_array($selectedIds) ? $selectedIds : explode(',', (string)$selectedIds);
            $donors = Donor::whereIn('id', $ids)->orderByDesc('id')->get();
            $reportTitle = 'كشف بيانات المتبرعين المحددين';
        } else {
            $q              = $filters['q'] ?? '';
            $type           = $filters['type'] ?? null;
            $classification = $filters['classification'] ?? null;
            $active         = $filters['active'] ?? null;

            $donors = Donor::query()
                ->when($q !== '', function ($query) use ($q) {
                    $parsed = \App\Helpers\ArabicSearchHelper::parseQuery($q);
                    $variants = $parsed['variants'];
                    $codes = $parsed['codes'];
                    $numbers = $parsed['numbers'];

                    $query->where(function ($w) use ($variants, $codes, $numbers) {
                        foreach ($variants as $var) {
                            $w->orWhere('name', 'like', "%{$var}%")
                              ->orWhere('code', 'like', "%{$var}%")
                              ->orWhere('phone', 'like', "%{$var}%");
                        }
                        foreach ($codes as $c) {
                            $w->orWhere('code', 'like', "%{$c}%");
                        }
                        foreach ($numbers as $num) {
                            $w->orWhere('id', (int)$num)
                              ->orWhere('phone', 'like', "%{$num}%")
                              ->orWhere('code', 'like', "%{$num}%");
                        }
                    });
                })
                ->when($type, fn($query, $t) => $query->where('type', $t))
                ->when($classification, fn($query, $c) => $query->where('classification', $c))
                ->when(!is_null($active) && $active !== '', fn($query, $a) => $query->where('active', (bool)$a))
                ->orderByDesc('id')
                ->limit(1000)
                ->get();

            $reportTitle = 'تقرير المتبرعين — كشف مفصل بالتصفية';
        }

        $donorIds = $donors->pluck('id');
        $donStats = Donation::select('donor_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereIn('donor_id', $donorIds)
            ->where('status', '!=', 'cancelled')
            ->groupBy('donor_id')
            ->get()
            ->keyBy('donor_id');

        $totalAmount = (float) Donation::whereIn('donor_id', $donorIds)
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('COALESCE(amount, estimated_value, 0)'));
        $totalCount  = (int) Donation::whereIn('donor_id', $donorIds)
            ->where('status', '!=', 'cancelled')
            ->count();

        return view('donors.print-report', compact(
            'donors', 'donStats', 'reportTitle', 'totalAmount', 'totalCount', 'filters'
        ));
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:donors,id'
        ]);

        Donor::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف المتبرعين المحددين بنجاح');
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        if ($this->hasPendingRequest($donor)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $result = $this->donorService->deleteDonor($donor);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المتبرع للموافقة.');
        }

        return redirect()->route('donors.index')->with('success', 'تم حذف المتبرع بنجاح.');
    }

    private function hasPendingRequest(Donor $donor): bool
    {
        $user = request()->user();
        if ($user && ($user->hasRole('admin') || $user->hasRole('manager'))) {
            return false;
        }

        return ChangeRequest::where('model_type', Donor::class)
            ->where('model_id', $donor->id)
            ->where('status', 'pending')
            ->exists();
    }
}
