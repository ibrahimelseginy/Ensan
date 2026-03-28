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

final readonly class VolunteerWebController extends Controller
{
    public function __construct(
        private VolunteerService $volunteerService
    ) {}

    public function index(): View
    {
        $volunteers = $this->volunteerService->getAllVolunteers(20);
        $stats      = $this->volunteerService->getVolunteerStats();

        return view('volunteers.index', array_merge(compact('volunteers'), $stats));
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
