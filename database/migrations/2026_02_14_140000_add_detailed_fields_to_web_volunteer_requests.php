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
        Schema::table('web_volunteer_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('web_volunteer_requests', 'address')) {
                $table->string('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'current_address')) {
                $table->string('current_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('current_address');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'national_id')) {
                $table->string('national_id')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'gender')) {
                $table->string('gender')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'education_level')) {
                $table->string('education_level')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'faculty')) {
                $table->string('faculty')->nullable()->after('education_level');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'university')) {
                $table->string('university')->nullable()->after('faculty');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'current_job')) {
                $table->string('current_job')->nullable()->after('university');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'previous_experience')) {
                $table->string('previous_experience')->nullable()->after('current_job');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'skills')) {
                $table->text('skills')->nullable()->after('previous_experience');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'goal')) {
                $table->text('goal')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'expectations')) {
                $table->text('expectations')->nullable()->after('goal');
            }
            if (!Schema::hasColumn('web_volunteer_requests', 'volunteer_hours')) {
                $table->string('volunteer_hours')->nullable()->after('expectations');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_volunteer_requests', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'current_address', 'birth_date', 'national_id', 'gender',
                'education_level', 'faculty', 'university', 'current_job',
                'previous_experience', 'skills', 'goal', 'expectations', 'volunteer_hours'
            ]);
        });
    }
};
