<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('application_locks', function (Blueprint $table) {
            $table->string('name')->primary();
        });
        DB::table('application_locks')->insert(['name' => 'ledger']);
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->boolean('period_locked')->default(false);
        });
    }
    public function down(): void
    {
        // Preserve effective locks when rolling back to the old representation.
        DB::table('journal_entries')->where('period_locked', true)->update(['locked' => true]);
        Schema::table('journal_entries', fn (Blueprint $table) => $table->dropColumn('period_locked'));
        Schema::dropIfExists('application_locks');
    }
};
