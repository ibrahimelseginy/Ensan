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
        if (!Schema::hasTable('mobile_case_applications')) {
            Schema::create('mobile_case_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('applicant_phone');
            $table->string('applicant_id_number')->nullable();
            $table->string('case_type'); // e.g., 'medical', 'financial', 'education'
            $table->text('description');
            $table->string('governorate')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('medical_report_path')->nullable(); // multiple files maybe?
            $table->string('status')->default('pending'); // pending, reviewing, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // If registered user
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_case_applications');
    }
};
