<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_donors', function (Blueprint $table) {
            $table->string('city')->nullable()->after('email');
            $table->string('governorate')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('web_donors', function (Blueprint $table) {
            $table->dropColumn(['city', 'governorate']);
        });
    }
};
