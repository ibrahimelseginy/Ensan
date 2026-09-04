<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dashboardPermissionId = DB::table('permissions')
            ->where('key', 'dashboard.view')
            ->value('id');

        if (!$dashboardPermissionId) {
            return;
        }

        $administrativeRoleIds = DB::table('roles')
            ->whereNotIn('key', ['donor', 'web_donor', 'mobile_donor'])
            ->pluck('id');

        foreach ($administrativeRoleIds as $roleId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $dashboardPermissionId,
            ]);
        }

        $systemUserIds = DB::table('role_user')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->whereNotIn('roles.key', ['donor', 'web_donor', 'mobile_donor', 'volunteer'])
            ->distinct()
            ->pluck('role_user.user_id');

        if ($systemUserIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $systemUserIds)
                ->update(['is_employee' => true]);
        }
    }

    public function down(): void
    {
        // Additive data repair: existing role assignments are intentionally preserved.
    }
};
