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
            $table->string('contact_name')->nullable()->after('category');
            $table->string('contact_number')->nullable()->after('contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_news', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_number']);
        });
    }
};
