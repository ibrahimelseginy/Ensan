<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create web_donors table
        Schema::create('web_donors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. Add web_donor_id to donations table
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'web_donor_id')) {
                $table->unsignedBigInteger('web_donor_id')->nullable()->after('user_id');
                $table->foreign('web_donor_id')->references('id')->on('web_donors')->onDelete('set null');
            }
        });

        // 3. Migrate existing donors from users table
        $existingDonors = DB::table('users')->where('role', 'donor')->get();

        foreach ($existingDonors as $donor) {
            $newId = DB::table('web_donors')->insertGetId([
                'name' => $donor->name,
                'email' => $donor->email,
                'phone' => $donor->phone,
                'password' => $donor->password,
                'otp_code' => $donor->otp_code ?? null,
                'otp_expires_at' => $donor->otp_expires_at ?? null,
                'active' => $donor->active ?? true,
                'created_at' => $donor->created_at,
                'updated_at' => $donor->updated_at,
            ]);

            // Update donations to point to new web_donor_id
            DB::table('donations')->where('user_id', $donor->id)->update(['web_donor_id' => $newId]);
        }
        
        // Optional: Remove donor role users from users table IF we want total separation
        // DB::table('users')->where('role', 'donor')->delete();
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['web_donor_id']);
            $table->dropColumn('web_donor_id');
        });
        Schema::dropIfExists('web_donors');
    }
};
