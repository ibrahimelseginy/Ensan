<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignDailyMenu;
use App\Models\CampaignMonthlyVolunteer;
use App\Models\Beneficiary;
use App\Models\Donor;
use App\Models\Project;
use App\Models\Treasury;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\ChangeRequest;
use App\Services\CampaignService;
use App\Services\DonationService;
use App\Services\ExpenseService;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Requests\SetCampaignManagerRequest;
use App\Http\Requests\AttachVolunteerRequest;
use App\Http\Requests\StoreDailyMenuRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class CampaignWebController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private DonationService $donationService,
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): View
    {
        $filters   = $request->only(['q', 'status', 'season_year']);
        $campaigns = $this->campaignService->getFilteredCampaigns($filters, 20);

        return view('campaigns.index', array_merge(compact('campaigns'), $filters));
    }

    public function create(): View
    {
        $projects = Project::orderBy('name')->get();
        return view('campaigns.create', compact('projects'));
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $result = $this->campaignService->createCampaign($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحملة للموافقة.');
        }

        return redirect()->route('campaigns.show', $result)->with('success', 'تم إضافة الحملة بنجاح.');
    }

    public function show(Campaign $campaign): View|RedirectResponse
    {
        if ($this->hasPendingRequest($campaign)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $stats              = $this->campaignService->getCampaignStats($campaign);
        $volunteers         = User::where('is_volunteer', true)->orderBy('name')->get();
        $campaignVolunteers = $campaign->volunteers()->orderBy('name')->get();
        $monthlyVolunteers  = $campaign->monthlyVolunteers()->with('user')->get();
        $dailyMenus        = $campaign->dailyMenus()->with('responsible')->get();
        $users             = User::orderBy('name')->get();
        $beneficiaryOptions = Beneficiary::orderBy('full_name')->get(['id', 'code', 'full_name']);
        $sponsors           = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $campaignDonors     = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $campaignBeneficiaryOptions = Beneficiary::where('campaign_id', $campaign->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);
        $campaignWarehouses = Warehouse::when(
            Schema::hasColumn('warehouses', 'is_active'),
            fn ($query) => $query->where('is_active', true)
        )->orderBy('name')->get(['id', 'name']);
        $campaignDonationTreasuries = Treasury::active()
            ->where(function ($query) use ($campaign): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE])
                    ->orWhere(function ($campaignTreasury) use ($campaign): void {
                        $campaignTreasury->where('type', Treasury::TYPE_CAMPAIGN)
                            ->where('campaign_id', $campaign->id);
                    });
            })
            ->orderBy('name')
            ->get();
        $campaignExpenseTreasuries = Treasury::active()
            ->where(function ($query) use ($campaign): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
                    ->orWhere(function ($campaignTreasury) use ($campaign): void {
                        $campaignTreasury->where('type', Treasury::TYPE_CAMPAIGN)
                            ->where('campaign_id', $campaign->id);
                    })
                    ->orWhere(function ($projectTreasury) use ($campaign): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $campaign->project_id);
                    });
            })
            ->orderBy('name')
            ->get();

        return view('campaigns.show', array_merge(compact(
            'campaign',
            'volunteers',
            'campaignVolunteers',
            'monthlyVolunteers',
            'dailyMenus',
            'users',
            'beneficiaryOptions',
            'sponsors',
            'campaignDonors',
            'campaignBeneficiaryOptions',
            'campaignWarehouses',
            'campaignDonationTreasuries',
            'campaignExpenseTreasuries'
        ), $stats));
    }

    public function setManager(SetCampaignManagerRequest $request, Campaign $campaign): RedirectResponse
    {
        if ($this->hasPendingRequest($campaign)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $result = $this->campaignService->setManager($campaign, $request->except('manager_photo'), $request->file('manager_photo'));

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تغيير المدير للمراجعة');
        }

        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم تحديث مدير الحملة بنجاح');
    }

    public function attachVolunteer(AttachVolunteerRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->campaignService->attachVolunteer($campaign, $request->validated());
        return redirect()->route('campaigns.show', $campaign);
    }

    public function detachVolunteer(Campaign $campaign, User $user): RedirectResponse
    {
        $this->campaignService->detachVolunteer($campaign, (int)$user->id);
        return redirect()->route('campaigns.show', $campaign);
    }

    public function storeMonthlyVolunteer(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000|max:2100',
            'notes'   => 'nullable|string'
        ]);

        $this->campaignService->storeMonthlyVolunteer($campaign, $data);
        return redirect()->route('campaigns.show', $campaign);
    }

    public function destroyMonthlyVolunteer(Campaign $campaign, CampaignMonthlyVolunteer $monthlyVolunteer): RedirectResponse
    {
        abort_unless((int) $monthlyVolunteer->campaign_id === (int) $campaign->id, 404);

        $this->campaignService->deleteMonthlyVolunteer((int)$monthlyVolunteer->id);
        return back()->with('success', 'تم حذف متطوع الشهر بنجاح');
    }

    public function edit(Campaign $campaign): View|RedirectResponse
    {
        if ($this->hasPendingRequest($campaign)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $projects = Project::orderBy('name')->get();
        return view('campaigns.edit', compact('campaign', 'projects'));
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        if ($this->hasPendingRequest($campaign)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $result = $this->campaignService->updateCampaign($campaign, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الحملة للموافقة.');
        }

        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم تعديل الحملة بنجاح.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        if ($this->hasPendingRequest($campaign)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $result = $this->campaignService->deleteCampaign($campaign);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الحملة للموافقة.');
        }

        return redirect()->route('campaigns.index')->with('success', 'تم حذف الحملة بنجاح');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:campaigns,id'
        ]);

        Campaign::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف الحملات المحددة بنجاح');
    }

    public function storeDailyMenu(StoreDailyMenuRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->campaignService->storeDailyMenu($campaign, $request->validated());
        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم إضافة القائمة بنجاح');
    }

    public function destroyDailyMenu(Campaign $campaign, CampaignDailyMenu $dailyMenu): RedirectResponse
    {
        abort_unless((int) $dailyMenu->campaign_id === (int) $campaign->id, 404);

        $this->campaignService->deleteDailyMenu((int)$dailyMenu->id);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم حذف القائمة بنجاح');
    }

    public function storeBeneficiaryFile(StoreBeneficiaryRequest $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validated();
        $allocatedBeneficiaryIds = $data['allocated_beneficiary_ids'] ?? [];
        $sponsorIds = $data['sponsor_ids'] ?? [];

        unset(
            $data['allocated_beneficiary_ids'],
            $data['sponsor_ids'],
            $data['project_id'],
            $data['campaign_id'],
            $data['guest_house_id'],
            $data['status'],
            $data['rejection_reason']
        );

        $data['status'] = 'new';
        if (empty($data['code'])) {
            $data['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        DB::transaction(function () use ($campaign, $data, $allocatedBeneficiaryIds, $sponsorIds): void {
            $beneficiary = $campaign->beneficiaries()->create($data);
            $beneficiary->allocatedBeneficiaries()->sync($allocatedBeneficiaryIds);
            $beneficiary->sponsors()->sync($sponsorIds);
        });

        return back()->with('success', 'تم إضافة المستفيد للحملة بنجاح');
    }

    public function storeDonation(StoreDonationRequest $request, Campaign $campaign): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['guest_house_id']);
            $data['campaign_id'] = $campaign->id;

            if (($data['type'] ?? null) === 'cash'
                && ! $this->isAllowedDonationTreasury($campaign, (int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لتبرعات هذه الحملة.');
            }

            $result = $this->donationService->createDonation($data);

            if ($result instanceof ChangeRequest) {
                return back()->with('success', 'تم إرسال تبرع الحملة للمراجعة.');
            }

            return back()->with('success', 'تم تسجيل تبرع الحملة بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل التبرع: ' . $exception->getMessage());
        }
    }

    public function storeExpense(StoreExpenseRequest $request, Campaign $campaign): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['workspace_id'], $data['guest_house_id']);
            $data['campaign_id'] = $campaign->id;

            if (! $this->isAllowedExpenseTreasury($campaign, (int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لمصروفات هذه الحملة.');
            }

            if (! empty($data['beneficiary_id'])
                && ! Beneficiary::whereKey($data['beneficiary_id'])->where('campaign_id', $campaign->id)->exists()) {
                throw new \RuntimeException('المستفيد المختار غير مرتبط بهذه الحملة.');
            }

            $result = $this->expenseService->createExpense($data);

            if ($result instanceof ChangeRequest) {
                return back()->with('success', 'تم إرسال مصروف الحملة للمراجعة.');
            }

            return back()->with('success', 'تم تسجيل مصروف الحملة وخصمه من الخزينة بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل المصروف: ' . $exception->getMessage());
        }
    }

    public function updateBeneficiaryFile(Request $request, Campaign $campaign, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->campaign_id === (int) $campaign->id, 404);

        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'assistance_type' => 'required|in:financial,in_kind,service',
            'notes'           => 'nullable|string',
        ]);

        $beneficiary->update($data);

        return back()->with('success', 'تم تعديل بيانات المستفيد بنجاح');
    }

    public function destroyBeneficiaryFile(Campaign $campaign, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->campaign_id === (int) $campaign->id, 404);

        $beneficiary->delete();

        return back()->with('success', 'تم حذف المستفيد بنجاح');
    }

    private function isAllowedDonationTreasury(Campaign $campaign, int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->where(function ($query) use ($campaign): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE])
                    ->orWhere(function ($campaignTreasury) use ($campaign): void {
                        $campaignTreasury->where('type', Treasury::TYPE_CAMPAIGN)
                            ->where('campaign_id', $campaign->id);
                    });
            })
            ->exists();
    }

    private function isAllowedExpenseTreasury(Campaign $campaign, int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->where(function ($query) use ($campaign): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
                    ->orWhere(function ($campaignTreasury) use ($campaign): void {
                        $campaignTreasury->where('type', Treasury::TYPE_CAMPAIGN)
                            ->where('campaign_id', $campaign->id);
                    })
                    ->orWhere(function ($projectTreasury) use ($campaign): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $campaign->project_id);
                    });
            })
            ->exists();
    }

    private function hasPendingRequest(Campaign $campaign): bool
    {
        return ChangeRequest::where('model_type', Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->exists();
    }
}
