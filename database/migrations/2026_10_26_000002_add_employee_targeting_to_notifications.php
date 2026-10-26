<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('mobile_notifications', 'target_type')) {
                $table->string('target_type', 30)->default('all')->after('target_audience');
            }
            if (!Schema::hasColumn('mobile_notifications', 'target_job_title')) {
                $table->string('target_job_title')->nullable()->after('target_type');
            }
            if (!Schema::hasColumn('mobile_notifications', 'target_department')) {
                $table->string('target_department')->nullable()->after('target_job_title');
            }
            if (!Schema::hasColumn('mobile_notifications', 'target_role')) {
                $table->string('target_role')->nullable()->after('target_department');
            }
            if (!Schema::hasColumn('mobile_notifications', 'target_user_id')) {
                $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete()->after('target_role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mobile_notifications', function (Blueprint $table) {
            $table->dropForeign(['target_user_id']);
            $table->dropColumn([
                'target_type',
                'target_job_title',
                'target_department',
                'target_role',
                'target_user_id',
            ]);
        });
    }
};
