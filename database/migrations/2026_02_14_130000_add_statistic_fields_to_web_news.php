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
        Schema::table('web_news', function (Blueprint $table) {
            $table->string('statistic_number')->nullable()->after('category');
            $table->string('statistic_description')->nullable()->after('statistic_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_news', function (Blueprint $table) {
            $table->dropColumn(['statistic_number', 'statistic_description']);
        });
    }
};
