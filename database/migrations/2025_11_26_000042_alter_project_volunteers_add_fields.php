<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('project_volunteers','role')) {
                $table->string('role')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('project_volunteers','started_at')) {
                $table->date('started_at')->nullable()->after('role');
            }
            if (!Schema::hasColumn('project_volunteers','campaign_id')) {
                $table->unsignedBigInteger('campaign_id')->nullable()->after('started_at');
            }
        });
    }
    public function down(): void
    {
        Schema::table('project_volunteers', function (Blueprint $table) {
            if (Schema::hasColumn('project_volunteers','campaign_id')) { $table->dropColumn('campaign_id'); }
            if (Schema::hasColumn('project_volunteers','started_at')) { $table->dropColumn('started_at'); }
            if (Schema::hasColumn('project_volunteers','role')) { $table->dropColumn('role'); }
        });
    }
};

