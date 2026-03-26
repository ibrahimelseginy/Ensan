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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('allocation_type')->nullable()->comment('نوع التخصيص لشخص واحد أو أكثر');
            $table->string('child_sponsorship_type')->nullable()->comment('التخصيص لكل طفل كافل أم أكثر');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['allocation_type', 'child_sponsorship_type']);
        });
    }
};
