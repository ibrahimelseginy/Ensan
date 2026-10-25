<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Enhance tasks table
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->string('priority', 20)->default('medium')->after('status');
            }
            if (!Schema::hasColumn('tasks', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('priority')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('reviewed_at');
            }
            if (!Schema::hasColumn('tasks', 'order')) {
                $table->integer('order')->default(0)->after('completed_at');
            }
        });

        // 2. Change status column type to string(50) if enum to allow 'under_review'
        try {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // 3. Shared tasks: task_user pivot table
        if (!Schema::hasTable('task_user')) {
            Schema::create('task_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role', 50)->default('collaborator');
                $table->timestamps();
                $table->unique(['task_id', 'user_id']);
            });
        }

        // 4. Task attachments
        if (!Schema::hasTable('task_attachments')) {
            Schema::create('task_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('file_name');
                $table->string('file_path');
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('mime_type', 100)->nullable();
                $table->timestamps();
            });
        }

        // 5. Task change log / audit trail
        if (!Schema::hasTable('task_logs')) {
            Schema::create('task_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 50);
                $table->string('from_status', 50)->nullable();
                $table->string('to_status', 50)->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_logs');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_user');
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
            }
            $columns = array_filter(['priority', 'reviewed_by', 'reviewed_at', 'completed_at', 'order'], function($col) {
                return Schema::hasColumn('tasks', $col);
            });
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
