<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('project_volunteers','hours')) {
                $table->double('hours')->nullable()->default(0)->after('campaign_id');
            }
        });
    }
    public function down(): void
    {
        Schema::table('project_volunteers', function (Blueprint $table) {
            if (Schema::hasColumn('project_volunteers','hours')) {
                $table->dropColumn('hours');
            }
        });
    }
};
