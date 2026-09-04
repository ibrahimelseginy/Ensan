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
        // Pivot table for Ensan Pillars and Projects
        Schema::create('ensan_pillar_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensan_pillar_id')->constrained('ensan_pillars')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table for Ensan Pillars and Mobile Home Items (Services)
        Schema::create('ensan_pillar_service_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensan_pillar_id')->constrained('ensan_pillars')->onDelete('cascade');
            $table->foreignId('mobile_home_item_id')->constrained('mobile_home_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ensan_pillar_service_item');
        Schema::dropIfExists('ensan_pillar_project');
    }
};
