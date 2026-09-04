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
        Schema::create('web_dynamic_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Badge
            $table->boolean('badge_visible')->default(false);
            $table->string('badge_text')->nullable();
            $table->string('badge_icon')->nullable();

            // Tag
            $table->string('tag_text')->nullable();

            // Stats Grid
            $table->json('stats_data')->nullable(); // Array of {value, label}

            // Button Stack
            $table->json('buttons_data')->nullable(); // Array of {text, icon, action, style}

            // Main Bottom Button
            $table->string('main_button_text')->nullable();
            $table->string('main_button_action')->nullable();
            $table->string('main_button_icon')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_dynamic_cards');
    }
};
