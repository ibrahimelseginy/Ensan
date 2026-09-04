<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Update Role Descriptions
        $roles = [
            'admin' => 'صلاحيات كاملة للتحكم في النظام وإدارة جميع الموارد والمستخدمين.',
            'finance' => 'إدارة التبرعات، المصروفات، والتقارير المالية والمحاسبية.',
            'manager' => 'إشراف عام على العمليات والمشاريع والموظفين.',
            'HR' => 'إدارة شؤون الموظفين، الحضور والانصراف، والرواتب.',
            'volunteer' => 'المشاركة في الأنشطة والفعاليات الميدانية.'
        ];

        foreach ($roles as $key => $desc) {
            DB::table('roles')->where('key', $key)->update(['description' => $desc]);
        }

        // 2. Define Permissions
        $resources = [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'delegates' => 'المندوبين',
            'donations' => 'التبرعات',
            'projects' => 'المشاريع',
            'campaigns' => 'الحملات',
            'beneficiaries' => 'المستفيدين',
            'warehouses' => 'المخازن',
            'items' => 'الأصناف',
            'inventory_transactions' => 'حركات المخزون',
            'complaints' => 'الشكاوى',
            'expenses' => 'المصروفات',
            'payrolls' => 'الرواتب',
            'guest_houses' => 'دور الضيافة',
            'workspaces' => 'مساحات العمل',
            'tasks' => 'المهام',
            'financial_closures' => 'الإغلاقات المالية',
            'audits' => 'سجلات النظام'
        ];

        $actions = [
            'view' => 'عرض',
            'create' => 'إضافة',
            'edit' => 'تعديل',
            'delete' => 'حذف'
        ];

        $permissions = [];
        foreach ($resources as $resKey => $resName) {
            foreach ($actions as $actKey => $actName) {
                // Skip create/edit/delete for audits if preferred, but keeping standard for now
                if ($resKey === 'audits' && $actKey !== 'view') continue;

                $permissions[] = [
                    'key' => "{$resKey}.{$actKey}",
                    'name' => "{$actName} {$resName}"
                ];
            }
        }

        // Insert permissions
        foreach ($permissions as $perm) {
            if (!DB::table('permissions')->where('key', $perm['key'])->exists()) {
                DB::table('permissions')->insert([
                    'key' => $perm['key'],
                    'name' => $perm['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 3. Assign Permissions to Roles
        $rolePermissions = [
            'admin' => ['*'], // All
            'manager' => [
                'users.view', 'users.create', 'users.edit',
                'delegates.*', 
                'donations.*', 
                'projects.*', 
                'campaigns.*', 
                'beneficiaries.*', 
                'warehouses.*', 
                'items.*', 
                'inventory_transactions.*', 
                'complaints.*', 
                'expenses.*', 
                'guest_houses.*', 
                'workspaces.*', 
                'tasks.*',
                'audits.view'
            ],
            'finance' => [
                'donations.*', 
                'expenses.*', 
                'payrolls.*', 
                'financial_closures.*',
                'inventory_transactions.view'
            ], 
            'HR' => [
                'users.*', 
                'payrolls.*', 
                'tasks.*',
                'complaints.view', 'complaints.edit' // HR handles complaints often
            ], 
            'volunteer' => [
                'tasks.view', 'tasks.edit' // View and update status of their tasks
            ]
        ];

        // Fetch all permission IDs map
        $allPerms = DB::table('permissions')->pluck('id', 'key');

        foreach ($rolePermissions as $roleKey => $perms) {
            $roleId = DB::table('roles')->where('key', $roleKey)->value('id');
            if (!$roleId) continue;

            $idsToSync = [];
            if (in_array('*', $perms)) {
                $idsToSync = $allPerms->values()->toArray();
            } else {
                foreach ($perms as $pattern) {
                    if (str_ends_with($pattern, '.*')) {
                        $prefix = substr($pattern, 0, -2);
                        foreach ($allPerms as $k => $id) {
                            if (str_starts_with($k, $prefix . '.')) {
                                $idsToSync[] = $id;
                            }
                        }
                    } else {
                        if (isset($allPerms[$pattern])) {
                            $idsToSync[] = $allPerms[$pattern];
                        }
                    }
                }
            }
            
            // Sync (Delete existing then insert)
            DB::table('permission_role')->where('role_id', $roleId)->delete();
            $insertData = [];
            foreach (array_unique($idsToSync) as $pid) {
                $insertData[] = ['role_id' => $roleId, 'permission_id' => $pid];
            }
            if (!empty($insertData)) {
                DB::table('permission_role')->insert($insertData);
            }
        }
    }

    public function down(): void
    {
        // Optional: clear permissions
    }
};
