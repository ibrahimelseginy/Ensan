<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('internal_messages')) {
            Schema::table('internal_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('internal_messages', 'attachment_path')) {
                    $table->string('attachment_path', 255)->nullable()->after('message');
                }
                if (!Schema::hasColumn('internal_messages', 'attachment_type')) {
                    $table->string('attachment_type', 50)->nullable()->after('attachment_path');
                }
                if (!Schema::hasColumn('internal_messages', 'attachment_name')) {
                    $table->string('attachment_name', 255)->nullable()->after('attachment_type');
                }
                if (!Schema::hasColumn('internal_messages', 'attachment_size')) {
                    $table->string('attachment_size', 50)->nullable()->after('attachment_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('internal_messages')) {
            Schema::table('internal_messages', function (Blueprint $table) {
                $columns = ['attachment_path', 'attachment_type', 'attachment_name', 'attachment_size'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('internal_messages', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
