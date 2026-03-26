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
        if (!Schema::hasTable('web_volunteers_wall')) {
            Schema::create('web_volunteers_wall', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('hours');
                $table->unsignedInteger('rank')->default(1);
                $table->string('image_path')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_volunteers_wall');
    }
};

