<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Services\RoleService;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permission_user')) {
            Schema::create('permission_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'permission_id']);
            });
        }

        // Sync complete permission catalog
        app(RoleService::class)->syncEntityPermissions();

        // Fix specific project/guest-house manager roles on live DB so global umbrella permissions
        // do not leak all projects or all guest houses to scoped managers.
        $globalProjectUmbrellaId = Permission::where('key', 'manage_project')->value('id');
        $globalGuestHouseUmbrellaId = Permission::where('key', 'manage_guest_house')->value('id');

        // 1. project_manager_2 (مدير مشروع بعثاء الأمل -> projects 46, 51)
        $pm2 = Role::where('key', 'project_manager_2')->first();
        if ($pm2) {
            if ($globalProjectUmbrellaId) {
                $pm2->permissions()->detach($globalProjectUmbrellaId);
            }
            $baathaaPerms = Permission::whereIn('key', ['projects.manage.46', 'projects.manage.51', 'projects.view', 'projects.edit'])->pluck('id')->all();
            if (!empty($baathaaPerms)) {
                $pm2->permissions()->syncWithoutDetaching($baathaaPerms);
            }
        }

        // 2. project_manager_3 (مدير مشروع زاد -> project 49)
        $pm3 = Role::where('key', 'project_manager_3')->first();
        if ($pm3) {
            if ($globalProjectUmbrellaId) {
                $pm3->permissions()->detach($globalProjectUmbrellaId);
            }
            $zadPerms = Permission::whereIn('key', ['projects.manage.49', 'projects.view', 'projects.edit'])->pluck('id')->all();
            if (!empty($zadPerms)) {
                $pm3->permissions()->syncWithoutDetaching($zadPerms);
            }
        }

        // 3. guest_house_manager_1 (مدير دار ضيافة طنطا -> guest_house 1)
        $ghm1 = Role::where('key', 'guest_house_manager_1')->first();
        if ($ghm1) {
            if ($globalGuestHouseUmbrellaId) {
                $ghm1->permissions()->detach($globalGuestHouseUmbrellaId);
            }
            $gh2PermId = Permission::where('key', 'guest_houses.manage.2')->value('id');
            if ($gh2PermId) {
                $ghm1->permissions()->detach($gh2PermId);
            }
            $gh1Perms = Permission::whereIn('key', ['guest_houses.manage.1', 'guest_houses.view', 'guest_houses.edit'])->pluck('id')->all();
            if (!empty($gh1Perms)) {
                $ghm1->permissions()->syncWithoutDetaching($gh1Perms);
            }
        }

        // 4. guest_house_manager_2 (مدير دار ضيافة كفر الشيخ -> guest_house 2)
        $ghm2 = Role::where('key', 'guest_house_manager_2')->first();
        if ($ghm2) {
            if ($globalGuestHouseUmbrellaId) {
                $ghm2->permissions()->detach($globalGuestHouseUmbrellaId);
            }
            $gh1PermId = Permission::where('key', 'guest_houses.manage.1')->value('id');
            if ($gh1PermId) {
                $ghm2->permissions()->detach($gh1PermId);
            }
            $gh2Perms = Permission::whereIn('key', ['guest_houses.manage.2', 'guest_houses.view', 'guest_houses.edit'])->pluck('id')->all();
            if (!empty($gh2Perms)) {
                $ghm2->permissions()->syncWithoutDetaching($gh2Perms);
            }
        }

        // 5. Detach redundant generic 'project_manager' role from users who already have a specific 'project_manager_*' role
        $genericPmRole = Role::where('key', 'project_manager')->first();
        $specificPmRoleIds = Role::where('key', 'like', 'project_manager_%')->pluck('id')->all();
        if ($genericPmRole && !empty($specificPmRoleIds)) {
            $usersWithSpecificPm = DB::table('role_user')
                ->whereIn('role_id', $specificPmRoleIds)
                ->pluck('user_id')
                ->unique()
                ->all();
            if (!empty($usersWithSpecificPm)) {
                DB::table('role_user')
                    ->where('role_id', $genericPmRole->id)
                    ->whereIn('user_id', $usersWithSpecificPm)
                    ->delete();
            }
        }

        // 6. Detach redundant generic 'guest_house_manager' role from users who already have a specific 'guest_house_manager_*' role
        $genericGhmRole = Role::where('key', 'guest_house_manager')->first();
        $specificGhmRoleIds = Role::where('key', 'like', 'guest_house_manager_%')->pluck('id')->all();
        if ($genericGhmRole && !empty($specificGhmRoleIds)) {
            $usersWithSpecificGhm = DB::table('role_user')
                ->whereIn('role_id', $specificGhmRoleIds)
                ->pluck('user_id')
                ->unique()
                ->all();
            if (!empty($usersWithSpecificGhm)) {
                DB::table('role_user')
                    ->where('role_id', $genericGhmRole->id)
                    ->whereIn('user_id', $usersWithSpecificGhm)
                    ->delete();
            }
        }

        // 7. Ensure Admin & Manager have all permissions
        $allPermIds = Permission::pluck('id')->all();
        foreach (Role::whereIn('key', ['admin', 'manager'])->get() as $adminRole) {
            $adminRole->permissions()->sync($allPermIds);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_user');
    }
};

