<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('financial_closures')) {
            Schema::create('financial_closures', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('branch')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('financial_closures');
    }
};
