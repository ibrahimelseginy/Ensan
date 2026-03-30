<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignDailyMenu;
use App\Models\CampaignMonthlyVolunteer;
use App\Models\Project;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\CampaignService;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Requests\SetCampaignManagerRequest;
use App\Http\Requests\AttachVolunteerRequest;
use App\Http\Requests\StoreDailyMenuRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CampaignWebController extends Controller
{
    public function __construct(
        private CampaignService $campaignService
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

        return view('campaigns.show', array_merge(compact('campaign', 'volunteers', 'campaignVolunteers', 'monthlyVolunteers', 'dailyMenus', 'users'), $stats));
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
        $this->campaignService->deleteMonthlyVolunteer((int)$monthlyVolunteer->id);
        return redirect()->route('campaigns.show', $campaign);
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
        $this->campaignService->deleteDailyMenu((int)$dailyMenu->id);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم حذف القائمة بنجاح');
    }

    private function hasPendingRequest(Campaign $campaign): bool
    {
        return ChangeRequest::where('model_type', Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->exists();
    }
}
