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
        Schema::dropIfExists('web_pages');

        Schema::create('web_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g., 'about-us', 'privacy-policy'
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable(); // Hero image

            // SEO Meta Tags
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_pages');
    }
};
