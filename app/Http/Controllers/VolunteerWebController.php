<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\GuestHouse;
use App\Models\ChangeRequest;
use App\Services\VolunteerService;
use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Requests\UpdateVolunteerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class VolunteerWebController extends Controller
{
    public function __construct(
        private VolunteerService $volunteerService
    ) {}

    public function index(Request $request): View
    {
        $projectId    = $request->filled('project_id') ? (int) $request->input('project_id') : null;
        $campaignId   = $request->filled('campaign_id') ? (int) $request->input('campaign_id') : null;
        $guestHouseId = $request->filled('guest_house_id') ? (int) $request->input('guest_house_id') : null;
        $unassigned   = $request->boolean('unassigned');
        $q            = trim((string) $request->input('q', ''));

        $volunteersQuery = User::where('is_volunteer', true)
            ->with(['project', 'campaign', 'guestHouse', 'projects', 'volunteerEvaluations'])
            ->when($projectId, function ($query) use ($projectId) {
                $query->where(function ($sub) use ($projectId) {
                    $sub->where('project_id', $projectId)
                        ->orWhereHas('projects', fn ($pq) => $pq->where('projects.id', $projectId));
                });
            })
            ->when($campaignId, fn ($query) => $query->where('campaign_id', $campaignId))
            ->when($guestHouseId, fn ($query) => $query->where('guest_house_id', $guestHouseId))
            ->when($unassigned, function ($query) {
                $query->whereNull('project_id')
                    ->whereNull('campaign_id')
                    ->whereNull('guest_house_id')
                    ->whereDoesntHave('projects');
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name');

        $volunteers = $volunteersQuery->paginate(20)->withQueryString();
        $stats      = $this->volunteerService->getVolunteerStats();

        // Build full Project / Campaign / GuestHouse files (each containing all assigned volunteers)
        $allVolunteers = User::where('is_volunteer', true)
            ->with(['project', 'campaign', 'guestHouse', 'projects', 'volunteerEvaluations'])
            ->orderBy('name')
            ->get();

        $allProjects = Project::orderBy('name')->get();
        $projectFiles = $allProjects->map(function (Project $project) use ($allVolunteers) {
            $members = $allVolunteers->filter(function (User $vol) use ($project) {
                return (int) $vol->project_id === (int) $project->id
                    || $vol->projects->contains('id', $project->id);
            })->values();

            return [
                'project' => $project,
                'members' => $members,
                'count'   => $members->count(),
            ];
        })->filter(fn (array $item) => $item['count'] > 0)->values();

        $campaignFiles = Campaign::orderBy('name')->get()->map(function (Campaign $campaign) use ($allVolunteers) {
            $members = $allVolunteers->where('campaign_id', $campaign->id)->values();
            return [
                'campaign' => $campaign,
                'members'  => $members,
                'count'    => $members->count(),
            ];
        })->filter(fn (array $item) => $item['count'] > 0)->values();

        $guestHouseFiles = GuestHouse::orderBy('name')->get()->map(function (GuestHouse $gh) use ($allVolunteers) {
            $members = $allVolunteers->where('guest_house_id', $gh->id)->values();
            return [
                'guest_house' => $gh,
                'members'     => $members,
                'count'       => $members->count(),
            ];
        })->filter(fn (array $item) => $item['count'] > 0)->values();

        $unassignedVolunteers = $allVolunteers->filter(function (User $vol) {
            return !$vol->project_id && !$vol->campaign_id && !$vol->guest_house_id && $vol->projects->isEmpty();
        })->values();

        $selectedProject = $projectId ? $allProjects->firstWhere('id', $projectId) : null;

        return view('volunteers.index', array_merge(
            compact(
                'volunteers',
                'projectFiles',
                'campaignFiles',
                'guestHouseFiles',
                'unassignedVolunteers',
                'selectedProject',
                'projectId',
                'campaignId',
                'guestHouseId',
                'unassigned',
                'q'
            ),
            $stats
        ));
    }

    public function create(): View
    {
        $projects    = Project::orderBy('name')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('volunteers.create', compact('projects', 'campaigns', 'guestHouses'));
    }

    public function store(StoreVolunteerRequest $request): RedirectResponse
    {
        $profilePhoto = $request->file('profile_photo');
        $user         = $this->volunteerService->createVolunteer($request->validated(), $profilePhoto);

        return redirect()->route('volunteers.show', $user)->with('success', 'تم تسجيل المتطوع بنجاح');
    }

    public function show(User $volunteer): View
    {
        return view('volunteers.show', compact('volunteer'));
    }

    public function edit(User $volunteer): View
    {
        $projects    = Project::orderBy('name')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('volunteers.edit', compact('volunteer', 'projects', 'campaigns', 'guestHouses'));
    }

    public function update(UpdateVolunteerRequest $request, User $volunteer): RedirectResponse
    {
        $profilePhoto = $request->file('profile_photo');
        $result       = $this->volunteerService->updateVolunteer($volunteer, $request->validated(), $profilePhoto);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل بيانات المتطوع للموافقة');
        }

        return redirect()->route('volunteers.show', $volunteer)->with('success', 'تم تحديث بيانات المتطوع بنجاح');
    }

    public function destroy(User $volunteer): RedirectResponse
    {
        $result = $this->volunteerService->deleteVolunteer($volunteer);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المتطوع للموافقة');
        }

        return redirect()->route('volunteers.index')->with('success', 'تم حذف المتطوع بنجاح');
    }

    public function reports(Request $request): View
    {
        $volunteers = User::where('is_volunteer', true)->orderBy('name')->get();
        $userId     = (int) $request->get('user_id', 0);

        $reportData = $this->volunteerService->getVolunteerReportData($userId);

        return view('volunteers.reports', array_merge($reportData, compact('volunteers', 'userId')));
    }
}
