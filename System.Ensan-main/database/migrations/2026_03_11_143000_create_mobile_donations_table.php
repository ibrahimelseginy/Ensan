<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->string('donor_phone');
            $table->string('donor_address')->nullable();
            $table->decimal('donation_amount', 12, 2);
            $table->string('donation_for'); // e.g. project_id, campaign_id, or general
            $table->string('payment_method'); // e.g. fawry, card, wallet
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_donations');
    }
};
