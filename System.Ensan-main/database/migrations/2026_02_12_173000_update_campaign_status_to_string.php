<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('campaigns', 'status')) {
            // Drop constraint if it exists (for enum), but this is tricky across DBs.
            // Best bet for MySQL is modifying column directly.
            // Using string type for flexibility.
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('status')->default('active')->change();
            });
        }
    }

    public function down(): void
    {
    // Revert is risky if data doesn't fit enum.
    }
};
