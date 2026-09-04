<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create web_donations table
        Schema::create('web_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('web_donor_id')->nullable();
            $table->unsignedBigInteger('donor_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');
            $table->string('category')->nullable();
            $table->integer('target_id')->default(0);
            $table->string('donationable_type')->nullable();
            $table->unsignedBigInteger('donationable_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('allocation_note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('web_donor_id')->references('id')->on('web_donors')->onDelete('set null');
        });

        // 2. Add web_donation_id to donation_proofs
        Schema::table('donation_proofs', function (Blueprint $table) {
            if (!Schema::hasColumn('donation_proofs', 'web_donation_id')) {
                $table->unsignedBigInteger('web_donation_id')->nullable()->after('donation_id');
                $table->foreign('web_donation_id')->references('id')->on('web_donations')->onDelete('cascade');
            }
        });

        // 3. Migrate data from donations to web_donations
        $webDonations = DB::table('donations')->where('source', 'website')->get();

        foreach ($webDonations as $d) {
            $webDonationId = DB::table('web_donations')->insertGetId([
                'web_donor_id' => $d->web_donor_id,
                'donor_id' => $d->donor_id,
                'amount' => $d->amount,
                'payment_method' => $d->payment_method,
                'status' => $d->status,
                'category' => $d->category,
                'target_id' => $d->target_id ?? 0,
                'donationable_type' => $d->donationable_type,
                'donationable_id' => $d->donationable_id ?? null,
                'campaign_id' => $d->campaign_id,
                'project_id' => $d->project_id,
                'allocation_note' => $d->allocation_note,
                'metadata' => $d->metadata ?? null,
                'created_at' => $d->created_at,
                'updated_at' => $d->updated_at,
            ]);

            // Update associated proofs
            DB::table('donation_proofs')
                ->where('donation_id', $d->id)
                ->update(['web_donation_id' => $webDonationId]);
        }

        // 4. Cleanup: Remove web donor records from main donations table
        // DB::table('donations')->where('source', 'website')->delete();
    }

    public function down(): void
    {
        Schema::table('donation_proofs', function (Blueprint $table) {
            $table->dropForeign(['web_donation_id']);
            $table->dropColumn('web_donation_id');
        });
        Schema::dropIfExists('web_donations');
    }
};
