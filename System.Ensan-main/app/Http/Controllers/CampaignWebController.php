<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignDailyMenu;
use Illuminate\Http\Request;

final class CampaignWebController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');
        $status = (string) $request->get('status', '');
        $year = $request->get('season_year');
        $campaigns = Campaign::query()
            ->when($q !== '', function($qr) use($q){ $qr->where('name','like','%'.$q.'%'); })
            ->when($status !== '', function($qr) use($status){ $qr->where('status',$status); })
            ->when(!empty($year), function($qr) use($year){ $qr->where('season_year',$year); })
            ->orderByDesc('season_year')->orderBy('name')
            ->paginate(20)
            ->appends(['q'=>$q,'status'=>$status,'season_year'=>$year]);

        // Add pending check for each campaign
        $campaigns->each(function($c) {
            $c->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
                ->where('model_id', $c->id)
                ->where('status', 'pending')
                ->first();
        });
        return view('campaigns.index', compact('campaigns','q','status','year'));
    }

    public function create()
    {
        $projects = \App\Models\Project::orderBy('name')->get();
        return view('campaigns.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'season_year' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,archived',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        // Use ChangeRequestService
        $executor = function () use ($data) {
             return \App\Models\Campaign::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Campaign::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحملة للموافقة.');
        }

        return redirect()->route('campaigns.show', $result)->with('success', 'تم إضافة الحملة بنجاح.');
    }

    public function show(Campaign $campaign)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $donationsCount = \App\Models\Donation::where('campaign_id',$campaign->id)->count();
        $cashSum = (float) \App\Models\Donation::where('campaign_id',$campaign->id)->where('type','cash')->sum('amount');
        $inKindSum = (float) \App\Models\Donation::where('campaign_id',$campaign->id)->where('type','in_kind')->sum('estimated_value');
        $beneficiariesCount = \App\Models\Beneficiary::where('campaign_id',$campaign->id)->count();
        $expensesCount = \App\Models\Expense::where('campaign_id',$campaign->id)->count();
        $latestDonations = \App\Models\Donation::where('campaign_id',$campaign->id)->orderByDesc('id')->limit(5)->get();
        $latestExpenses = \App\Models\Expense::where('campaign_id',$campaign->id)->orderByDesc('id')->limit(5)->get();
        $latestBeneficiaries = \App\Models\Beneficiary::where('campaign_id',$campaign->id)->orderByDesc('id')->limit(5)->get();
        
        $expensesTotal = (float) \App\Models\Expense::where('campaign_id',$campaign->id)->sum('amount');
        $donationsTotal = $cashSum + $inKindSum;
        $netBalance = $donationsTotal - $expensesTotal;
        $cashPct = $donationsTotal > 0 ? round(($cashSum/$donationsTotal)*100) : 0;
        
        $volunteers = \App\Models\User::where('is_volunteer',true)->orderBy('name')->get();
        $campaignVolunteers = $campaign->volunteers()->orderBy('name')->get();
        $monthlyVolunteers = $campaign->monthlyVolunteers()->with('user')->get();
        $dailyMenus = $campaign->dailyMenus()->with('responsible')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('campaigns.show', compact('campaign','donationsCount','cashSum','inKindSum','beneficiariesCount','expensesCount','latestDonations','latestExpenses','latestBeneficiaries','expensesTotal','donationsTotal','netBalance','cashPct','campaignVolunteers','volunteers','monthlyVolunteers','dailyMenus','users'));
    }

    public function setManager(Campaign $campaign, Request $request)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_photo' => 'nullable|image|max:5120'
        ]);

        if ($request->hasFile('manager_photo')) {
            $file = $request->file('manager_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/managers'), $filename);
            $data['manager_photo_url'] = '/uploads/managers/' . $filename;
        }
        
        unset($data['manager_photo']);
        
        $executor = function() use ($campaign, $data) {
            $campaign->update($data);
            return $campaign;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Campaign::class,
            $campaign->id,
            'update',
            $data,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تغيير المدير للمراجعة');
        }

        return redirect()->route('campaigns.show', $campaign);
    }

    public function attachVolunteer(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string',
            'started_at' => 'nullable|date',
            'hours' => 'nullable|numeric'
        ]);
        $campaign->volunteers()->syncWithoutDetaching([
            $data['user_id'] => [
                'role' => $data['role'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'hours' => $data['hours'] ?? 0
            ]
        ]);
        return redirect()->route('campaigns.show', $campaign);
    }

    public function detachVolunteer(Campaign $campaign, \App\Models\User $user)
    {
        $campaign->volunteers()->detach($user->id);
        return redirect()->route('campaigns.show', $campaign);
    }

    public function storeMonthlyVolunteer(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'notes' => 'nullable|string'
        ]);

        $exists = $campaign->monthlyVolunteers()
            ->where('user_id', $data['user_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if (!$exists) {
            $campaign->monthlyVolunteers()->create($data);
        }

        return redirect()->route('campaigns.show', $campaign);
    }

    public function destroyMonthlyVolunteer(Campaign $campaign, \App\Models\CampaignMonthlyVolunteer $monthlyVolunteer)
    {
        $monthlyVolunteer->delete();
        return redirect()->route('campaigns.show', $campaign);
    }

    public function edit(Campaign $campaign)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $projects = \App\Models\Project::orderBy('name')->get();
        return view('campaigns.edit', compact('campaign','projects'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'season_year' => 'sometimes|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'sometimes|in:active,archived',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        $executor = function () use ($campaign, $data) {
            $campaign->update($data);
            return $campaign;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Campaign::class,
            $campaign->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الحملة للموافقة.');
        }

        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم تعديل الحملة بنجاح.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:campaigns,id'
        ]);

        Campaign::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف الحملات المحددة بنجاح');
    }
    public function destroy(Campaign $campaign)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)
            ->where('model_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحملة لديها طلب مراجعة حالياً');
        }

        $executor = function () use ($campaign) {
            $campaign->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Campaign::class,
            $campaign->id,
            'delete',
            request()->all(),
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الحملة للموافقة.');
        }
        
        return back()->with('success', 'تم حذف الحملة بنجاح');
    }

    public function storeDailyMenu(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'day_date' => 'required|date',
            'responsible_user_id' => 'nullable|exists:users,id',
            'meal_type' => 'nullable|string',
            'menu' => 'nullable|string',
            'meal_count' => 'nullable|integer|min:0',
            'ingredients' => 'nullable|string',
        ]);

        $campaign->dailyMenus()->create($data);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم إضافة القائمة بنجاح');
    }

    public function destroyDailyMenu(Campaign $campaign, CampaignDailyMenu $dailyMenu)
    {
        $dailyMenu->delete();
        return redirect()->route('campaigns.show', $campaign)->with('success', 'تم حذف القائمة بنجاح');
    }
}

