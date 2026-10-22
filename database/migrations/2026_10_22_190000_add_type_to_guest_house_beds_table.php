<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_house_beds', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_house_beds', 'type')) {
                $table->string('type')->default('standard')->after('number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_house_beds', function (Blueprint $table) {
            if (Schema::hasColumn('guest_house_beds', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
