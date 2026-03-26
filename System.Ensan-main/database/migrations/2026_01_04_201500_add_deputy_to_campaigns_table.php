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
            if (!Schema::hasColumn('campaigns', 'deputy_user_id')) {
                $table->unsignedBigInteger('deputy_user_id')->nullable()->after('manager_user_id');
                $table->foreign('deputy_user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('campaigns', 'deputy_photo_url')) {
                $table->string('deputy_photo_url')->nullable()->after('deputy_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['deputy_user_id']);
            $table->dropColumn(['deputy_user_id', 'deputy_photo_url']);
        });
    }
};
