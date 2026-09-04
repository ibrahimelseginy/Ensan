<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donors')) { return; }
        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors','sponsorship_monthly_amount')) {
                $table->decimal('sponsorship_monthly_amount', 12, 2)->nullable()->after('sponsorship_project_id');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('donors')) { return; }
        Schema::table('donors', function (Blueprint $table) {
            if (Schema::hasColumn('donors','sponsorship_monthly_amount')) {
                $table->dropColumn('sponsorship_monthly_amount');
            }
        });
    }
};

