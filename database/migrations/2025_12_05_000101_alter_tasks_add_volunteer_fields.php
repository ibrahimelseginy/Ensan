<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks','volunteer_activity_name')) {
                $table->string('volunteer_activity_name')->nullable()->after('title');
            }
            if (!Schema::hasColumn('tasks','project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('volunteer_activity_name');
                $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks','campaign_id')) {
                $table->unsignedBigInteger('campaign_id')->nullable()->after('project_id');
                $table->foreign('campaign_id')->references('id')->on('campaigns')->nullOnDelete();
            }
            if (Schema::hasTable('guest_houses') && !Schema::hasColumn('tasks','guest_house_id')) {
                $table->unsignedBigInteger('guest_house_id')->nullable()->after('campaign_id');
                $table->foreign('guest_house_id')->references('id')->on('guest_houses')->nullOnDelete();
            }
        });
    }
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks','guest_house_id')) { $table->dropForeign(['guest_house_id']); $table->dropColumn('guest_house_id'); }
            if (Schema::hasColumn('tasks','campaign_id')) { $table->dropForeign(['campaign_id']); $table->dropColumn('campaign_id'); }
            if (Schema::hasColumn('tasks','project_id')) { $table->dropForeign(['project_id']); $table->dropColumn('project_id'); }
            if (Schema::hasColumn('tasks','volunteer_activity_name')) { $table->dropColumn('volunteer_activity_name'); }
        });
    }
};

