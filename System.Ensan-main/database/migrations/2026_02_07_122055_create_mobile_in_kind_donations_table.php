<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('mobile_in_kind_donations')) {
            Schema::create('mobile_in_kind_donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name')->nullable();
            $table->string('donor_phone');
            $table->string('item_name');
            $table->string('item_description')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('image_path')->nullable();
            $table->string('pickup_address')->nullable();
            $table->timestamp('preferred_pickup_time')->nullable();
            $table->string('status')->default('pending'); // pending, scheduled, collected
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_in_kind_donations');
    }
};
