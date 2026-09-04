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
        Schema::create('mobile_home_items', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // hero, gallery, service, share, campaign, final
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('icon')->nullable(); // For Hero
            $table->decimal('price', 15, 2)->nullable(); // For Service
            $table->decimal('share_price', 15, 2)->nullable(); // For Service
            $table->text('details')->nullable(); // For Seasonal Campaigns
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_home_items');
    }
};
