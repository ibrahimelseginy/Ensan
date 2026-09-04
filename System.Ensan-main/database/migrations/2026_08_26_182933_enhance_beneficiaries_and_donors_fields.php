<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('donors', 'monthly_allocation_target')) {
                $table->string('monthly_allocation_target')->nullable()->after('allocation_type');
            }
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('beneficiaries', 'patient_name')) {
                $table->string('patient_name')->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('beneficiaries', 'patient_age')) {
                $table->integer('patient_age')->nullable()->after('patient_name');
            }
            if (!Schema::hasColumn('beneficiaries', 'patient_code')) {
                $table->string('patient_code')->nullable()->after('patient_age');
            }
            if (!Schema::hasColumn('beneficiaries', 'visa_card_number')) {
                $table->string('visa_card_number')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('beneficiaries', 'collection_day')) {
                $table->integer('collection_day')->nullable()->after('assistance_type');
            }
            if (!Schema::hasColumn('beneficiaries', 'collection_method')) {
                $table->string('collection_method')->nullable()->after('collection_day');
            }
            if (!Schema::hasColumn('beneficiaries', 'family_members_data')) {
                $table->json('family_members_data')->nullable()->after('children_names');
            }
            if (!Schema::hasColumn('beneficiaries', 'monthly_sponsorship_amount')) {
                $table->decimal('monthly_sponsorship_amount', 12, 2)->nullable()->after('family_members_data');
            }
            if (!Schema::hasColumn('beneficiaries', 'brothers_count')) {
                $table->integer('brothers_count')->nullable()->after('monthly_sponsorship_amount');
            }
            if (!Schema::hasColumn('beneficiaries', 'adult_children_count')) {
                $table->integer('adult_children_count')->nullable()->after('brothers_count');
            }
            if (!Schema::hasColumn('beneficiaries', 'adult_children_ages')) {
                $table->text('adult_children_ages')->nullable()->after('adult_children_count');
            }
            if (!Schema::hasColumn('beneficiaries', 'sponsorship_scope_type')) {
                $table->string('sponsorship_scope_type')->nullable()->after('child_sponsorship_type');
            }
            if (!Schema::hasColumn('beneficiaries', 'archived_reason')) {
                $table->string('archived_reason')->nullable()->after('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn(['code', 'monthly_allocation_target']);
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn([
                'guardian_name', 'patient_name', 'patient_age', 'patient_code',
                'visa_card_number', 'collection_day', 'collection_method',
                'family_members_data', 'monthly_sponsorship_amount', 'brothers_count',
                'adult_children_count', 'adult_children_ages', 'sponsorship_scope_type',
                'archived_reason'
            ]);
        });
    }
};
