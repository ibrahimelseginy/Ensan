<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('donors')) {
            return;
        }

        if (! Schema::hasColumn('donors', 'code')) {
            Schema::table('donors', function (Blueprint $table): void {
                $table->string('code')->nullable()->after('id');
            });
        }

        DB::table('donors')
            ->orderBy('id')
            ->select(['id', 'code'])
            ->chunkById(100, function ($donors): void {
                foreach ($donors as $donor) {
                    if (trim((string) $donor->code) === '') {
                        DB::table('donors')
                            ->where('id', $donor->id)
                            ->update(['code' => $this->uniqueCode((int) $donor->id)]);
                    }
                }
            });

        $duplicateCodes = DB::table('donors')
            ->whereNotNull('code')
            ->where('code', '<>', '')
            ->select('code')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('code');

        foreach ($duplicateCodes as $code) {
            $ids = DB::table('donors')->where('code', $code)->orderBy('id')->pluck('id');
            foreach ($ids->slice(1) as $id) {
                DB::table('donors')
                    ->where('id', $id)
                    ->update(['code' => $this->uniqueCode((int) $id)]);
            }
        }

        Schema::table('donors', function (Blueprint $table): void {
            $table->unique('code', 'donors_code_unique');
        });

        if (Schema::hasTable('beneficiary_sponsors')
            && Schema::hasColumn('donors', 'sponsored_beneficiary_id')) {
            DB::table('donors')
                ->whereNotNull('sponsored_beneficiary_id')
                ->orderBy('id')
                ->select(['id', 'sponsored_beneficiary_id'])
                ->chunkById(100, function ($donors): void {
                    foreach ($donors as $donor) {
                        DB::table('beneficiary_sponsors')->insertOrIgnore([
                            'beneficiary_id' => $donor->sponsored_beneficiary_id,
                            'donor_id' => $donor->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('donors')) {
            Schema::table('donors', function (Blueprint $table): void {
                $table->dropUnique('donors_code_unique');
            });
        }
    }

    private function uniqueCode(int $donorId): string
    {
        $base = 'DON-' . str_pad((string) $donorId, 6, '0', STR_PAD_LEFT);
        $candidate = $base;
        $suffix = 1;

        while (DB::table('donors')
            ->where('id', '<>', $donorId)
            ->where('code', $candidate)
            ->exists()) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
};
