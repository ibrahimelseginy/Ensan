<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('backup_settings')) {
            Schema::create('backup_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('auto_enabled')->default(true);
                $table->enum('frequency', ['daily', 'weekly'])->default('daily');
                $table->string('schedule_time', 5)->default('02:00');
                $table->enum('default_scope', ['full', 'db_only'])->default('db_only');
                $table->unsignedSmallInteger('keep_daily_count')->default(7);
                $table->unsignedSmallInteger('keep_weekly_count')->default(4);
                $table->unsignedSmallInteger('keep_monthly_count')->default(3);
                $table->unsignedInteger('max_storage_mb')->default(10240);
                $table->boolean('encrypt_backups')->default(true);
                $table->timestamp('last_run_at')->nullable();
                $table->string('last_status', 30)->nullable();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('backup_snapshots')) {
            Schema::create('backup_snapshots', function (Blueprint $table) {
                $table->id();
                $table->string('snapshot_code', 48)->unique();
                $table->enum('scope', ['full', 'db_only'])->default('db_only')->index();
                $table->enum('trigger_type', ['manual', 'scheduled', 'pre_restore', 'uploaded'])->default('manual')->index();
                $table->enum('status', ['running', 'completed', 'failed'])->default('running')->index();
                $table->enum('format', ['ensanbak', 'tar_gz'])->default('ensanbak');
                $table->string('file_path', 500)->nullable();
                $table->string('file_name', 255)->nullable();
                $table->unsignedBigInteger('file_size_bytes')->default(0);
                $table->char('sha256_checksum', 64)->nullable();
                $table->char('hmac_signature', 64)->nullable();
                $table->boolean('is_encrypted')->default(true);
                $table->boolean('is_pinned')->default(false)->index();
                $table->unsignedInteger('db_tables_count')->default(0);
                $table->unsignedInteger('files_count')->default(0);
                $table->string('app_version', 40)->nullable();
                $table->text('notes')->nullable();
                $table->text('error_message')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_snapshots');
        Schema::dropIfExists('backup_settings');
    }
};
