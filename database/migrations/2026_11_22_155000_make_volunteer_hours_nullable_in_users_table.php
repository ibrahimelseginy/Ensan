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
        if (Schema::hasColumn('users', 'volunteer_hours')) {
            try {
                DB::statement('ALTER TABLE users MODIFY COLUMN volunteer_hours DECIMAL(10, 2) DEFAULT 0 NULL');
            } catch (\Throwable $e) {
                Schema::table('users', function (Blueprint $table) {
                    $table->decimal('volunteer_hours', 10, 2)->default(0)->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op
    }
};

