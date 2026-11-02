<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('complaints')) {
            // Modify source_type to VARCHAR(50) so it can accept 'website' and any future source types
            DB::statement("ALTER TABLE `complaints` MODIFY COLUMN `source_type` VARCHAR(50) NOT NULL DEFAULT 'website'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('complaints')) {
            DB::statement("ALTER TABLE `complaints` MODIFY COLUMN `source_type` ENUM('donor','beneficiary','employee') NOT NULL");
        }
    }
};
