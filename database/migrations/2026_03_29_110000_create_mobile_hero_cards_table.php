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
        // First, drop the old fields from the previous task
        Schema::table('mobile_home_items', function (Blueprint $table) {
            $table->dropColumn(['hero_card_image', 'hero_card_title', 'hero_card_description']);
        });

        // Second, create the new table for multiple cards
        Schema::create('mobile_hero_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_home_item_id')->constrained('mobile_home_items')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_hero_cards');

        Schema::table('mobile_home_items', function (Blueprint $table) {
            $table->string('hero_card_image')->nullable()->after('icon');
            $table->string('hero_card_title')->nullable()->after('hero_card_image');
            $table->text('hero_card_description')->nullable()->after('hero_card_title');
        });
    }
};
