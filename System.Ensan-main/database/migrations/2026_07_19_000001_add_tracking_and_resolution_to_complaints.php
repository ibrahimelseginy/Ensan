<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'tracking_code')) {
                $table->string('tracking_code', 20)->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('complaints', 'resolution')) {
                $table->text('resolution')->nullable()->after('message');
            }
            if (!Schema::hasColumn('complaints', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resolution');
            }
        });

        // توليد كود تتبع للشكاوي الموجودة
        DB::table('complaints')->whereNull('tracking_code')->orderBy('id')->each(function ($complaint) {
            DB::table('complaints')->where('id', $complaint->id)->update([
                'tracking_code' => 'ENS-' . str_pad($complaint->id, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['tracking_code', 'resolution', 'resolved_at']);
        });
    }
};
