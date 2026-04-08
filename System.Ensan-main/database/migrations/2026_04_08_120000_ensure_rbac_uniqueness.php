<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // --- Merge Role: مدير دار ضيافة طنطا (23 -> 16) ---
            $this->mergeRoles(23, 16);

            // --- Merge Role: مدير دار ضيافة كفر الشيخ (24 -> 17) ---
            $this->mergeRoles(24, 17);

            // --- Apply UNIQUE constraints ---
            Schema::table('roles', function (Blueprint $table) {
                // Ensure no duplicate names exist before applying
                // (Already handled by merge logic for known duplicates)
                $table->string('name')->unique()->change();
            });

            Schema::table('permissions', function (Blueprint $table) {
                $table->string('name')->unique()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }

    /**
     * Safely merge relationships from one role to another and delete the source.
     */
    private function mergeRoles(int $sourceId, int $targetId): void
    {
        if ($sourceId === $targetId) return;

        // 1. Move Users (ensure no duplicate role_user entries)
        $usersToMove = DB::table('role_user')->where('role_id', $sourceId)->get();
        foreach ($usersToMove as $userRole) {
            $exists = DB::table('role_user')
                ->where('role_id', $targetId)
                ->where('user_id', $userRole->user_id)
                ->exists();

            if (!$exists) {
                DB::table('role_user')->insert([
                    'role_id' => $targetId,
                    'user_id' => $userRole->user_id
                ]);
            }
        }
        DB::table('role_user')->where('role_id', $sourceId)->delete();

        // 2. Move Permissions
        $permsToMove = DB::table('permission_role')->where('role_id', $sourceId)->get();
        foreach ($permsToMove as $permRole) {
            $exists = DB::table('permission_role')
                ->where('role_id', $targetId)
                ->where('permission_id', $permRole->permission_id)
                ->exists();

            if (!$exists) {
                DB::table('permission_role')->insert([
                    'role_id' => $targetId,
                    'permission_id' => $permRole->permission_id
                ]);
            }
        }
        DB::table('permission_role')->where('role_id', $sourceId)->delete();

        // 3. Delete the duplicate role
        DB::table('roles')->where('id', $sourceId)->delete();
    }
};
