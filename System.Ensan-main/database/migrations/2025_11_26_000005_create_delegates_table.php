<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('delegates')) {
            Schema::create('delegates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('route_id')->nullable()->constrained('travel_routes')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('delegates');
    }
};
