<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable();
        });
        Schema::table('tokens', function (Blueprint $table) {
            $table->boolean('hashed')->default(false);
        });
        DB::table('tokens')->where('hashed', false)->orderBy('id')->chunkById(200, function ($tokens) {
            foreach ($tokens as $token) {
                DB::table('tokens')->where('id', $token->id)->update([
                    'token' => hash('sha256', $token->token),
                    'hashed' => true,
                    'expires_at' => $token->expires_at ?? now()->addDays(30),
                ]);
            }
        });
        DB::table('anasen_personal_access_tokens')->whereNull('expires_at')->update(['expires_at' => now()->addDays(30)]);
    }

    public function down(): void
    {
        // Hashes cannot be reversed. Revoke sessions before restoring the old schema.
        DB::table('tokens')->delete();
        Schema::table('tokens', fn (Blueprint $table) => $table->dropColumn('hashed'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('phone_verified_at'));
    }
};
