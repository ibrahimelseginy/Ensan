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
        Schema::create('ramadan_bags', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->nullable();
            $table->string('beneficiary_name');
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('bag_contents')->nullable();
            $table->string('status')->default('جديد');
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
        Schema::dropIfExists('ramadan_bags');
    }
};
