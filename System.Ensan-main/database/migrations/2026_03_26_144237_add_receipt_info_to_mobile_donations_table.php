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
        Schema::table('mobile_donations', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('notes');
            $table->string('account_name')->nullable()->after('account_number');
            $table->string('receipt_path')->nullable()->after('account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_donations', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'account_name', 'receipt_path']);
        });
    }
};
