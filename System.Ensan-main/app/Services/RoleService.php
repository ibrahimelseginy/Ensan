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
use Illuminate\Validation\ValidationException;

final readonly class RoleService
{
    private const PROTECTED_ROLE_KEYS = ['admin', 'manager'];

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

    public function syncEntityPermissions(): void
    {
        // 1. Projects (excludes guest house scope)
        $projects = \App\Models\Project::all();
        $validProjectIds = $projects->pluck('id')->toArray();

        foreach ($projects as $project) {
            \App\Models\Permission::updateOrCreate(
                ['key' => "projects.manage.{$project->id}"],
                ['name' => "إدارة مشروع: {$project->name}"]
            );
        }

        // Clean up non-project records from projects.manage
        \App\Models\Permission::where('key', 'like', 'projects.manage.%')
            ->get()
            ->each(function ($p) use ($validProjectIds) {
                $id = (int) str_replace('projects.manage.', '', $p->key);
                if (!in_array($id, $validProjectIds, true)) {
                    $p->delete();
                }
            });

        // 2. Campaigns
        $campaigns = \App\Models\Campaign::all();
        foreach ($campaigns as $campaign) {
            \App\Models\Permission::updateOrCreate(
                ['key' => "campaigns.manage.{$campaign->id}"],
                ['name' => "إدارة حملة: {$campaign->name}"]
            );
        }

        // 3. Guest Houses
        $guestHouses = \App\Models\GuestHouse::all();
        foreach ($guestHouses as $gh) {
            \App\Models\Permission::updateOrCreate(
                ['key' => "guest_houses.manage.{$gh->id}"],
                ['name' => "إدارة دار ضيافة: {$gh->name}"]
            );
        }
    }

    public function getPermissionGroups(): Collection
    {
        $this->syncEntityPermissions();
        $groupLabels = $this->getGroupLabels();

        return Permission::orderBy('key')->get()->groupBy(function (Permission $permission) use ($groupLabels) {
            if (preg_match('/^projects\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['projects_specific'];
            }
            if (preg_match('/^campaigns\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['campaigns_specific'];
            }
            if (preg_match('/^guest_houses\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['guest_houses_specific'];
            }

            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function getGroupedRolePermissions(Role $role): Collection
    {
        $this->syncEntityPermissions();
        $groupLabels = $this->getGroupLabels();

        return $role->permissions->groupBy(function (Permission $permission) use ($groupLabels) {
            if (preg_match('/^projects\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['projects_specific'];
            }
            if (preg_match('/^campaigns\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['campaigns_specific'];
            }
            if (preg_match('/^guest_houses\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['guest_houses_specific'];
            }

            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function createRole(array $data): Role
    {
        $permissions = $this->withDashboardPermission($data['permissions'] ?? []);
        unset($data['permissions']);
    
        $role = $this->roleRepository->create($data);
        $role->permissions()->sync($permissions);
    
        return $role;
    }

    public function updateRole(Role $role, array $data): mixed
    {
        if (in_array($role->key, self::PROTECTED_ROLE_KEYS, true) && ($data['key'] ?? $role->key) !== $role->key) {
            throw ValidationException::withMessages([
                'key' => 'لا يمكن تغيير معرّف دور أساسي في النظام.',
            ]);
        }

        $originalData = $data;
        $executor = function () use ($role, $originalData) {
            $permissions = $this->withDashboardPermission($originalData['permissions'] ?? []);
            $updateData = $originalData;
            unset($updateData['permissions']);

            $this->roleRepository->update($role, $updateData);
            $role->permissions()->sync($permissions);
            return $role;
        };

        return ChangeRequestService::handleRequest(
            Role::class,
            $role->id,
            'update',
            $data,
            $executor,
            false
        );
    }

    public function deleteRole(Role $role): mixed
    {
        if (in_array($role->key, self::PROTECTED_ROLE_KEYS, true)) {
            throw ValidationException::withMessages([
                'role' => 'لا يمكن حذف دور أساسي في النظام.',
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'لا يمكن حذف دور مرتبط بمستخدمين. انقل المستخدمين إلى دور آخر أولاً.',
            ]);
        }

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
            false
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
            'leaves'                 => 'الإجازات',
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
            'projects'               => 'المشاريع (عام)',
            'projects_specific'      => 'إدارة المشاريع المحددة (مشروع بعينه)',
            'campaigns'              => 'الحملات (عام)',
            'campaigns_specific'     => 'إدارة الحملات المحددة (حملة بعينها)',
            'payroll'                => 'الرواتب',
            'guest_houses'           => 'دار الضيافة (عام)',
            'guest_houses_specific'  => 'إدارة دور الضيافة المحددة (دار بعينها)',
            'workspaces'             => 'مساحات العمل',
            'notifications'          => 'الإشعارات',
            'complaints'             => 'الشكاوى',
            'logs'                   => 'السجلات',
            'roles'                  => 'الأدوار والصلاحيات',
            'attachments'            => 'المرفقات',
            'tasks'                  => 'المهام العامة',
            'audits'                 => 'سجلات النظام (Logs)',
            'reports'                => 'التقارير',
            'reception'              => 'الاستقبال',
            'visits'                 => 'الزيارات الميدانية',
            'website'                => 'إدارة الموقع الإلكتروني',
            'mobile'                 => 'إدارة تطبيق الموبايل',
            'school_collaborations'  => 'تعاونات المدارس',
            'memberships'            => 'العضويات',
            'oncology_medicine_reps' => 'مناديب أدوية الأورام',
            'kafr_el_sheikh_brokers' => 'سماسرة كفر الشيخ',
            'kafr_el_sheikh_deliveries' => 'توصيلات كفر الشيخ',
            'kafr_el_sheikh_services' => 'خدمات كفر الشيخ',
            'tanta_workers'          => 'عمال باليومية (طنطا)',
            'ramadan_bags'           => 'شنط رمضان',
            'ramadan_iftars'         => 'إفطارات رمضان',
            'change_requests'        => 'طلبات المراجعة',
            'revenues'               => 'الإيرادات والتحليل',
        ];
    }

    private function withDashboardPermission(array $permissionIds): array
    {
        $dashboardPermissionId = Permission::where('key', 'dashboard.view')->value('id');

        if ($dashboardPermissionId) {
            $permissionIds[] = (int) $dashboardPermissionId;
        }

        return array_values(array_unique(array_map('intval', $permissionIds)));
    }
}
