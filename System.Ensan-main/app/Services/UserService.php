<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\ChangeRequest;
use App\Repositories\UserRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function getFilteredUsers(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $users = $this->userRepository->paginateFilteredUsers($filters, $perPage);

        $users->each(function(User $user) {
            $user->pendingRequest = ChangeRequest::where('model_type', User::class)
                ->where('model_id', $user->id)
                ->where('status', 'pending')
                ->first();
        });

        return $users;
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data, array $files = []): User
    {
        $data['password'] = Hash::make($data['password']);
        
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = $this->userRepository->create($data);

        // Files
        if (isset($files['profile_photo'])) {
            $user->uploadImage($files['profile_photo'], 'profile-photos', 'profile_photo_path');
        }

        foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
            if (isset($files[$field])) {
                $user->uploadImage($files[$field], 'user-docs', $field);
            }
        }

        if (!empty($roles)) {
            $user->roles()->sync($roles);
        }

        return $user;
    }

    public function updateUser(User $user, array $data, array $files = []): User
    {
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->saveQuietly();
        }
        unset($data['password']);

        // Roles
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        unset($data['roles']);

        // Main update
        if (!empty($data)) {
            $this->userRepository->update($user, $data);
        }

        // Files
        if (isset($files['profile_photo'])) {
            $user->uploadImage($files['profile_photo'], 'profile-photos', 'profile_photo_path');
        }

        foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
            if (isset($files[$field])) {
                $user->uploadImage($files[$field], 'user-docs', $field);
            }
        }

        return $user;
    }

    public function deleteUser(User $user): mixed
    {
        $executor = function () use ($user) {
            return $this->userRepository->delete($user);
        };

        return ChangeRequestService::handleRequest(
            User::class,
            $user->id,
            'delete',
            [
                'note'         => 'حذف مستخدم',
                'name'         => $user->name,
                'job_title'    => $user->job_title,
                'phone'        => $user->phone,
                'is_employee'  => $user->is_employee,
                'is_volunteer' => $user->is_volunteer,
            ],
            $executor,
            true
        );
    }

    public function attachRole(User $user, int $roleId): void
    {
        $user->roles()->syncWithoutDetaching([$roleId]);
    }

    public function detachRole(User $user, int $roleId): void
    {
        $user->roles()->detach($roleId);
    }
}
