<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donors')) { return; }
        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors','sponsorship_type')) {
                $table->enum('sponsorship_type', ['none','monthly_sponsor','sadaqa_jariya'])->default('none')->after('recurring_cycle');
            }
            if (!Schema::hasColumn('donors','sponsored_beneficiary_id')) {
                $table->unsignedBigInteger('sponsored_beneficiary_id')->nullable()->after('sponsorship_type');
            }
            if (!Schema::hasColumn('donors','sponsorship_project_id')) {
                $table->unsignedBigInteger('sponsorship_project_id')->nullable()->after('sponsored_beneficiary_id');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('donors')) { return; }
        Schema::table('donors', function (Blueprint $table) {
            foreach (['sponsorship_type','sponsored_beneficiary_id','sponsorship_project_id'] as $col) {
                if (Schema::hasColumn('donors',$col)) { $table->dropColumn($col); }
            }
        });
    }
};

