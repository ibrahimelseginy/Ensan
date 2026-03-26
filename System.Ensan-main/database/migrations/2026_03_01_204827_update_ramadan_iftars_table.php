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
            $table->dropColumn('case_number');
            $table->string('region')->nullable()->after('beneficiary_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ramadan_iftars', function (Blueprint $table) {
            $table->string('case_number')->nullable();
            $table->dropColumn('region');
        });
    }
};
