<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donations')) { return; }
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations','receipt_number')) {
                $table->string('receipt_number', 64)->nullable()->after('currency');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('donations')) { return; }
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations','receipt_number')) {
                $table->dropColumn('receipt_number');
            }
        });
    }
};

