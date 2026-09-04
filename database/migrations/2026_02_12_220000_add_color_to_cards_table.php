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
        Schema::table('web_dynamic_cards', function (Blueprint $table) {
            $table->string('card_color')->nullable()->after('description'); // e.g., #3b82f6 or blue
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_dynamic_cards', function (Blueprint $table) {
            $table->dropColumn('card_color');
        });
    }
};
