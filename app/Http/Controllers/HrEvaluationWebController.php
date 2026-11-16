<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmployeeEvaluation;
use App\Models\Project;
use App\Models\User;
use App\Models\VolunteerEvaluation;
use App\Services\HrEvaluationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HrEvaluationWebController extends Controller
{
    public function __construct(
        private HrEvaluationService $hrEvaluationService
    ) {}

    public function index(): View
    {
        $stats = $this->hrEvaluationService->getGlobalEvaluationStats();
        return view('hr.evaluations', $stats);
    }

    public function employees(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $department = trim((string) $request->input('department', ''));

        $employees = User::where('is_employee', true)
            ->where('active', true)
            ->with(['latestEvaluation'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('job_title', 'like', "%{$q}%");
                });
            })
            ->when($department !== '', fn ($query) => $query->where('department', $department))
            ->orderBy('name')
            ->get();

        $departments = User::where('is_employee', true)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $recentEvaluations = EmployeeEvaluation::with(['user', 'evaluator'])
            ->orderByDesc('evaluation_date')
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        $criteria = EmployeeEvaluation::CRITERIA;

        return view('hr.evaluations_employees', compact(
            'employees',
            'departments',
            'recentEvaluations',
            'criteria',
            'q',
            'department'
        ));
    }

    public function volunteers(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $projectId = $request->filled('project_id') ? (int) $request->input('project_id') : null;

        $volunteers = User::where('is_volunteer', true)
            ->where('active', true)
            ->with(['project', 'campaign', 'guestHouse', 'volunteerEvaluations'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->when($projectId, function ($query) use ($projectId) {
                $query->where(function ($sub) use ($projectId) {
                    $sub->where('project_id', $projectId)
                        ->orWhereHas('projects', fn ($pq) => $pq->where('projects.id', $projectId));
                });
            })
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('name')->get(['id', 'name']);

        $recentEvaluations = VolunteerEvaluation::with(['user', 'evaluator'])
            ->orderByDesc('evaluation_date')
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        $criteria = VolunteerEvaluation::CRITERIA;

        return view('hr.evaluations_volunteers', compact(
            'volunteers',
            'projects',
            'recentEvaluations',
            'criteria',
            'q',
            'projectId'
        ));
    }
}
