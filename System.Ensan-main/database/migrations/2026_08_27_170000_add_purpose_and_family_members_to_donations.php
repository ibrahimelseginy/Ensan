<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->string('purpose', 40)->nullable()->after('receipt_number')->index();
        });

        Schema::create('donation_family_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->foreignId('family_member_id')->constrained('beneficiary_family_members')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['donation_id', 'family_member_id'], 'donation_family_member_unique');
        });

        foreach ([
            'kafalat_aytam' => 'kafalat_aytam',
            'kafalat_yateem' => 'kafalat_aytam',
            'طفل' => 'kafalat_aytam',
            'kafalat_awram' => 'kafalat_awram',
            'sadaqat' => 'sadaqat',
            'zakat_maal' => 'zakat_maal',
            'sadaqa_jariya' => 'sadaqa_jariya',
        ] as $legacyValue => $purpose) {
            DB::table('donations')
                ->whereNull('purpose')
                ->where('allocation_note', 'like', '%sponsorship=' . $legacyValue . '%')
                ->update(['purpose' => $purpose]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_family_members');
        Schema::table('donations', function (Blueprint $table): void {
            $table->dropIndex(['purpose']);
            $table->dropColumn('purpose');
        });
    }
};
