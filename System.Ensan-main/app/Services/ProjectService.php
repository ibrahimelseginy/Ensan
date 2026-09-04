<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectMonthlyVolunteer;
use App\Models\Beneficiary;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\ChangeRequest;
use App\Repositories\ProjectRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository
    ) {}

    public function getFilteredProjects(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->projectRepository->paginateFiltered($filters, $perPage);
    }

    public function findProjectById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function getProjectStats(Project $project): array
    {
        $activeDonations = Donation::where('project_id', $project->id)
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', '!=', 'cancelled'));
        $activeExpenses = Expense::where('project_id', $project->id)
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', '!=', 'cancelled'));

        $donationsCount = (clone $activeDonations)->count();
        $cashSum        = (float) (clone $activeDonations)->where('type', 'cash')->sum('amount');
        $inKindSum      = (float) (clone $activeDonations)->where('type', 'in_kind')->sum('estimated_value');
        $donationsTotal = $cashSum + $inKindSum;
        
        $expensesCount = (clone $activeExpenses)->count();
        $expensesTotal = (float) (clone $activeExpenses)->sum('amount');
        
        $activitiesRevenue = (float) $project->activities()->sum('revenue');
        $exhibitions       = $project->activities()->where('type', 'exhibition')->orderByDesc('activity_date')->get();
        $exhibitionsRevenue = (float) $project->activities()->where('type', 'exhibition')->sum('revenue');
        $advertisingDays    = $project->activities()->where('type', 'advertising')->orderByDesc('activity_date')->get();
        
        return [
            'donationsCount'     => $donationsCount,
            'donationsTotal'     => $donationsTotal,
            'cashSum'            => $cashSum,
            'inKindSum'          => $inKindSum,
            'beneficiariesCount' => $project->beneficiaries()->count(),
            'expensesCount'      => $expensesCount,
            'expensesTotal'      => $expensesTotal,
            'activitiesRevenue'  => $activitiesRevenue,
            'exhibitions'        => $exhibitions,
            'exhibitionsRevenue' => $exhibitionsRevenue,
            'advertisingDays'    => $advertisingDays,
            'netBalance'         => $donationsTotal - $expensesTotal,
            'cashPct'            => $donationsTotal > 0 ? (int) round(($cashSum / $donationsTotal) * 100) : 0,
        ];
    }

    public function createProject(array $data): mixed
    {
        $executor = fn() => $this->projectRepository->create($data);

        return ChangeRequestService::handleRequest(
            Project::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateProject(Project $project, array $data): mixed
    {
        $executor = function () use ($project, $data) {
            $this->projectRepository->update($project, $data);
            return $project;
        };

        return ChangeRequestService::handleRequest(
            Project::class,
            $project->id,
            'update',
            $data,
            $executor
        );
    }

    public function deleteProject(Project $project): mixed
    {
        $executor = function () use ($project) {
            return $this->projectRepository->delete($project);
        };

        return ChangeRequestService::handleRequest(
            Project::class,
            $project->id,
            'delete',
            ['name' => $project->name],
            $executor,
            true
        );
    }

    public function attachVolunteer(Project $project, array $data): void
    {
        $project->volunteers()->syncWithoutDetaching([$data['user_id'] => ['role' => $data['role'] ?? null]]);
    }

    public function detachVolunteer(Project $project, int $userId): void
    {
        $project->volunteers()->detach($userId);
    }

    public function storeMonthlyVolunteer(Project $project, array $data): void
    {
        $project->monthlyVolunteers()->create($data);
    }

    public function deleteMonthlyVolunteer(int $id): void
    {
        ProjectMonthlyVolunteer::where('id', $id)->delete();
    }

    public function storeActivity(Project $project, array $data): ProjectActivity
    {
        return $project->activities()->create($data);
    }

    public function deleteActivity(int $id): void
    {
        ProjectActivity::where('id', $id)->delete();
    }

    public function storeZadFamily(Project $project, array $data): Beneficiary
    {
        $data['full_name']       = $data['mother_name']; 
        $data['project_id']      = $project->id;
        $data['assistance_type'] = 'in_kind';
        
        return Beneficiary::create($data);
    }

    public function storeZadResource(Project $project, array $data): Supplier
    {
        $data['project_id'] = $project->id;
        return Supplier::create($data);
    }
}
