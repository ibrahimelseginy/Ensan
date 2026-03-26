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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'manager_user_id')) {
                $table->unsignedBigInteger('manager_user_id')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'deputy_user_id')) {
                $table->unsignedBigInteger('deputy_user_id')->nullable();
            }

            if (!Schema::hasColumn('campaigns', 'manager_photo_url')) {
                $table->string('manager_photo_url')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'deputy_photo_url')) {
                $table->string('deputy_photo_url')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            if (!Schema::hasColumn('campaigns', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'website_content')) {
                $table->text('website_content')->nullable();
            }

            if (!Schema::hasColumn('campaigns', 'target_amount')) {
                $table->decimal('target_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'goal_amount')) {
                $table->decimal('goal_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'current_amount')) {
                $table->decimal('current_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'beneficiaries_count')) {
                $table->integer('beneficiaries_count')->nullable();
            }

        // Foreign key constraints can be added here if needed, but keeping it simple to avoid issues if reference tables are empty or mismatched
        // $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        // $table->foreign('manager_user_id')->references('id')->on('users')->onDelete('set null');
        // $table->foreign('deputy_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'project_id',
                'manager_user_id',
                'deputy_user_id',
                'manager_photo_url',
                'deputy_photo_url',
                'image_path',
                'category',
                'website_content',
                'target_amount',
                'goal_amount',
                'current_amount',
                'beneficiaries_count'
            ]);
        });
    }
};
