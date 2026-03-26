<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Supplier;
use App\Models\ProjectMonthlyVolunteer;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use App\Services\ChangeRequestService;
use App\Models\ChangeRequest;

class ProjectWebController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');
        $status = (string) $request->get('status', '');
        
        $projects = Project::query()
            ->when($q !== '', function($qr) use($q){ $qr->where('name','like','%'.$q.'%'); })
            ->when($status !== '', function($qr) use($status){ $qr->where('status',$status); })
            ->orderBy('name')
            ->paginate(20)
            ->appends(['q'=>$q,'status'=>$status]);

        return view('projects.index', compact('projects','q','status'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'fixed' => 'required|boolean',
            'status' => 'required|in:active,archived',
            'description' => 'nullable|string',
            'category' => 'nullable|string'
        ]);

        $executor = function () use ($data) {
             return Project::create($data);
        };

        $result = ChangeRequestService::handleRequest(
            Project::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشروع للموافقة.');
        }

        return redirect()->route('projects.show', $result)->with('success', 'تم إضافة المشروع بنجاح.');
    }

    public function show(Project $project)
    {
        $project->load(['manager', 'deputy', 'volunteers', 'monthlyVolunteers.user', 'activities.responsible', 'suppliers', 'campaigns']);
        
        $donationsCount = $project->donations()->count();
        $cashSum = (float) $project->donations()->where('type', 'cash')->sum('amount');
        $inKindSum = (float) $project->donations()->where('type', 'in_kind')->sum('estimated_value');
        $donationsTotal = $cashSum + $inKindSum;
        $beneficiariesCount = $project->beneficiaries()->count();
        
        $expensesCount = \App\Models\Expense::where('project_id', $project->id)->count();
        $expensesTotal = (float) \App\Models\Expense::where('project_id', $project->id)->sum('amount');
        
        // Activities revenue (exhibitions + advertising)
        $activitiesRevenue = (float) $project->activities()->sum('revenue');
        $exhibitions = $project->activities()->where('type', 'exhibition')->orderByDesc('activity_date')->get();
        $exhibitionsRevenue = (float) $project->activities()->where('type', 'exhibition')->sum('revenue');
        $advertisingDays = $project->activities()->where('type', 'advertising')->orderByDesc('activity_date')->get();
        
        $netBalance = $donationsTotal - $expensesTotal;
        $cashPct = $donationsTotal > 0 ? round(($cashSum / $donationsTotal) * 100) : 0;
        
        $volunteers = User::where('is_volunteer', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $projectVolunteers = $project->volunteers()->orderBy('name')->get();
        $monthlyVolunteers = $project->monthlyVolunteers()->with('user')->get();

        return view('projects.show', compact(
            'project', 'donationsCount', 'donationsTotal', 'cashSum', 'inKindSum',
            'beneficiariesCount', 'expensesCount', 'expensesTotal',
            'activitiesRevenue', 'exhibitions', 'exhibitionsRevenue', 'advertisingDays',
            'netBalance', 'cashPct', 'volunteers', 'users', 'projectVolunteers', 'monthlyVolunteers'
        ));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'fixed' => 'sometimes|boolean',
            'status' => 'sometimes|in:active,archived',
            'description' => 'nullable|string',
            'category' => 'nullable|string'
        ]);

        $executor = function () use ($project, $data) {
            $project->update($data);
            return $project;
        };

        $result = ChangeRequestService::handleRequest(
            Project::class,
            $project->id,
            'update',
            $data,
            $executor,
            true
        );

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المشروع للموافقة.');
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم تعديل المشروع بنجاح.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id'
        ]);

        Project::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف المشاريع المحددة بنجاح');
    }
    public function destroy(Project $project)
    {
        $executor = function () use ($project) {
            $project->delete();
            return true;
        };

        $result = ChangeRequestService::handleRequest(
            Project::class,
            $project->id,
            'delete',
            request()->all(),
            $executor,
            true
        );

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المشروع للموافقة.');
        }
        
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    public function setManager(Project $project, Request $request)
    {
        $data = $request->validate([
            'manager_user_id' => 'nullable|exists:users,id'
        ]);
        
        $project->update($data);
        return back()->with('success', 'تم تعيين مدير المشروع بنجاح');
    }

    public function setDeputy(Project $project, Request $request)
    {
        $data = $request->validate([
            'deputy_user_id' => 'nullable|exists:users,id'
        ]);
        
        $project->update($data);
        return back()->with('success', 'تم تعيين نائب مدير المشروع بنجاح');
    }

    public function attachVolunteer(Request $request, Project $project)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string'
        ]);
        
        $project->volunteers()->syncWithoutDetaching([$data['user_id'] => ['role' => $data['role']]]);
        return back()->with('success', 'تم إضافة متطوع للمشروع بنجاح');
    }

    public function detachVolunteer(Project $project, User $user)
    {
        $project->volunteers()->detach($user->id);
        return back()->with('success', 'تم إزالة متطوع من المشروع بنجاح');
    }

    public function storeMonthlyVolunteer(Request $request, Project $project)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'notes' => 'nullable|string'
        ]);
        
        $project->monthlyVolunteers()->create($data);
        return back()->with('success', 'تم تسجيل متطوع الشهر بنجاح');
    }

    public function destroyMonthlyVolunteer(Project $project, ProjectMonthlyVolunteer $monthlyVolunteer)
    {
        $monthlyVolunteer->delete();
        return back()->with('success', 'تم حذف متطوع الشهر بنجاح');
    }

    public function storeActivity(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'activity_date' => 'required|date',
            'responsible_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);
        
        $project->activities()->create($data);
        return back()->with('success', 'تم إضافة النشاط بنجاح');
    }

    public function destroyActivity(Project $project, ProjectActivity $activity)
    {
        $activity->delete();
        return back()->with('success', 'تم حذف النشاط بنجاح');
    }

    // Zad Management
    public function storeZadFamily(Request $request, Project $project)
    {
        $data = $request->validate([
            'mother_name' => 'required|string',
            'children_names' => 'nullable|string',
            'phone' => 'nullable|string',
            'backup_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'children_count' => 'nullable|integer',
            'sponsored_children_count' => 'nullable|integer',
            'study_grade' => 'nullable|string',
            'poultry_type' => 'nullable|string',
            'notes_cases' => 'nullable|string',
            'meat' => 'nullable|string'
        ]);

        $data['full_name'] = $data['mother_name']; 
        $data['project_id'] = $project->id;
        $data['assistance_type'] = 'in_kind';
        
        \App\Models\Beneficiary::create($data);
        return back()->with('success', 'تم إضافة حالة أهالي زاد بنجاح');
    }

    public function destroyZadFamily(Project $project, Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return back()->with('success', 'تم إزالة حالة أهالي زاد بنجاح');
    }

    public function storeZadResource(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'merchant_name' => 'nullable|string',
            'source_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $data['project_id'] = $project->id;
        Supplier::create($data);
        return back()->with('success', 'تم إضافة مورد مشروع زاد بنجاح');
    }

    public function destroyZadResource(Project $project, Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'تم حذف مورد مشروع زاد بنجاح');
    }
}
