<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $col) {
            if (!Schema::hasColumn('donations', 'donationable_type')) {
                $col->string('donationable_type')->nullable()->after('donor_id');
            }
            if (!Schema::hasColumn('donations', 'donationable_id')) {
                $col->unsignedBigInteger('donationable_id')->nullable()->after('donationable_type');
            }
            if (!Schema::hasColumn('donations', 'payment_method')) {
                $col->string('payment_method')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('donations', 'user_id')) {
                $col->unsignedBigInteger('user_id')->nullable()->after('id');
            } else {
                $col->unsignedBigInteger('user_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $col) {
            $col->dropColumn(['donationable_type', 'donationable_id', 'payment_method', 'is_flagged']);
        });
    }
};
