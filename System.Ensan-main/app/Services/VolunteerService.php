<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\VolunteerHour;
use App\Models\Task;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\ChangeRequest;
use App\Repositories\VolunteerRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

final readonly class VolunteerService
{
    public function __construct(
        private VolunteerRepository $volunteerRepository
    ) {}

    public function getAllVolunteers(int $perPage = 20): LengthAwarePaginator
    {
        return $this->volunteerRepository->paginateVolunteers($perPage);
    }

    public function findVolunteerById(int $id): ?User
    {
        return $this->volunteerRepository->findById($id);
    }

    public function getVolunteerStats(): array
    {
        $totalVolunteers  = User::where('is_volunteer', true)->count();
        $activeVolunteers = User::where('is_volunteer', true)->where('active', true)->count();
        
        $projectCounts = User::where('is_volunteer', true)
            ->whereNotNull('project_id')
            ->select('project_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('project_id')
            ->pluck('total', 'project_id');
            
        $projectsWithStats = Project::orderBy('name')->get()->map(function($p) use ($projectCounts) {
            $p->volunteers_count = $projectCounts[$p->id] ?? 0;
            return $p;
        });

        return compact('totalVolunteers', 'activeVolunteers', 'projectsWithStats');
    }

    public function createVolunteer(array $data, $profilePhoto = null): User
    {
        $data['password']     = Hash::make($data['password']);
        $data['is_volunteer'] = true;
        $data['is_employee']  = false;
        
        $user = $this->volunteerRepository->create($data);

        if ($profilePhoto) {
            $user->uploadImage($profilePhoto, 'profile-photos', 'profile_photo_path');
        }

        return $user;
    }

    public function updateVolunteer(User $volunteer, array $data, $profilePhoto = null): mixed
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $executor = function () use ($volunteer, $data, $profilePhoto) {
            $this->volunteerRepository->update($volunteer, $data);
            if ($profilePhoto) {
                $volunteer->uploadImage($profilePhoto, 'profile-photos', 'profile_photo_path');
            }
            return $volunteer;
        };

        return ChangeRequestService::handleRequest(
            User::class,
            $volunteer->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteVolunteer(User $volunteer): mixed
    {
        $executor = function () use ($volunteer) {
            return $this->volunteerRepository->delete($volunteer);
        };

        return ChangeRequestService::handleRequest(
            User::class,
            $volunteer->id,
            'delete',
            [
                'note'  => 'حذف متطوع',
                'name'  => $volunteer->name,
                'phone' => $volunteer->phone,
                'email' => $volunteer->email
            ],
            $executor,
            true
        );
    }

    public function getVolunteerReportData(int $userId): array
    {
        $selected = $this->findVolunteerById($userId);
        if (!$selected) {
            return [
                'assignments' => collect(),
                'summary'     => ['projects' => 0, 'hours' => 0, 'tasks_done' => 0],
                'campaignMap' => collect(),
                'selected'    => null
            ];
        }

        $assignments = $selected->projects()->with(['manager', 'deputy'])->orderBy('name')->get();
        
        $summary = [
            'projects'   => $assignments->count(),
            'hours'      => (float) VolunteerHour::where('user_id', $selected->id)->sum('hours'),
            'tasks_done' => (int) Task::where('assigned_to', $selected->id)->where('status', 'done')->count(),
        ];

        $campaignIds = $assignments->pluck('pivot.campaign_id')->filter()->unique()->values();
        $campaignMap = $campaignIds->isNotEmpty() 
            ? Campaign::whereIn('id', $campaignIds)->get()->keyBy('id')
            : collect();

        return compact('selected', 'assignments', 'summary', 'campaignMap');
    }
}
