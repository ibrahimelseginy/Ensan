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
        Schema::table('ramadan_iftars', function (Blueprint $table) {
            $table->string('guide_phone_2')->nullable()->after('guide_phone');
            $table->string('delivery_method')->nullable()->after('guide_phone_2');
            $table->decimal('delivery_cost', 10, 2)->nullable()->after('delivery_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ramadan_iftars', function (Blueprint $table) {
            $table->dropColumn(['guide_phone_2', 'delivery_method', 'delivery_cost']);
        });
    }
};
