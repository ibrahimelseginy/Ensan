<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donor_pending_sponsorships')) {
            return;
        }

        Schema::create('donor_pending_sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->enum('type', ['child', 'family'])->default('child');
            $table->decimal('monthly_amount', 12, 2);
            $table->string('target_notes', 500)->nullable();
            $table->enum('status', ['pending', 'linked', 'cancelled'])->default('pending');
            $table->foreignId('linked_family_member_id')->nullable()->constrained('beneficiary_family_members')->nullOnDelete();
            $table->foreignId('linked_beneficiary_id')->nullable()->constrained('beneficiaries')->nullOnDelete();
            $table->timestamp('linked_at')->nullable();
            $table->foreignId('linked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['donor_id', 'status'], 'donor_pending_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_pending_sponsorships');
    }
};
