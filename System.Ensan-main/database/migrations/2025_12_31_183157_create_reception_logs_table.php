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
        if (!Schema::hasTable('reception_logs')) {
            Schema::create('reception_logs', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('phone')->nullable();
            $table->enum('visit_type', ['personal', 'phone']);
            $table->enum('purpose', ['help_request', 'complaint', 'donation', 'inquiry', 'other']);
            $table->enum('status', ['pending', 'completed', 'directed'])->default('pending');
            $table->string('directed_to')->nullable(); // Employee name or Department
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_logs');
    }
};
