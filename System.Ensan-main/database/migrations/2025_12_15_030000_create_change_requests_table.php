<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('change_requests')) {
            Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('model_type'); // App\Models\Donation, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // Null for CREATE actions
            $table->string('action'); // 'create', 'update', 'delete'
            $table->json('payload')->nullable(); // The data to create/update
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('change_requests');
    }
};
