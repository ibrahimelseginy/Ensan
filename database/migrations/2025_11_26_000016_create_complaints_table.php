<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->enum('source_type', ['donor','beneficiary','employee']);
            $table->unsignedBigInteger('source_id');
            $table->foreignId('against_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open','in_progress','closed'])->default('open');
            $table->string('subject');
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->index(['source_type','source_id']);
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
