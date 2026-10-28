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
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'beneficiary_id')) {
                $table->unsignedBigInteger('beneficiary_id')->nullable()->after('donor_id')->index();
            }
            if (!Schema::hasColumn('donations', 'paid_months')) {
                $table->json('paid_months')->nullable()->after('amount');
            }
        });

        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->after('monthly_donation_day')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'beneficiary_id')) {
                $table->dropColumn('beneficiary_id');
            }
            if (Schema::hasColumn('donations', 'paid_months')) {
                $table->dropColumn('paid_months');
            }
        });

        Schema::table('donors', function (Blueprint $table) {
            if (Schema::hasColumn('donors', 'next_due_date')) {
                $table->dropColumn('next_due_date');
            }
        });
    }
};
