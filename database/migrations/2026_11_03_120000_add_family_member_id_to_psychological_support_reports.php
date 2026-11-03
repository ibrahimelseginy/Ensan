<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('psychological_support_reports')) {
            Schema::table('psychological_support_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('psychological_support_reports', 'family_member_id')) {
                    $table->foreignId('family_member_id')
                        ->nullable()
                        ->after('beneficiary_id')
                        ->constrained('beneficiary_family_members')
                        ->nullOnDelete();

                    $table->index(['family_member_id', 'report_month'], 'psych_report_member_month_idx');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('psychological_support_reports')) {
            Schema::table('psychological_support_reports', function (Blueprint $table) {
                if (Schema::hasColumn('psychological_support_reports', 'family_member_id')) {
                    $table->dropForeign(['family_member_id']);
                    $table->dropColumn('family_member_id');
                }
            });
        }
    }
};
