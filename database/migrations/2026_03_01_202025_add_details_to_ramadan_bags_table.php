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
        Schema::table('ramadan_bags', function (Blueprint $table) {
            $table->dropColumn('case_number');
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->integer('family_members')->nullable();
            $table->text('case_conditions')->nullable();
            $table->string('region')->nullable();
            $table->integer('bags_count')->default(1);
            $table->string('phone_2')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ramadan_bags', function (Blueprint $table) {
            $table->string('case_number')->nullable();
            $table->dropColumn([
                'marital_status',
                'spouse_name',
                'family_members',
                'case_conditions',
                'region',
                'bags_count',
                'phone_2',
                'notes'
            ]);
        });
    }
};
