<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('complaints')) {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('complaints')) {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('open', 'in_progress', 'closed') NOT NULL DEFAULT 'open'");
        }
    }
};
