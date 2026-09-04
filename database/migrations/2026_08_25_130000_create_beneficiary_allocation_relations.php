<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('allocated_beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['beneficiary_id', 'allocated_beneficiary_id'], 'beneficiary_allocation_unique');
        });

        Schema::create('beneficiary_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['beneficiary_id', 'donor_id'], 'beneficiary_sponsor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_sponsors');
        Schema::dropIfExists('beneficiary_allocations');
    }
};
