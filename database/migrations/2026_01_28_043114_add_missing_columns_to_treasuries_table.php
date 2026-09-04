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
        Schema::table('treasuries', function (Blueprint $table) {
            if (!Schema::hasColumn('treasuries', 'type')) {
                $table->string('type')->default('main')->after('code');
            }
            if (!Schema::hasColumn('treasuries', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            }
            if (!Schema::hasColumn('treasuries', 'campaign_id')) {
                $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            }
            if (!Schema::hasColumn('treasuries', 'delegate_id')) {
                $table->foreignId('delegate_id')->nullable()->constrained('delegates')->onDelete('set null');
            }
            if (!Schema::hasColumn('treasuries', 'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treasuries', function (Blueprint $table) {
            $table->dropColumn(['type', 'project_id', 'campaign_id', 'delegate_id', 'branch_id']);
        });
    }
};
