<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donations')) { return; }
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations','cash_channel')) {
                $table->enum('cash_channel', ['cash','instapay','vodafone_cash'])->nullable()->after('type');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('donations')) { return; }
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations','cash_channel')) {
                $table->dropColumn('cash_channel');
            }
        });
    }
};

