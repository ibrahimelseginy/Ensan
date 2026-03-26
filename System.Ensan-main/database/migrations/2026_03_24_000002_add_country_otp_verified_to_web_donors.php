<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_donors', function (Blueprint $table) {
            if (!Schema::hasColumn('web_donors', 'country')) {
                $table->string('country')->nullable()->after('governorate');
            }
            if (!Schema::hasColumn('web_donors', 'otp_verified')) {
                // false = OTP never completed (first login required)
                // true  = OTP was verified at least once (skip OTP on subsequent logins)
                $table->boolean('otp_verified')->default(false)->after('otp_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('web_donors', function (Blueprint $table) {
            $table->dropColumn(['country', 'otp_verified']);
        });
    }
};
