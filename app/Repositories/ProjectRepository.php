<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class ProjectRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q      = $filters['q'] ?? null;
        $status = $filters['status'] ?? null;
        $fixed  = $filters['fixed'] ?? null;
        $user   = auth()->user();
        $allowedProjectIds = ($user && method_exists($user, 'getAccessibleProjectIds'))
            ? $user->getAccessibleProjectIds()
            : null;

        return Project::query()
            ->when(is_array($allowedProjectIds), fn($qr) => $qr->whereIn('id', $allowedProjectIds ?: [0]))
            ->when($q, fn($qr) => $qr->where('name', 'like', '%' . $q . '%'))
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($fixed !== null && $fixed !== '', fn($qr) => $qr->where('fixed', (bool) $fixed))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Project
    {
        return Project::with(['manager', 'deputy', 'volunteers', 'monthlyVolunteers.user', 'activities.responsible', 'suppliers', 'campaigns'])->find($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }
}
