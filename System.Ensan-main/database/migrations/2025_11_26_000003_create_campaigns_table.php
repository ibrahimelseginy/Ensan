<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('season_year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active','archived'])->default('active');
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
