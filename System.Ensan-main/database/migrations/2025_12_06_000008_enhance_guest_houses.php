<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add manager fields to guest_houses
        Schema::table('guest_houses', function (Blueprint $table) {
            $table->foreignId('manager_user_id')->nullable()->after('description')->constrained('users')->nullOnDelete();
            $table->string('manager_photo_url')->nullable()->after('manager_user_id');
        });

        // 2. Create guest_house_volunteers table
        if (!Schema::hasTable('guest_house_volunteers')) {
            Schema::create('guest_house_volunteers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_id')->constrained('guest_houses')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role')->nullable();
                $table->date('started_at')->nullable();
                $table->decimal('hours', 8, 2)->default(0);
                $table->timestamps();
                $table->unique(['guest_house_id', 'user_id']);
            });
        }

        // 3. Create guest_house_monthly_volunteers table
        if (!Schema::hasTable('guest_house_monthly_volunteers')) {
            Schema::create('guest_house_monthly_volunteers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_id')->constrained('guest_houses')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('month');
                $table->unsignedSmallInteger('year');
                $table->string('notes')->nullable();
                $table->timestamps();
                $table->index(['guest_house_id', 'year', 'month']);
            });
        }

        // 4. Add guest_house_id to donations
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('guest_house_id')->nullable()->after('campaign_id')->constrained('guest_houses')->nullOnDelete();
        });

        // 5. Add guest_house_id to expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('guest_house_id')->nullable()->after('campaign_id')->constrained('guest_houses')->nullOnDelete();
        });

        // 6. Add guest_house_id to beneficiaries
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->foreignId('guest_house_id')->nullable()->after('campaign_id')->constrained('guest_houses')->nullOnDelete();
        });
    }

    public function down()
    {
        // Reversing changes
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['guest_house_id']);
            $table->dropColumn('guest_house_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['guest_house_id']);
            $table->dropColumn('guest_house_id');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['guest_house_id']);
            $table->dropColumn('guest_house_id');
        });

        Schema::dropIfExists('guest_house_monthly_volunteers');
        Schema::dropIfExists('guest_house_volunteers');

        Schema::table('guest_houses', function (Blueprint $table) {
            $table->dropForeign(['manager_user_id']);
            $table->dropColumn(['manager_user_id', 'manager_photo_url']);
        });
    }
};
