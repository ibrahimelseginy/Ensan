<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'complainant_name')) {
                $table->string('complainant_name')->nullable()->after('source_id');
            }
            if (!Schema::hasColumn('complaints', 'complainant_phone')) {
                $table->string('complainant_phone', 50)->nullable()->after('complainant_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['complainant_name', 'complainant_phone']);
        });
    }
};
