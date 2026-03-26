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
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->string('source')->default('web')->after('status')->comment('web or mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
