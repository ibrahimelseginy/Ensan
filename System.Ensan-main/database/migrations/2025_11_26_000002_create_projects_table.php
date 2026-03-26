<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('fixed')->default(true);
            $table->enum('status', ['active','archived'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
