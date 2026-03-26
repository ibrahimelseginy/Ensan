<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Create Department Permissions
        $deptPermissions = [
            ['name' => 'إدارة المالية', 'key' => 'manage_finance'],
            // ['name' => 'إدارة الإعلام', 'key' => 'manage_media'],
            ['name' => 'إدارة اللوجستيك', 'key' => 'manage_logistics'],
            // ['name' => 'إدارة التقنية', 'key' => 'manage_it'],
        ];

        foreach ($deptPermissions as $perm) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $perm['name'],
                'key' => $perm['key'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Department Roles
        $deptRoles = [
            ['name' => 'مدير مالية', 'key' => 'finance_manager', 'perm' => 'manage_finance'],
            // ['name' => 'مدير إعلام', 'key' => 'media_manager', 'perm' => 'manage_media'],
            ['name' => 'مدير لوجستيك', 'key' => 'logistics_manager', 'perm' => 'manage_logistics'],
            // ['name' => 'مدير تقنية', 'key' => 'it_manager', 'perm' => 'manage_it'],
        ];

        foreach ($deptRoles as $role) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $role['name'],
                'key' => $role['key'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $permId = DB::table('permissions')->where('key', $role['perm'])->value('id');
            if ($permId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permId
                ]);
            }
        }

        // 3. Dynamic Roles from Entities
        
        // Projects
        $projects = DB::table('projects')->get();
        $projPermId = DB::table('permissions')->where('key', 'manage_project')->value('id');
        
        foreach($projects as $p) {
            $roleName = 'مدير ' . $p->name;
            $roleKey = 'project_manager_' . $p->id;
            
            // Check if exists
            if (DB::table('roles')->where('key', $roleKey)->exists()) continue;

            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'key' => $roleKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($projPermId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $projPermId
                ]);
            }
        }

        // Campaigns
        $campaigns = DB::table('campaigns')->get();
        $campPermId = DB::table('permissions')->where('key', 'manage_campaign')->value('id');

        foreach($campaigns as $c) {
            $roleName = 'مدير ' . $c->name;
            $roleKey = 'campaign_manager_' . $c->id;

            if (DB::table('roles')->where('key', $roleKey)->exists()) continue;

            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'key' => $roleKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($campPermId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $campPermId
                ]);
            }
        }

        // Guest Houses
        $guestHouses = DB::table('guest_houses')->get();
        $ghPermId = DB::table('permissions')->where('key', 'manage_guest_house')->value('id');

        foreach($guestHouses as $gh) {
            $roleName = 'مدير ' . $gh->name;
            $roleKey = 'guest_house_manager_' . $gh->id;

            if (DB::table('roles')->where('key', $roleKey)->exists()) continue;

            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'key' => $roleKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($ghPermId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $ghPermId
                ]);
            }
        }
    }

    public function down()
    {
        // Remove dynamic roles created (approximate cleanup)
        DB::table('roles')->where('key', 'like', 'project_manager_%')->delete();
        DB::table('roles')->where('key', 'like', 'campaign_manager_%')->delete();
        DB::table('roles')->where('key', 'like', 'guest_house_manager_%')->delete();
        
        $deptKeys = ['finance_manager', 'media_manager', 'logistics_manager', 'it_manager'];
        DB::table('roles')->whereIn('key', $deptKeys)->delete();
        
        $permKeys = ['manage_finance', 'manage_media', 'manage_logistics', 'manage_it'];
        DB::table('permissions')->whereIn('key', $permKeys)->delete();
    }
};
