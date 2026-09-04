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
        Schema::create('mobile_volunteer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('current_address')->nullable();
            $table->string('education_level')->nullable();
            $table->string('faculty')->nullable();
            $table->string('university')->nullable();
            $table->string('current_job')->nullable();
            $table->string('previous_experience')->nullable();
            $table->text('skills')->nullable();
            $table->text('goal')->nullable();
            $table->text('expectations')->nullable();
            $table->string('volunteer_hours')->nullable();
            $table->string('area_of_interest')->nullable();
            $table->text('message')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('id_card_path')->nullable();
            $table->enum('status', ['new', 'contacted', 'accepted', 'rejected'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_volunteer_requests');
    }
};
