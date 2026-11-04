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
        if (Schema::hasTable('web_volunteer_requests')) {
            // 1. Modify status column from ENUM to VARCHAR(50) to support 'interview', 'accepted', 'rejected', 'new', 'contacted'
            try {
                DB::statement("ALTER TABLE web_volunteer_requests MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'new'");
            } catch (\Throwable $e) {
                // Ignore if DB driver doesn't support raw ALTER (e.g. SQLite in some unit tests)
            }

            // 2. Add decision & tracking columns if they don't already exist
            Schema::table('web_volunteer_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('web_volunteer_requests', 'tracking_code')) {
                    $table->string('tracking_code', 50)->nullable()->after('id')->index();
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'assigned_project_id')) {
                    $table->unsignedBigInteger('assigned_project_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'assigned_project_name')) {
                    $table->string('assigned_project_name', 191)->nullable()->after('assigned_project_id');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'interview_date')) {
                    $table->dateTime('interview_date')->nullable()->after('assigned_project_name');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'interview_location')) {
                    $table->string('interview_location', 255)->nullable()->after('interview_date');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'response_notes')) {
                    $table->text('response_notes')->nullable()->after('interview_location');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'reviewed_at')) {
                    $table->timestamp('reviewed_at')->nullable()->after('response_notes');
                }
                if (!Schema::hasColumn('web_volunteer_requests', 'reviewed_by')) {
                    $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
                }
            });

            // 3. Populate tracking_code for existing requests
            $existing = DB::table('web_volunteer_requests')->whereNull('tracking_code')->get(['id']);
            foreach ($existing as $req) {
                $code = 'VOL-' . date('Y') . '-' . str_pad((string)$req->id, 5, '0', STR_PAD_LEFT);
                DB::table('web_volunteer_requests')->where('id', $req->id)->update(['tracking_code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('web_volunteer_requests')) {
            Schema::table('web_volunteer_requests', function (Blueprint $table) {
                $columns = [
                    'tracking_code',
                    'assigned_project_id',
                    'assigned_project_name',
                    'interview_date',
                    'interview_location',
                    'response_notes',
                    'reviewed_at',
                    'reviewed_by',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('web_volunteer_requests', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
