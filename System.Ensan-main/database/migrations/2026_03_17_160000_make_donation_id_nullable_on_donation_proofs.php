<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('donation_proofs') || !Schema::hasColumn('donation_proofs', 'donation_id')) {
            return;
        }

        DB::statement('ALTER TABLE donation_proofs DROP FOREIGN KEY donation_proofs_donation_id_foreign');
        DB::statement('ALTER TABLE donation_proofs MODIFY donation_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE donation_proofs ADD CONSTRAINT donation_proofs_donation_id_foreign FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('donation_proofs') || !Schema::hasColumn('donation_proofs', 'donation_id')) {
            return;
        }

        DB::table('donation_proofs')
            ->whereNull('donation_id')
            ->whereNotNull('web_donation_id')
            ->delete();

        DB::statement('ALTER TABLE donation_proofs DROP FOREIGN KEY donation_proofs_donation_id_foreign');
        DB::statement('ALTER TABLE donation_proofs MODIFY donation_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE donation_proofs ADD CONSTRAINT donation_proofs_donation_id_foreign FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE');
    }
};
