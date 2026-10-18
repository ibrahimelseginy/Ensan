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
                if (!Schema::hasColumn('mobile_case_applications', 'source_name')) {
                    $table->string('source_name')->nullable()->after('submitter_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'reference_number')) {
                    $table->string('reference_number')->nullable()->after('source_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'mother_name')) {
                    $table->string('mother_name')->nullable()->after('applicant_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'deceased_husband_name')) {
                    $table->string('deceased_husband_name')->nullable()->after('mother_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'submitter_relation_to_mother')) {
                    $table->string('submitter_relation_to_mother')->nullable()->after('submitter_relation');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'transit_details')) {
                    $table->text('transit_details')->nullable()->after('address');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'death_details')) {
                    $table->text('death_details')->nullable()->after('deceased_husband_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'children_count')) {
                    $table->string('children_count', 50)->nullable()->after('death_details');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'children_details')) {
                    $table->text('children_details')->nullable()->after('children_count');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'income_aid_sources')) {
                    $table->text('income_aid_sources')->nullable()->after('children_details');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'expenses_debts_details')) {
                    $table->text('expenses_debts_details')->nullable()->after('income_aid_sources');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'zad_details')) {
                    $table->json('zad_details')->nullable()->after('expenses_debts_details');
                }
            });
        }

        if (Schema::hasTable('beneficiaries')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                if (!Schema::hasColumn('beneficiaries', 'source_name')) {
                    $table->string('source_name')->nullable()->after('full_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'reference_number')) {
                    $table->string('reference_number')->nullable()->after('source_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'mother_name')) {
                    $table->string('mother_name')->nullable()->after('full_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'deceased_husband_name')) {
                    $table->string('deceased_husband_name')->nullable()->after('mother_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'transit_details')) {
                    $table->text('transit_details')->nullable()->after('address');
                }
                if (!Schema::hasColumn('beneficiaries', 'death_details')) {
                    $table->text('death_details')->nullable()->after('deceased_husband_name');
                }
                if (!Schema::hasColumn('beneficiaries', 'income_aid_sources')) {
                    $table->text('income_aid_sources')->nullable()->after('assistance_type');
                }
                if (!Schema::hasColumn('beneficiaries', 'expenses_debts_details')) {
                    $table->text('expenses_debts_details')->nullable()->after('income_aid_sources');
                }
                if (!Schema::hasColumn('beneficiaries', 'zad_details')) {
                    $table->json('zad_details')->nullable()->after('expenses_debts_details');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mobile_case_applications')) {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                $table->dropColumn([
                    'source_name',
                    'reference_number',
                    'mother_name',
                    'deceased_husband_name',
                    'submitter_relation_to_mother',
                    'transit_details',
                    'death_details',
                    'children_count',
                    'children_details',
                    'income_aid_sources',
                    'expenses_debts_details',
                    'zad_details',
                ]);
            });
        }

        if (Schema::hasTable('beneficiaries')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropColumn([
                    'source_name',
                    'reference_number',
                    'mother_name',
                    'deceased_husband_name',
                    'transit_details',
                    'death_details',
                    'income_aid_sources',
                    'expenses_debts_details',
                    'zad_details',
                ]);
            });
        }
    }
};
