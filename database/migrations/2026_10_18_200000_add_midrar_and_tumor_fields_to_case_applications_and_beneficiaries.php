<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mobile_case_applications')) {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_case_applications', 'need_type')) {
                    $table->string('need_type', 100)->nullable()->after('case_type');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'family_members_count')) {
                    $table->string('family_members_count', 50)->nullable()->after('children_count');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'social_status_description')) {
                    $table->text('social_status_description')->nullable()->after('children_details');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'guide_name')) {
                    $table->string('guide_name', 255)->nullable()->after('submitter_relation');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'guide_phone')) {
                    $table->string('guide_phone', 50)->nullable()->after('guide_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'midrar_details')) {
                    $table->json('midrar_details')->nullable()->after('zad_details');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'hospital_name')) {
                    $table->string('hospital_name', 255)->nullable()->after('address');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'tumor_location')) {
                    $table->string('tumor_location', 255)->nullable()->after('hospital_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'treatment_protocol')) {
                    $table->text('treatment_protocol')->nullable()->after('tumor_location');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'tumor_details')) {
                    $table->json('tumor_details')->nullable()->after('midrar_details');
                }
            });
        }

        if (Schema::hasTable('beneficiaries')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                if (!Schema::hasColumn('beneficiaries', 'need_type')) {
                    $table->string('need_type', 100)->nullable()->after('assistance_type');
                }
                if (!Schema::hasColumn('beneficiaries', 'family_members_count')) {
                    $table->string('family_members_count', 50)->nullable()->after('children_count');
                }
                if (!Schema::hasColumn('beneficiaries', 'social_status_description')) {
                    $table->text('social_status_description')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('beneficiaries', 'guide_name')) {
                    $table->string('guide_name', 255)->nullable()->after('guardian_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'guide_phone')) {
                    $table->string('guide_phone', 50)->nullable()->after('guide_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'midrar_details')) {
                    $table->json('midrar_details')->nullable()->after('zad_details');
                }
                if (!Schema::hasColumn('beneficiaries', 'hospital_name')) {
                    $table->string('hospital_name', 255)->nullable()->after('address');
                }
                if (!Schema::hasColumn('beneficiaries', 'tumor_location')) {
                    $table->string('tumor_location', 255)->nullable()->after('hospital_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'treatment_protocol')) {
                    $table->text('treatment_protocol')->nullable()->after('tumor_location');
                }
                if (!Schema::hasColumn('beneficiaries', 'tumor_details')) {
                    $table->json('tumor_details')->nullable()->after('midrar_details');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mobile_case_applications')) {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                $columns = [
                    'need_type', 'family_members_count', 'social_status_description',
                    'guide_name', 'guide_phone', 'midrar_details',
                    'hospital_name', 'tumor_location', 'treatment_protocol', 'tumor_details'
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('mobile_case_applications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('beneficiaries')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $columns = [
                    'need_type', 'family_members_count', 'social_status_description',
                    'guide_name', 'guide_phone', 'midrar_details',
                    'hospital_name', 'tumor_location', 'treatment_protocol', 'tumor_details'
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('beneficiaries', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
