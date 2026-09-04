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
        if (!Schema::hasTable('web_volunteer_requests')) {
            Schema::create('web_volunteer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('area_of_interest')->nullable();
            $table->string('cv_path')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'accepted', 'rejected'])->default('new');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_volunteer_requests');
    }
};
