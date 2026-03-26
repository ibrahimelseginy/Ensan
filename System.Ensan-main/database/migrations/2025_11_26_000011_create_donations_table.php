<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->enum('type', ['cash','in_kind']);
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency')->default('EGP');
            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('delegate_id')->nullable()->constrained('delegates')->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained('travel_routes')->nullOnDelete();
            $table->text('allocation_note')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
