<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convert restrictive ENUM columns on expenses, donations, and treasury_transactions to VARCHAR
        //    to prevent SQLSTATE[01000]: Warning: 1265 Data truncated in MySQL strict mode.
        if (Schema::hasTable('expenses')) {
            if (Schema::hasColumn('expenses', 'status')) {
                DB::statement("ALTER TABLE `expenses` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
            }
            if (Schema::hasColumn('expenses', 'type')) {
                DB::statement("ALTER TABLE `expenses` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'operational'");
            }
        }

        if (Schema::hasTable('donations')) {
            if (Schema::hasColumn('donations', 'status')) {
                DB::statement("ALTER TABLE `donations` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
            }
            if (Schema::hasColumn('donations', 'cash_channel')) {
                DB::statement("ALTER TABLE `donations` MODIFY COLUMN `cash_channel` VARCHAR(50) NULL DEFAULT 'cash'");
            }
            if (Schema::hasColumn('donations', 'source')) {
                DB::statement("ALTER TABLE `donations` MODIFY COLUMN `source` VARCHAR(50) NULL DEFAULT 'dashboard'");
            }
            if (Schema::hasColumn('donations', 'type')) {
                DB::statement("ALTER TABLE `donations` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'cash'");
            }
        }

        if (Schema::hasTable('treasury_transactions')) {
            if (Schema::hasColumn('treasury_transactions', 'status')) {
                DB::statement("ALTER TABLE `treasury_transactions` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
            }
        }

        // 2. Expand web_volunteer_requests text columns to prevent 1406 Data too long
        if (Schema::hasTable('web_volunteer_requests')) {
            foreach (['area_of_interest', 'volunteer_hours', 'previous_experience', 'current_job'] as $col) {
                if (Schema::hasColumn('web_volunteer_requests', $col)) {
                    DB::statement("ALTER TABLE `web_volunteer_requests` MODIFY COLUMN `{$col}` TEXT NULL");
                }
            }
        }

        // 3. Ensure projects table has a nullable code column if searched by GlobalSearchController
        if (Schema::hasTable('projects') && !Schema::hasColumn('projects', 'code')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('code', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Non-destructive column type widening
    }
};
