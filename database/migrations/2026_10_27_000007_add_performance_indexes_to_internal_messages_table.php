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
                if (!Schema::hasColumn('internal_messages', 'is_bot')) {
                    $table->boolean('is_bot')->default(false)->after('receiver_id');
                }
            });

            $existingIndexes = [];
            try {
                $existingIndexes = collect(Schema::getIndexes('internal_messages'))->pluck('name')->toArray();
            } catch (\Throwable $e) {
                // fallback
            }

            Schema::table('internal_messages', function (Blueprint $table) use ($existingIndexes) {
                if (!in_array('idx_im_rec_read_id', $existingIndexes)) {
                    $table->index(['receiver_id', 'read_at', 'id'], 'idx_im_rec_read_id');
                }
                if (!in_array('idx_im_snd_rec_id', $existingIndexes)) {
                    $table->index(['sender_id', 'receiver_id', 'id'], 'idx_im_snd_rec_id');
                }
                if (!in_array('idx_im_rec_id', $existingIndexes)) {
                    $table->index(['receiver_id', 'id'], 'idx_im_rec_id');
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
                if (Schema::hasColumn('internal_messages', 'is_bot')) {
                    $table->dropColumn('is_bot');
                }
                $table->dropIndex('idx_im_rec_read_id');
                $table->dropIndex('idx_im_snd_rec_id');
                $table->dropIndex('idx_im_rec_id');
            });
        }
    }
};
