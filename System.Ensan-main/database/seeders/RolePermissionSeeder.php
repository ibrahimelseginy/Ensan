<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ═══════════════════════════════════════════════════════════════
        // تعريف الأدوار وصلاحيات كل دور
        // ═══════════════════════════════════════════════════════════════

        $rolesPermissions = [

            // ─────────────────────────────────────────
            // 1. مدير المؤسسة بالكامل (Admin)
            //    كل الصلاحيات - يتم تجاوزه في الميدلوير
            // ─────────────────────────────────────────
            'admin' => [
                'name' => 'مدير المؤسسة بالكامل',
                'permissions' => 'all', // سيتم إعطاؤه كل الصلاحيات
            ],

            // ─────────────────────────────────────────
            // 2. إدارة (Manager)
            //    كل الصلاحيات - يتم تجاوزه في الميدلوير
            // ─────────────────────────────────────────
            'manager' => [
                'name' => 'إدارة',
                'permissions' => 'all',
            ],

            // ─────────────────────────────────────────
            // 3. مدراء المشاريع (Project Manager)
            //    إدارة المشاريع والحملات والمستفيدين
            // ─────────────────────────────────────────
            'project_manager' => [
                'name' => 'مدراء المشاريع',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',
                    'attachments.delete',
                    'complaints.view',
                    'complaints.create',

                    // المشاريع - تحكم كامل
                    'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
                    'manage_project',
                    'manage_project_volunteers',

                    // الحملات - تحكم كامل
                    'campaigns.view', 'campaigns.create', 'campaigns.edit', 'campaigns.delete',
                    'manage_campaign',

                    // المستفيدين - تحكم كامل
                    'beneficiaries.view', 'beneficiaries.create', 'beneficiaries.edit', 'beneficiaries.delete',

                    // التبرعات - عرض وإضافة
                    'donations.view', 'donations.create',

                    // المتبرعين - عرض
                    'donors.view',

                    // المتطوعين - عرض وإدارة
                    'volunteers.view',
                    'volunteer_attendance.view',

                    // المصروفات - عرض
                    'expenses.view',

                    // المخازن - عرض
                    'warehouses.view',
                    'items.view',
                    'inventory_transactions.view',
                ],
            ],

            // ─────────────────────────────────────────
            // 4. الاستقبال (Receptionist)
            //    استقبال + تبرعات + شكاوى
            // ─────────────────────────────────────────
            'receptionist' => [
                'name' => 'الاستقبال',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',
                    'complaints.view',
                    'complaints.create',

                    // الاستقبال - تحكم كامل
                    'reception.view', 'reception.create', 'reception.edit',

                    // التبرعات - عرض وإضافة
                    'donations.view', 'donations.create',

                    // المتبرعين - عرض وإضافة
                    'donors.view', 'donors.create',

                    // المستفيدين - عرض
                    'beneficiaries.view',

                    // المشاريع والحملات - عرض فقط
                    'projects.view',
                    'campaigns.view',
                ],
            ],

            // ─────────────────────────────────────────
            // 5. الحسابات (Finance / Accountant)
            //    كل الأمور المالية + عرض بيانات ذات صلة
            // ─────────────────────────────────────────
            'finance' => [
                'name' => 'الحسابات',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',
                    'attachments.delete',
                    'complaints.view',
                    'complaints.create',
                    'manage_finance',

                    // دليل الحسابات - تحكم كامل
                    'accounts.view', 'accounts.create', 'accounts.edit', 'accounts.delete',

                    // القيود اليومية - تحكم كامل
                    'journal_entries.view', 'journal_entries.create', 'journal_entries.edit', 'journal_entries.delete',

                    // المصروفات - تحكم كامل
                    'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',

                    // الإقفال المالي - تحكم كامل
                    'financial_closures.view', 'financial_closures.create', 'financial_closures.edit', 'financial_closures.delete',

                    // الرواتب - تحكم كامل
                    'payrolls.view', 'payrolls.create', 'payrolls.edit', 'payrolls.delete',
                    'manage_payrolls',

                    // التبرعات - عرض وإضافة وتعديل
                    'donations.view', 'donations.create', 'donations.edit',

                    // المتبرعين - عرض وإضافة
                    'donors.view', 'donors.create',

                    // المستفيدين - عرض
                    'beneficiaries.view',

                    // المشاريع والحملات - عرض
                    'projects.view',
                    'campaigns.view',

                    // المخازن - عرض
                    'warehouses.view',
                    'items.view',
                    'inventory_transactions.view',

                    // المستخدمين - عرض (لربط الرواتب)
                    'users.view',
                ],
            ],

            // ─────────────────────────────────────────
            // 6. الموارد البشرية (HR)
            //    إدارة الموظفين والمتطوعين والحضور والرواتب
            // ─────────────────────────────────────────
            'hr' => [
                'name' => 'الموارد البشرية',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'attachments.create',
                    'attachments.delete',
                    'complaints.view',
                    'complaints.create',
                    'manage_employees',
                    'manage_volunteers_hr',
                    'manage_payrolls',

                    // المستخدمين والموظفين - تحكم كامل
                    'users.view', 'users.create', 'users.edit', 'users.delete',

                    // حضور الموظفين - تحكم كامل
                    'employee_attendance.view', 'employee_attendance.create', 'employee_attendance.edit',

                    // مهام الموظفين - تحكم كامل
                    'employee_tasks.view', 'employee_tasks.create', 'employee_tasks.edit', 'employee_tasks.delete',

                    // الإجازات - تحكم كامل
                    'leaves.view', 'leaves.create', 'leaves.edit', 'leaves.delete',

                    // المهام العامة - تحكم كامل
                    'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
                    'view_own_tasks',

                    // الرواتب - تحكم كامل
                    'payrolls.view', 'payrolls.create', 'payrolls.edit', 'payrolls.delete',

                    // التقييمات
                    'hr.evaluations',

                    // المتطوعين - تحكم كامل
                    'volunteers.view', 'volunteers.create', 'volunteers.edit', 'volunteers.delete',

                    // حضور المتطوعين
                    'volunteer_attendance.view', 'volunteer_attendance.create', 'volunteer_attendance.edit',

                    // مهام المتطوعين
                    'volunteer_tasks.view', 'volunteer_tasks.create', 'volunteer_tasks.edit', 'volunteer_tasks.delete',

                    // ساعات التطوع
                    'volunteer_hours.view', 'volunteer_hours.create', 'volunteer_hours.edit',
                    'log_volunteer_hours',

                    // المشاريع والحملات - عرض (لربط الموظفين)
                    'projects.view',
                    'campaigns.view',
                ],
            ],

            // ─────────────────────────────────────────
            // 7. مدراء دار الضيافة (Guest House Manager)
            //    إدارة دار الضيافة ومساحات العمل
            // ─────────────────────────────────────────
            'guest_house_manager' => [
                'name' => 'مدراء دار الضيافه',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',
                    'complaints.view',
                    'complaints.create',
                    'manage_guest_house',

                    // دار الضيافة - تحكم كامل
                    'guest_houses.view', 'guest_houses.create', 'guest_houses.edit', 'guest_houses.delete',
                    'guest_houses.set_manager',
                    'guest_houses.manage_volunteers',
                    'guest_houses.manage_monthly_volunteers',

                    // مساحات العمل - تحكم كامل
                    'workspaces.view', 'workspaces.create', 'workspaces.edit', 'workspaces.delete',

                    // المتطوعين - عرض
                    'volunteers.view',

                    // المصروفات - عرض وإضافة
                    'expenses.view', 'expenses.create',
                ],
            ],

            // ─────────────────────────────────────────
            // 8. المسؤول التسويقي (Marketer)
            //    إدارة الموقع والتطبيق
            // ─────────────────────────────────────────
            'marketer' => [
                'name' => 'المسؤول التسويقي',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',

                    // الموقع - تحكم كامل
                    'website.view', 'website.create', 'website.edit', 'website.delete',

                    // التطبيق - تحكم كامل
                    'mobile.view', 'mobile.create', 'mobile.edit', 'mobile.delete',

                    // المشاريع والحملات - عرض (للمحتوى)
                    'projects.view',
                    'campaigns.view',
                ],
            ],

            // ─────────────────────────────────────────
            // 9. اللوجستيك (Logistics)
            //    إدارة المناديب وخطوط السير والرحلات
            // ─────────────────────────────────────────
            'logistics' => [
                'name' => 'اللوجستيك',
                'permissions' => [
                    'dashboard.view',
                    'notifications.view',
                    'view_own_tasks',
                    'attachments.create',
                    'complaints.view',
                    'complaints.create',
                    'manage_logistics',

                    // المناديب - تحكم كامل
                    'delegates.view', 'delegates.create', 'delegates.edit', 'delegates.delete',

                    // خطوط السير - تحكم كامل
                    'travel_routes.view', 'travel_routes.create', 'travel_routes.edit', 'travel_routes.delete',

                    // الرحلات - تحكم كامل
                    'trips.view', 'trips.create', 'trips.edit', 'trips.delete',

                    // المخازن - عرض
                    'warehouses.view',
                    'items.view',
                    'inventory_transactions.view', 'inventory_transactions.create',

                    // التبرعات - عرض
                    'donations.view',

                    // المستفيدين - عرض
                    'beneficiaries.view',
                ],
            ],
        ];

        // ═══════════════════════════════════════════════════════════════
        // تطبيق الصلاحيات
        // ═══════════════════════════════════════════════════════════════

        $allPermissions = Permission::all();

        foreach ($rolesPermissions as $roleKey => $config) {
            // إنشاء أو تحديث الدور
            $role = Role::firstOrCreate(
            ['key' => $roleKey],
            ['name' => $config['name']]
            );

            // تحديث اسم الدور لو اتغير
            $role->update(['name' => $config['name']]);

            if ($config['permissions'] === 'all') {
                // إعطاء كل الصلاحيات
                $role->permissions()->sync($allPermissions->pluck('id')->toArray());
            }
            else {
                // إعطاء صلاحيات محددة
                $permissionIds = $allPermissions
                    ->whereIn('key', $config['permissions'])
                    ->pluck('id')
                    ->toArray();

                $role->permissions()->sync($permissionIds);
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // حذف الأدوار المكررة أو الغير مستخدمة
        // ═══════════════════════════════════════════════════════════════
        $validRoleKeys = array_keys($rolesPermissions);
        $orphanRoles = Role::whereNotIn('key', $validRoleKeys)
            ->whereDoesntHave('users')
            ->get();

        foreach ($orphanRoles as $orphan) {
            $orphan->permissions()->detach();
            $orphan->delete();
        }
    }
}
