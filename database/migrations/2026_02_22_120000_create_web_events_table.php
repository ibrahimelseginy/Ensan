<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        if (!Schema::hasTable('web_events')) {
            Schema::create('web_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content')->nullable();
                $table->string('image_path')->nullable();
                $table->string('location')->nullable();
                $table->string('category')->nullable(); // e.g. 'cultural', 'charity', 'awareness'
                $table->timestamp('event_date')->nullable();
                $table->timestamp('event_end_date')->nullable();
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('shares_count')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('web_events');
    }
};
