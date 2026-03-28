<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Models\ChangeRequest;
use App\Repositories\RoleRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class RoleService
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {}

    public function getFilteredRoles(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->roleRepository->paginateFiltered($filters, $perPage);
    }

    public function findRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function getPermissionGroups(): Collection
    {
        $groupLabels = $this->getGroupLabels();

        return Permission::orderBy('key')->get()->groupBy(function (Permission $permission) use ($groupLabels) {
            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function getGroupedRolePermissions(Role $role): Collection
    {
        $groupLabels = $this->getGroupLabels();

        return $role->permissions->groupBy(function (Permission $permission) use ($groupLabels) {
            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function createRole(array $data): Role
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = $this->roleRepository->create($data);
        if (!empty($permissions)) {
            $role->permissions()->sync($permissions);
        }

        return $role;
    }

    public function updateRole(Role $role, array $data): mixed
    {
        $executor = function () use ($role, $data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $this->roleRepository->update($role, $data);
            if (!empty($permissions)) {
                $role->permissions()->sync($permissions);
            }
            return $role;
        };

        return ChangeRequestService::handleRequest(
            Role::class,
            $role->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteRole(Role $role): mixed
    {
        $executor = function () use ($role) {
            return $this->roleRepository->delete($role);
        };

        return ChangeRequestService::handleRequest(
            Role::class,
            $role->id,
            'delete',
            [
                'note' => 'حذف دور وصلاحيات',
                'name' => $role->name,
                'key'  => $role->key
            ],
            $executor,
            true
        );
    }

    private function getGroupLabels(): array
    {
        return [
            'dashboard'              => 'لوحة التحكم',
            'donors'                 => 'المتبرعون',
            'donations'              => 'التبرعات',
            'beneficiaries'          => 'المستفيدون',
            'delegates'              => 'اللوجيستك - المناديب',
            'travel_routes'          => 'اللوجيستك - خطوط السير',
            'trips'                  => 'اللوجيستك - الرحلات',
            'users'                  => 'الموظفين',
            'employee_attendance'    => 'حضور الموظفين',
            'employee_tasks'         => 'مهام الموظفين',
            'volunteers'             => 'المتطوعين',
            'volunteer_attendance'   => 'حضور المتطوعين',
            'volunteer_tasks'        => 'مهام المتطوعين',
            'volunteer_hours'        => 'ساعات التطوع',
            'hr'                     => 'الموارد البشرية - تقييمات',
            'accounts'               => 'دليل الحسابات',
            'journal_entries'        => 'القيود اليومية',
            'expenses'               => 'المصروفات',
            'financial_closures'     => 'الإقفال المالي',
            'warehouses'             => 'المخازن',
            'items'                  => 'الأصناف',
            'inventory_transactions' => 'حركات المخزن',
            'suppliers'              => 'الموردين',
            'projects'               => 'المشاريع',
            'campaigns'              => 'الحملات',
            'payroll'                => 'الرواتب',
            'guest_houses'           => 'دار الضيافة',
            'workspaces'             => 'مساحات العمل',
            'notifications'          => 'الإشعارات',
            'complaints'             => 'الشكاوى',
            'logs'                   => 'السجلات',
            'roles'                  => 'الأدوار والصلاحيات',
            'attachments'            => 'المرفقات',
            'tasks'                  => 'المهام العامة',
            'audits'                 => 'سجلات النظام (Logs)',
            'website'                => 'إدارة الموقع الإلكتروني',
            'mobile'                 => 'إدارة تطبيق الموبايل',
        ];
    }
}
