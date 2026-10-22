<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('field_visits', 'house_photo_path')) {
                $table->string('house_photo_path')->nullable()->after('home_ownership');
            }
            if (!Schema::hasColumn('field_visits', 'condition_photos')) {
                $table->json('condition_photos')->nullable()->after('home_and_social_condition');
            }
        });
    }

    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            if (Schema::hasColumn('field_visits', 'house_photo_path')) {
                $table->dropColumn('house_photo_path');
            }
            if (Schema::hasColumn('field_visits', 'condition_photos')) {
                $table->dropColumn('condition_photos');
            }
        });
    }
};
