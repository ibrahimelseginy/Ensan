<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('manager_photo_url')->nullable();
        });

        if (!Schema::hasTable('campaign_volunteers')) {
            Schema::create('campaign_volunteers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role')->nullable();
                $table->date('started_at')->nullable();
                $table->decimal('hours', 8, 2)->default(0);
                $table->timestamps();
                $table->unique(['campaign_id', 'user_id']);
            });

            Schema::create('campaign_monthly_volunteers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('month');
                $table->integer('year');
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        // Enhance Project Table if missing manager photo
        if (!Schema::hasColumn('projects', 'manager_photo_url')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('manager_photo_url')->nullable()->after('manager_user_id');
                $table->string('deputy_photo_url')->nullable()->after('deputy_user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('campaign_monthly_volunteers');
        Schema::dropIfExists('campaign_volunteers');
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['manager_user_id']);
            $table->dropColumn(['manager_user_id', 'manager_photo_url']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['manager_photo_url', 'deputy_photo_url']);
        });
    }
};
