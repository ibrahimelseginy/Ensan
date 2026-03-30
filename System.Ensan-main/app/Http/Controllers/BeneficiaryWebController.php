<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\ChangeRequest;
use App\Services\BeneficiaryService;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class BeneficiaryWebController extends Controller
{
    public function __construct(
        private BeneficiaryService $beneficiaryService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 20);
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }

        $filters = $request->only([
            'q', 'status', 'assistance_type', 'project_id', 'campaign_id',
            'guest_house_id', 'sort', 'dir', 'date_from', 'date_to',
            'has_phone', 'has_attachments', 'address_like'
        ]);
        
        $filters['per_page'] = $perPage;

        $beneficiaries = $this->beneficiaryService->getFilteredBeneficiaries($filters, $perPage);
        $stats         = $this->beneficiaryService->getDashboardStats();

        $projects = Project::query()
            ->where(fn($q) => $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%')
            )
            ->orderBy('name')->get();

        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();

        return view('beneficiaries.index', array_merge(
            compact('beneficiaries', 'projects', 'campaigns'),
            $filters,
            $stats
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        return $this->beneficiaryService->exportToCsv($request->all());
    }

    public function create(): View
    {
        $projects = Project::query()->where(fn($q) => $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%')
            )
            ->orderBy('name')->get();

        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('beneficiaries.create', compact('projects', 'campaigns', 'guestHouses'));
    }

    public function store(StoreBeneficiaryRequest $request): RedirectResponse
    {
        $result = $this->beneficiaryService->createBeneficiary($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.show', $result);
    }

    public function show(Beneficiary $beneficiary): View|RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $duplicates = $this->beneficiaryService->checkDuplicates($beneficiary);
        $isDup      = !empty($duplicates);
        $donations  = $this->beneficiaryService->getBeneficiaryDonations((int)$beneficiary->id);

        return view('beneficiaries.show', compact('beneficiary', 'isDup', 'donations'));
    }

    public function edit(Beneficiary $beneficiary): View|RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $projects = Project::query()->where(fn($q) => $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%')
            )
            ->orderBy('name')->get();

        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('beneficiaries.edit', compact('beneficiary', 'projects', 'campaigns', 'guestHouses'));
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary): RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        try {
            $result = $this->beneficiaryService->updateBeneficiary($beneficiary, $request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المستفيد للموافقة.');
            }

            return redirect()->route('beneficiaries.show', $beneficiary)->with('success', 'تم تعديل المستفيد بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function destroy(Beneficiary $beneficiary): RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $result = $this->beneficiaryService->deleteBeneficiary($beneficiary);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:beneficiaries,id',
            'bulk_action' => 'required|string',
        ]);

        $count = $this->beneficiaryService->bulkUpdate($request->input('ids'), $request->input('bulk_action'));

        $message = "تم تحديث {$count} مستفيد بنجاح.";
        if ($request->input('bulk_action') === 'delete') {
            $message = "تم حذف {$count} مستفيد بنجاح.";
        }

        return redirect()->back()->with('success', $message);
    }

    private function hasPendingRequest(Beneficiary $beneficiary): bool
    {
        return ChangeRequest::where('model_type', Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->exists();
    }
}
