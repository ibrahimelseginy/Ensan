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
        if (Schema::hasTable('web_testimonials')) {
            Schema::table('web_testimonials', function (Blueprint $table) {
                if (!Schema::hasColumn('web_testimonials', 'status')) {
                    $table->string('status')->default('pending')->after('rating');
                }
            // pending, approved, rejected
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_testimonials', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
