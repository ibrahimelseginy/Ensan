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
        if (!Schema::hasTable('web_partners')) {
            Schema::create('web_partners', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('logo_path')->nullable();
                $table->text('description')->nullable();
                $table->enum('type', ['partner', 'supporter'])->default('partner');
                $table->string('website_url')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_partners');
    }
};
