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
        $data['is_employee'] = $data['is_employee'] ?? true;
        $data['is_volunteer'] = $data['is_volunteer'] ?? false;
        
        $roles = $data['roles'] ?? [];
        $directPermissions = $data['direct_permissions'] ?? [];
        unset(
            $data['roles'],
            $data['direct_permissions'],
            $data['profile_photo'],
            $data['contract_image'],
            $data['criminal_record_image'],
            $data['id_card_image']
        );

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

        if (!empty($directPermissions) && \Illuminate\Support\Facades\Schema::hasTable('permission_user')) {
            $user->directPermissions()->sync(array_values(array_unique(array_map('intval', $directPermissions))));
        }

        $user->clearPermissionCache();

        return $user;
    }

    public function updateUser(User $user, array $data, array $files = []): mixed
    {
        unset(
            $data['profile_photo'],
            $data['contract_image'],
            $data['criminal_record_image'],
            $data['id_card_image']
        );

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $hasRolesSync = array_key_exists('roles', $data) || !empty($data['roles_present']);
        $roles = $hasRolesSync ? ($data['roles'] ?? []) : null;

        $hasDirectPermsSync = array_key_exists('direct_permissions', $data) || !empty($data['direct_permissions_present']);
        $directPermissions = $hasDirectPermsSync ? ($data['direct_permissions'] ?? []) : null;

        $payloadData = $data;
        unset(
            $data['roles'],
            $data['roles_present'],
            $data['direct_permissions'],
            $data['direct_permissions_present']
        );

        $currentUser = request()->user() ?? auth()->user();
        $isAdmin = $currentUser && (
            $currentUser->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة']) ||
            \Illuminate\Support\Facades\Gate::allows('users.edit')
        );

        if ($isAdmin) {
            if (!$user->is_volunteer && !isset($data['is_employee'])) {
                $data['is_employee'] = true;
            }
        } else {
            // Store requested files without changing the current profile. Their
            // paths are applied only after the admin approves the change request.
            $fileColumns = [
                'profile_photo'         => ['profile_photo_path', 'pending/user-profile'],
                'contract_image'        => ['contract_image', 'pending/user-docs'],
                'criminal_record_image' => ['criminal_record_image', 'pending/user-docs'],
                'id_card_image'         => ['id_card_image', 'pending/user-docs'],
            ];

            foreach ($fileColumns as $fileKey => [$column, $directory]) {
                if (isset($files[$fileKey])) {
                    $path = app(ImageUploadService::class)->upload($files[$fileKey], $directory);
                    if ($path) {
                        $data[$column] = $path;
                    }
                }
            }
        }

        $executor = function () use ($user, $data, $roles, $directPermissions, $files) {
            if ($roles !== null) {
                $user->roles()->sync(array_values(array_unique(array_map('intval', (array) $roles))));
            }

            if ($directPermissions !== null && \Illuminate\Support\Facades\Schema::hasTable('permission_user')) {
                $user->directPermissions()->sync(array_values(array_unique(array_map('intval', (array) $directPermissions))));
            }

            if (!empty($data)) {
                $this->userRepository->update($user, $data);
            }

            if (isset($files['profile_photo'])) {
                $user->uploadImage($files['profile_photo'], 'profile-photos', 'profile_photo_path');
            }

            foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
                if (isset($files[$field])) {
                    $user->uploadImage($files[$field], 'user-docs', $field);
                }
            }

            $user->clearPermissionCache();
            return $user->refresh();
        };

        if ($roles !== null) {
            $payloadData['roles'] = array_values(array_map('intval', (array) $roles));
        }
        if ($directPermissions !== null) {
            $payloadData['direct_permissions'] = array_values(array_map('intval', (array) $directPermissions));
        }

        return ChangeRequestService::handleRequest(
            User::class,
            $user->id,
            'update',
            $payloadData,
            $executor
        );
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
