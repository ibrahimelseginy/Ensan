<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'category')) {
                $table->string('category')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('donations', 'target_id')) {
                $table->unsignedBigInteger('target_id')->nullable()->after('category');
            }
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['category', 'target_id']);
        });
    }
};
