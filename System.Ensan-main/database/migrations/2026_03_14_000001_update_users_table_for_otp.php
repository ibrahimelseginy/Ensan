<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $col) {
            if (!Schema::hasColumn('users', 'phone')) {
                $col->string('phone')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $col->string('role')->default('donor')->after('phone');
            }
            if (!Schema::hasColumn('users', 'otp_code')) {
                $col->string('otp_code', 6)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $col->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $col) {
            $col->dropColumn(['phone', 'role', 'otp_code', 'otp_expires_at']);
        });
    }
};
