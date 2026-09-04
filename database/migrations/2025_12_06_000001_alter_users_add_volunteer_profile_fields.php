<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('college')->nullable()->after('is_volunteer');
            $table->string('governorate')->nullable()->after('college');
            $table->string('city')->nullable()->after('governorate');
            $table->string('project_role')->nullable()->after('city');
            $table->decimal('volunteer_hours', 8, 2)->default(0)->after('project_role');
            
            // Affiliations
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete()->after('volunteer_hours');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete()->after('project_id');
            $table->foreignId('guest_house_id')->nullable()->constrained('guest_houses')->nullOnDelete()->after('campaign_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['guest_house_id']);
            $table->dropColumn(['college', 'governorate', 'city', 'project_role', 'volunteer_hours', 'project_id', 'campaign_id', 'guest_house_id']);
        });
    }
};
