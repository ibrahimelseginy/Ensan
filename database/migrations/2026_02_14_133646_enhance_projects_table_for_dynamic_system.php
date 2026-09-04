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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('icon_path')->nullable();
            $table->text('short_description')->nullable();
            $table->json('features')->nullable();
            $table->json('stats')->nullable();
            $table->json('theme_colors')->nullable();
            $table->string('action_text')->nullable();
            $table->string('action_url')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('show_badge')->default(true);
            $table->string('badge_text')->nullable();
            $table->string('badge_icon')->nullable();
            $table->string('subcategory_text')->nullable();
            $table->boolean('show_subcategory')->default(true);
            $table->string('action_icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'icon_path', 'short_description', 'features', 'stats', 'theme_colors',
                'action_text', 'action_url', 'is_visible', 'show_badge', 'badge_text',
                'badge_icon', 'subcategory_text', 'show_subcategory'
            ]);
        });
    }
};
