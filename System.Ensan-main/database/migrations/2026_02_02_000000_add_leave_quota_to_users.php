<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'annual_leave_quota')) {
                $table->integer('annual_leave_quota')->default(21)->after('job_title');
            }
            if (!Schema::hasColumn('users', 'leave_balance')) {
                $table->integer('leave_balance')->default(21)->after('annual_leave_quota');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['annual_leave_quota', 'leave_balance']);
        });
    }
};
