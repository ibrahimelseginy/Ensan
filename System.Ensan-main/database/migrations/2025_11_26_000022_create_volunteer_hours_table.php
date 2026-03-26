<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('volunteer_hours')) {
            Schema::create('volunteer_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('hours', 5, 2);
            $table->string('task')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('volunteer_hours');
    }
};
