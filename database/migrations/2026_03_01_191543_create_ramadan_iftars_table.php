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
        Schema::create('ramadan_iftars', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->nullable();
            $table->string('beneficiary_name');
            $table->string('nickname')->nullable();
            $table->string('national_id')->nullable();
            $table->integer('meals_count')->default(1);
            $table->string('guide_name')->nullable();
            $table->string('guide_phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ramadan_iftars');
    }
};
