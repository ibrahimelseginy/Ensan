<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_notification_states')) {
            Schema::create('user_notification_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('notification_key', 191);
                $table->boolean('is_read')->default(false);
                $table->boolean('is_dismissed')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamp('dismissed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'notification_key'], 'user_notif_key_unique');
                $table->index(['user_id', 'is_read', 'is_dismissed']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_states');
    }
};
