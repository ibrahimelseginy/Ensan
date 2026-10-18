<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_case_applications', function (Blueprint $table) {
            $table->string('submitter_type', 20)->default('self')->after('applicant_name'); // 'self', 'other'
            $table->string('submitter_name')->nullable()->after('submitter_type');
            $table->string('submitter_phone', 25)->nullable()->after('submitter_name');
            $table->string('submitter_relation', 100)->nullable()->after('submitter_phone');
            $table->string('backup_phone', 25)->nullable()->after('applicant_phone');
            $table->text('rejection_reason')->nullable()->after('admin_notes');
            $table->foreignId('assigned_researcher_id')->nullable()->after('rejection_reason')->constrained('users')->nullOnDelete();
            $table->foreignId('field_visit_id')->nullable()->after('assigned_researcher_id')->constrained('field_visits')->nullOnDelete();
            $table->string('source', 30)->default('mobile')->after('field_visit_id'); // 'system', 'website', 'mobile'
        });
    }

    public function down(): void
    {
        Schema::table('mobile_case_applications', function (Blueprint $table) {
            $table->dropForeign(['assigned_researcher_id']);
            $table->dropForeign(['field_visit_id']);
            $table->dropColumn([
                'submitter_type',
                'submitter_name',
                'submitter_phone',
                'submitter_relation',
                'backup_phone',
                'rejection_reason',
                'assigned_researcher_id',
                'field_visit_id',
                'source',
            ]);
        });
    }
};
