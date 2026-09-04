<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('web_volunteer_requests', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('current_address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('national_id')->nullable();
            $table->string('gender')->nullable();
            $table->string('education_level')->nullable();
            $table->string('faculty')->nullable();
            $table->string('university')->nullable();
            $table->string('current_job')->nullable();
            $table->boolean('previous_experience')->default(false);
            $table->text('skills')->nullable();
            $table->text('goal')->nullable();
            $table->text('expectations')->nullable();
            $table->string('volunteer_hours')->nullable();
        });
    }

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
