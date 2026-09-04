<?php

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SidebarPermissionsSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hasNameUniqueIndex = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', 'permissions')
            ->where('index_name', 'permissions_name_unique')
            ->exists();

        if ($hasNameUniqueIndex) {
            DB::statement('ALTER TABLE permissions DROP INDEX permissions_name_unique');
        }

        // Remove relationships left behind by imports performed with FK checks disabled.
        DB::table('role_user')
            ->whereNotIn('role_id', DB::table('roles')->select('id'))
            ->orWhereNotIn('user_id', DB::table('users')->select('id'))
            ->delete();

        DB::table('permission_role')
            ->whereNotIn('role_id', DB::table('roles')->select('id'))
            ->orWhereNotIn('permission_id', DB::table('permissions')->select('id'))
            ->delete();

        // Rebuild the canonical catalog and synchronize built-in roles.
        (new PermissionSeeder())->run();
        (new SidebarPermissionsSeeder())->run();
        (new RolePermissionSeeder())->run();
    }

    public function down(): void
    {
        // This is a data repair migration; repaired relationships are preserved.
    }
};
