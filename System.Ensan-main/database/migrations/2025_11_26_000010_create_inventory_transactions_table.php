<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->enum('type', ['in','transfer','out']);
            $table->decimal('quantity', 12, 3);
            $table->foreignId('source_donation_id')->nullable();
            $table->index('source_donation_id');
            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
