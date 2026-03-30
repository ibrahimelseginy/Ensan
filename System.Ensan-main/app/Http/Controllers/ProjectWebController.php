<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Supplier;
use App\Models\ProjectMonthlyVolunteer;
use App\Models\ProjectActivity;
use App\Models\ChangeRequest;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectActivityRequest;
use App\Http\Requests\StoreZadFamilyRequest;
use App\Http\Requests\StoreZadResourceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProjectWebController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    public function index(Request $request): View
    {
        $filters  = $request->only(['q', 'status']);
        $projects = $this->projectService->getFilteredProjects($filters, 20);

        return view('projects.index', array_merge(compact('projects'), $filters));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $result = $this->projectService->createProject($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشروع للموافقة.');
        }

        return redirect()->route('projects.show', $result)->with('success', 'تم إضافة المشروع بنجاح.');
    }

    public function show(Project $project): View
    {
        $stats              = $this->projectService->getProjectStats($project);
        $volunteers         = User::where('is_volunteer', true)->orderBy('name')->get();
        $users              = User::orderBy('name')->get();
        $projectVolunteers  = $project->volunteers()->orderBy('name')->get();
        $monthlyVolunteers  = $project->monthlyVolunteers()->with('user')->get();

        return view('projects.show', array_merge(compact('project', 'volunteers', 'users', 'projectVolunteers', 'monthlyVolunteers'), $stats));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $result = $this->projectService->updateProject($project, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المشروع للموافقة.');
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم تعديل المشروع بنجاح.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $result = $this->projectService->deleteProject($project);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المشروع للموافقة.');
        }
        
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:projects,id'
        ]);

        Project::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف المشاريع المحددة بنجاح');
    }

    public function setManager(Project $project, Request $request): RedirectResponse
    {
        $data = $request->validate(['manager_user_id' => 'nullable|exists:users,id']);
        $project->update($data);
        return back()->with('success', 'تم تعيين مدير المشروع بنجاح');
    }

    public function setDeputy(Project $project, Request $request): RedirectResponse
    {
        $data = $request->validate(['deputy_user_id' => 'nullable|exists:users,id']);
        $project->update($data);
        return back()->with('success', 'تم تعيين نائب مدير المشروع بنجاح');
    }

    public function attachVolunteer(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'nullable|string|max:255'
        ]);
        
        $this->projectService->attachVolunteer($project, $data);
        return back()->with('success', 'تم إضافة متطوع للمشروع بنجاح');
    }

    public function detachVolunteer(Project $project, User $user): RedirectResponse
    {
        $this->projectService->detachVolunteer($project, (int)$user->id);
        return back()->with('success', 'تم إزالة متطوع من المشروع بنجاح');
    }

    public function storeMonthlyVolunteer(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000|max:2100',
            'notes'   => 'nullable|string'
        ]);
        
        $this->projectService->storeMonthlyVolunteer($project, $data);
        return back()->with('success', 'تم تسجيل متطوع الشهر بنجاح');
    }

    public function destroyMonthlyVolunteer(Project $project, ProjectMonthlyVolunteer $monthlyVolunteer): RedirectResponse
    {
        $this->projectService->deleteMonthlyVolunteer((int)$monthlyVolunteer->id);
        return back()->with('success', 'تم حذف متطوع الشهر بنجاح');
    }

    public function storeActivity(StoreProjectActivityRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->storeActivity($project, $request->validated());
        return back()->with('success', 'تم إضافة النشاط بنجاح');
    }

    public function destroyActivity(Project $project, ProjectActivity $activity): RedirectResponse
    {
        $this->projectService->deleteActivity((int)$activity->id);
        return back()->with('success', 'تم حذف النشاط بنجاح');
    }

    public function storeZadFamily(StoreZadFamilyRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->storeZadFamily($project, $request->validated());
        return back()->with('success', 'تم إضافة حالة أهالي زاد بنجاح');
    }

    public function destroyZadFamily(Project $project, Beneficiary $beneficiary): RedirectResponse
    {
        $beneficiary->delete();
        return back()->with('success', 'تم إزالة حالة أهالي زاد بنجاح');
    }

    public function storeZadResource(StoreZadResourceRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->storeZadResource($project, $request->validated());
        return back()->with('success', 'تم إضافة مورد مشروع زاد بنجاح');
    }

    public function destroyZadResource(Project $project, Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return back()->with('success', 'تم حذف مورد مشروع زاد بنجاح');
    }
}
