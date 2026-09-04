<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->decimal('estimated_value', 12, 2)->default(0);
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
