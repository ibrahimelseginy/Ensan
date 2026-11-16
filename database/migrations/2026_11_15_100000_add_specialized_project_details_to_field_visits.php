<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('field_visits')) {
            Schema::table('field_visits', function (Blueprint $table) {
                if (!Schema::hasColumn('field_visits', 'medrar_details')) {
                    $table->json('medrar_details')->nullable()->after('treatment_protocol');
                }
                if (!Schema::hasColumn('field_visits', 'zad_details')) {
                    $table->json('zad_details')->nullable()->after('medrar_details');
                }
                if (!Schema::hasColumn('field_visits', 'ramadan_details')) {
                    $table->json('ramadan_details')->nullable()->after('zad_details');
                }
            });
        }

        if (Schema::hasTable('mobile_case_applications')) {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_case_applications', 'medrar_details')) {
                    $table->json('medrar_details')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'zad_details')) {
                    $table->json('zad_details')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'ramadan_details')) {
                    $table->json('ramadan_details')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('field_visits')) {
            Schema::table('field_visits', function (Blueprint $table) {
                if (Schema::hasColumn('field_visits', 'medrar_details')) {
                    $table->dropColumn('medrar_details');
                }
                if (Schema::hasColumn('field_visits', 'zad_details')) {
                    $table->dropColumn('zad_details');
                }
                if (Schema::hasColumn('field_visits', 'ramadan_details')) {
                    $table->dropColumn('ramadan_details');
                }
            });
        }

        if (Schema::hasTable('mobile_case_applications')) {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                if (Schema::hasColumn('mobile_case_applications', 'medrar_details')) {
                    $table->dropColumn('medrar_details');
                }
                if (Schema::hasColumn('mobile_case_applications', 'zad_details')) {
                    $table->dropColumn('zad_details');
                }
                if (Schema::hasColumn('mobile_case_applications', 'ramadan_details')) {
                    $table->dropColumn('ramadan_details');
                }
            });
        }
    }
};

