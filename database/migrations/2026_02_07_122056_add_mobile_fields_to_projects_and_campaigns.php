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
            $table->text('mobile_content')->nullable();
            $table->boolean('show_on_mobile')->default(true);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('mobile_content')->nullable();
            $table->boolean('show_on_mobile')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects_and_campaigns', function (Blueprint $table) {
            //
        });
    }
};
