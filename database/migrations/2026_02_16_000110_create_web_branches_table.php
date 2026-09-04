<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('web_branches')) {
            Schema::create('web_branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('working_hours')->nullable();
                $table->string('email')->nullable();
                $table->string('google_maps_url')->nullable();
                $table->boolean('is_main')->default(false);
                $table->string('status_text')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('web_branches');
    }
};

