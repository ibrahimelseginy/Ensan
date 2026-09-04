<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Donor;
use App\Models\Treasury;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\ProjectMonthlyVolunteer;
use App\Models\ProjectActivity;
use App\Models\ChangeRequest;
use App\Services\ProjectService;
use App\Services\DonationService;
use App\Services\ExpenseService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectActivityRequest;
use App\Http\Requests\StoreZadFamilyRequest;
use App\Http\Requests\StoreZadResourceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class ProjectWebController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private DonationService $donationService,
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): View
    {
        $filters  = $request->only(['q', 'status', 'fixed']);
        $projects = $this->projectService->getFilteredProjects($filters, 20);

        return view('projects.index', array_merge(compact('projects'), $filters));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        try {
            $result = $this->projectService->createProject($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'يوجد مشروع بهذا الاسم بالفعل. يرجى اختيار اسم مختلف.']);
        }

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
        $beneficiaryOptions = Beneficiary::orderBy('full_name')->get(['id', 'code', 'full_name']);
        $sponsors           = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $projectDonors      = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $projectBeneficiaryOptions = Beneficiary::where('project_id', $project->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);
        $projectWarehouses = Warehouse::when(
            Schema::hasColumn('warehouses', 'is_active'),
            fn ($query) => $query->where('is_active', true)
        )->orderBy('name')->get(['id', 'name']);
        $projectDonationTreasuries = Treasury::active()
            ->where(function ($query) use ($project): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE])
                    ->orWhere(function ($projectTreasury) use ($project): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $project->id);
                    });
            })
            ->orderBy('name')
            ->get();
        $projectExpenseTreasuries = Treasury::active()
            ->where(function ($query) use ($project): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
                    ->orWhere(function ($projectTreasury) use ($project): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $project->id);
                    });
            })
            ->orderBy('name')
            ->get();

        return view('projects.show', array_merge(compact(
            'project',
            'volunteers',
            'users',
            'projectVolunteers',
            'monthlyVolunteers',
            'beneficiaryOptions',
            'sponsors',
            'projectDonors',
            'projectBeneficiaryOptions',
            'projectWarehouses',
            'projectDonationTreasuries',
            'projectExpenseTreasuries'
        ), $stats));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        try {
            $result = $this->projectService->updateProject($project, $request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'يوجد مشروع آخر بهذا الاسم. يرجى اختيار اسم مختلف.']);
        }

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
        abort_unless((int) $monthlyVolunteer->project_id === (int) $project->id, 404);

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
        abort_unless((int) $activity->project_id === (int) $project->id, 404);

        $this->projectService->deleteActivity((int)$activity->id);
        return back()->with('success', 'تم حذف النشاط بنجاح');
    }

    public function storeBeneficiaryFile(StoreBeneficiaryRequest $request, Project $project): RedirectResponse
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

        DB::transaction(function () use ($project, $data, $allocatedBeneficiaryIds, $sponsorIds): void {
            $beneficiary = $project->beneficiaries()->create($data);
            $beneficiary->allocatedBeneficiaries()->sync($allocatedBeneficiaryIds);
            $beneficiary->sponsors()->sync($sponsorIds);
        });

        return back()->with('success', 'تم إضافة المستفيد بنجاح');
    }

    public function storeDonation(StoreDonationRequest $request, Project $project): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['guest_house_id']);
            $data['project_id'] = $project->id;
            $data['donationable_type'] = Project::class;
            $data['donationable_id'] = $project->id;

            if (($data['type'] ?? null) === 'cash'
                && ! $this->isAllowedDonationTreasury($project, (int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لتبرعات هذا المشروع.');
            }

            $result = $this->donationService->createDonation($data);

            if ($result instanceof ChangeRequest) {
                return back()->with('success', 'تم إرسال تبرع المشروع للمراجعة.');
            }

            return back()->with('success', 'تم تسجيل تبرع المشروع بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل التبرع: ' . $exception->getMessage());
        }
    }

    public function storeExpense(StoreExpenseRequest $request, Project $project): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['workspace_id'], $data['guest_house_id']);
            $data['project_id'] = $project->id;

            if (! $this->isAllowedExpenseTreasury($project, (int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لمصروفات هذا المشروع.');
            }

            if (! empty($data['beneficiary_id'])
                && ! Beneficiary::whereKey($data['beneficiary_id'])->where('project_id', $project->id)->exists()) {
                throw new \RuntimeException('المستفيد المختار غير مرتبط بهذا المشروع.');
            }

            $result = $this->expenseService->createExpense($data);

            if ($result instanceof ChangeRequest) {
                return back()->with('success', 'تم إرسال مصروف المشروع للمراجعة.');
            }

            return back()->with('success', 'تم تسجيل مصروف المشروع وخصمه من الخزينة بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل المصروف: ' . $exception->getMessage());
        }
    }

    public function updateBeneficiaryFile(Request $request, Project $project, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->project_id === (int) $project->id, 404);

        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'assistance_type' => 'required|in:financial,in_kind,service',
            'notes'           => 'nullable|string',
        ]);

        $beneficiary->update($data);

        return back()->with('success', 'تم تعديل بيانات المستفيد بنجاح');
    }

    public function destroyBeneficiaryFile(Project $project, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->project_id === (int) $project->id, 404);

        $beneficiary->delete();

        return back()->with('success', 'تم حذف المستفيد بنجاح');
    }

    private function isAllowedDonationTreasury(Project $project, int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->where(function ($query) use ($project): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE])
                    ->orWhere(function ($projectTreasury) use ($project): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $project->id);
                    });
            })
            ->exists();
    }

    private function isAllowedExpenseTreasury(Project $project, int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->where(function ($query) use ($project): void {
                $query->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
                    ->orWhere(function ($projectTreasury) use ($project): void {
                        $projectTreasury->where('type', Treasury::TYPE_PROJECT)
                            ->where('project_id', $project->id);
                    });
            })
            ->exists();
    }

    public function storeZadFamily(StoreZadFamilyRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->storeZadFamily($project, $request->validated());
        return back()->with('success', 'تم إضافة حالة أهالي زاد بنجاح');
    }

    public function updateZadFamily(StoreZadFamilyRequest $request, Project $project, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->project_id === (int) $project->id, 404);

        $data = $request->validated();
        $data['full_name'] = $data['mother_name'];
        $beneficiary->update($data);

        return back()->with('success', 'تم تعديل حالة أهالي زاد بنجاح');
    }

    public function destroyZadFamily(Project $project, Beneficiary $beneficiary): RedirectResponse
    {
        abort_unless((int) $beneficiary->project_id === (int) $project->id, 404);

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
