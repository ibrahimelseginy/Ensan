<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'project_role')) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `project_role` TEXT NULL");
            }
            if (Schema::hasColumn('users', 'governorate')) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `governorate` VARCHAR(500) NULL");
            }
            if (Schema::hasColumn('users', 'city')) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `city` VARCHAR(500) NULL");
            }
            if (Schema::hasColumn('users', 'college')) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `college` VARCHAR(500) NULL");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'project_role')) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `project_role` VARCHAR(191) NULL");
            }
        }
    }
};

