<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Define roles
        $roles = [
            ['name' => 'متطوع', 'key' => 'volunteer'],
            ['name' => 'مدير مشروع', 'key' => 'project_manager'],
            ['name' => 'نائب مدير مشروع', 'key' => 'project_deputy'],
            ['name' => 'مدير حملة', 'key' => 'campaign_manager'],
            ['name' => 'مدير دار', 'key' => 'guest_house_manager'],
            ['name' => 'الموارد البشرية', 'key' => 'hr'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role['name'],
                'key' => $role['key'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Define permissions
        $permissions = [
            // Volunteer
            ['name' => 'عرض المهام الخاصة', 'key' => 'view_own_tasks'],
            ['name' => 'تسجيل ساعات تطوع', 'key' => 'log_volunteer_hours'],
            
            // Project Manager
            ['name' => 'إدارة المشروع', 'key' => 'manage_project'],
            ['name' => 'إدارة متطوعي المشروع', 'key' => 'manage_project_volunteers'],
            
            // Deputy Project Manager
            ['name' => 'مساعدة في إدارة المشروع', 'key' => 'assist_project_management'],
            
            // Campaign Manager
            ['name' => 'إدارة الحملة', 'key' => 'manage_campaign'],
            
            // Guest House Manager
            ['name' => 'إدارة الدار', 'key' => 'manage_guest_house'],
            
            // HR
            ['name' => 'إدارة الموظفين', 'key' => 'manage_employees'],
            ['name' => 'إدارة المتطوعين', 'key' => 'manage_volunteers_hr'],
            ['name' => 'إدارة الرواتب', 'key' => 'manage_payrolls'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $perm['name'],
                'key' => $perm['key'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign permissions to roles
        $rolePermissions = [
            'volunteer' => ['view_own_tasks', 'log_volunteer_hours'],
            'project_manager' => ['manage_project', 'manage_project_volunteers', 'view_own_tasks'],
            'project_deputy' => ['assist_project_management', 'view_own_tasks'],
            'campaign_manager' => ['manage_campaign', 'view_own_tasks'],
            'guest_house_manager' => ['manage_guest_house', 'view_own_tasks'],
            'hr' => ['manage_employees', 'manage_volunteers_hr', 'manage_payrolls', 'view_own_tasks'],
        ];

        foreach ($rolePermissions as $roleKey => $permKeys) {
            $roleId = DB::table('roles')->where('key', $roleKey)->value('id');
            if (!$roleId) continue;

            foreach ($permKeys as $permKey) {
                $permId = DB::table('permissions')->where('key', $permKey)->value('id');
                if ($permId) {
                    DB::table('permission_role')->insertOrIgnore([
                        'role_id' => $roleId,
                        'permission_id' => $permId
                    ]);
                }
            }
        }
    }

    public function down()
    {
        // We don't necessarily want to delete them in down() to avoid data loss if rolled back accidentally, 
        // but for strict migration consistency:
        
        $roleKeys = ['volunteer', 'project_manager', 'project_deputy', 'campaign_manager', 'guest_house_manager', 'hr'];
        $permKeys = [
            'view_own_tasks', 'log_volunteer_hours', 
            'manage_project', 'manage_project_volunteers', 
            'assist_project_management', 
            'manage_campaign', 
            'manage_guest_house', 
            'manage_employees', 'manage_volunteers_hr', 'manage_payrolls'
        ];

        $roleIds = DB::table('roles')->whereIn('key', $roleKeys)->pluck('id');
        DB::table('permission_role')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('key', $roleKeys)->delete();
        DB::table('permissions')->whereIn('key', $permKeys)->delete();
    }
};
