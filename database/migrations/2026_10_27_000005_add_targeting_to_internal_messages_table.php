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
                if (!Schema::hasColumn('internal_messages', 'target_type')) {
                    $table->string('target_type', 30)->default('all')->after('receiver_id');
                }
                if (!Schema::hasColumn('internal_messages', 'visible_to_users')) {
                    $table->json('visible_to_users')->nullable()->after('target_type');
                }
                if (!Schema::hasColumn('internal_messages', 'hidden_from_users')) {
                    $table->json('hidden_from_users')->nullable()->after('visible_to_users');
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
                $columns = ['target_type', 'visible_to_users', 'hidden_from_users'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('internal_messages', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
