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
            if (!Schema::hasColumn('projects', 'image_path'))
                $table->string('image_path')->nullable();
            if (!Schema::hasColumn('projects', 'category'))
                $table->string('category')->nullable();
            if (!Schema::hasColumn('projects', 'website_content'))
                $table->text('website_content')->nullable();
            if (!Schema::hasColumn('projects', 'sponsorship_details'))
                $table->text('sponsorship_details')->nullable();
        });

        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'image_path'))
                $table->string('image_path')->nullable();
            if (!Schema::hasColumn('campaigns', 'category'))
                $table->string('category')->nullable();
            if (!Schema::hasColumn('campaigns', 'website_content'))
                $table->text('website_content')->nullable();
            if (!Schema::hasColumn('campaigns', 'goal_amount'))
                $table->decimal('goal_amount', 15, 2)->default(0);
            if (!Schema::hasColumn('campaigns', 'current_amount'))
                $table->decimal('current_amount', 15, 2)->default(0);
            if (!Schema::hasColumn('campaigns', 'beneficiaries_count'))
                $table->integer('beneficiaries_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'category', 'website_content', 'sponsorship_details']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'category', 'website_content', 'goal_amount', 'current_amount', 'beneficiaries_count']);
        });
    }
};
