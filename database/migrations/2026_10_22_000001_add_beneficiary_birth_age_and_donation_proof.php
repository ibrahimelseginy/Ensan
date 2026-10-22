<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('beneficiaries', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('beneficiaries', 'deceased_husband_birth_date')) {
                $table->date('deceased_husband_birth_date')->nullable()->after('deceased_husband_name');
            }
            if (!Schema::hasColumn('beneficiaries', 'deceased_husband_death_date')) {
                $table->date('deceased_husband_death_date')->nullable()->after('deceased_husband_birth_date');
            }
            if (!Schema::hasColumn('beneficiaries', 'deceased_husband_age')) {
                $table->unsignedTinyInteger('deceased_husband_age')->nullable()->after('deceased_husband_death_date');
            }
        });

        Schema::table('beneficiary_family_members', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiary_family_members', 'death_date')) {
                $table->date('death_date')->nullable()->after('birth_date');
            }
        });

        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'operation_number')) {
                $table->string('operation_number')->nullable()->after('receipt_number');
            }
            if (!Schema::hasColumn('donations', 'transfer_receipt_path')) {
                $table->string('transfer_receipt_path')->nullable()->after('operation_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiaries', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
            if (Schema::hasColumn('beneficiaries', 'age')) {
                $table->dropColumn('age');
            }
            if (Schema::hasColumn('beneficiaries', 'deceased_husband_birth_date')) {
                $table->dropColumn('deceased_husband_birth_date');
            }
            if (Schema::hasColumn('beneficiaries', 'deceased_husband_death_date')) {
                $table->dropColumn('deceased_husband_death_date');
            }
            if (Schema::hasColumn('beneficiaries', 'deceased_husband_age')) {
                $table->dropColumn('deceased_husband_age');
            }
        });

        Schema::table('beneficiary_family_members', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiary_family_members', 'death_date')) {
                $table->dropColumn('death_date');
            }
        });

        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'operation_number')) {
                $table->dropColumn('operation_number');
            }
            if (Schema::hasColumn('donations', 'transfer_receipt_path')) {
                $table->dropColumn('transfer_receipt_path');
            }
        });
    }
};
