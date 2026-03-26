<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('donors')) {
            Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['individual','organization'])->default('individual');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->enum('classification', ['one_time','recurring'])->default('one_time');
            $table->enum('recurring_cycle', ['monthly','yearly'])->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
