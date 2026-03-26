<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Define new permissions
        $newPermissions = [
            'dashboard' => ['view' => 'عرض لوحة التحكم'],
            'notifications' => ['view' => 'عرض التنبيهات'],
            'tasks' => ['view' => 'عرض', 'create' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف'],
            'attachments' => ['create' => 'إضافة', 'delete' => 'حذف'],
        ];

        // 2. Insert Permissions
        foreach ($newPermissions as $resource => $actions) {
            foreach ($actions as $action => $label) {
                $key = "{$resource}.{$action}";
                $name = "{$label} " . $this->getResourceLabel($resource);

                if (!DB::table('permissions')->where('key', $key)->exists()) {
                    DB::table('permissions')->insert([
                        'key' => $key,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // 3. Assign to Roles
        $roleAssignments = [
            // Universal access
            'dashboard.view' => ['admin', 'manager', 'hr', 'finance', 'volunteer'],
            'notifications.view' => ['admin', 'manager', 'hr', 'finance', 'volunteer'],

            // Tasks - HR, Manager, Admin
            'tasks.*' => ['admin', 'manager', 'hr'],

            // Attachments - Manager, Admin
            'attachments.*' => ['admin', 'manager'],
        ];

        $allRoles = DB::table('roles')->pluck('id', 'key');
        $allPerms = DB::table('permissions')->pluck('id', 'key');

        foreach ($roleAssignments as $permPattern => $rolesList) {
            // Validate roles first
            $targetRoleIds = [];
            foreach ($rolesList as $rKey) {
                if (isset($allRoles[$rKey])) {
                    $targetRoleIds[] = $allRoles[$rKey];
                }
            }
            if (empty($targetRoleIds))
                continue;

            // Resolve permissions
            $targetPermIds = [];
            if (str_ends_with($permPattern, '.*')) {
                $prefix = substr($permPattern, 0, -2);
                foreach ($allPerms as $k => $pid) {
                    if (str_starts_with($k, $prefix . '.')) {
                        $targetPermIds[] = $pid;
                    }
                }
            } else {
                if (isset($allPerms[$permPattern])) {
                    $targetPermIds[] = $allPerms[$permPattern];
                }
            }

            // Sync/Attach
            foreach ($targetRoleIds as $rid) {
                foreach ($targetPermIds as $pid) {
                    if (!DB::table('permission_role')->where(['role_id' => $rid, 'permission_id' => $pid])->exists()) {
                        DB::table('permission_role')->insert(['role_id' => $rid, 'permission_id' => $pid]);
                    }
                }
            }
        }
    }

    private function getResourceLabel($resource)
    {
        return match ($resource) {
            'dashboard' => '',
            'notifications' => '',
            'tasks' => 'المهام',
            'attachments' => 'المرفقات',
            default => $resource
        };
    }

    public function down(): void
    {
        // No explicit rollback needed for additive permissions usually
    }
};
