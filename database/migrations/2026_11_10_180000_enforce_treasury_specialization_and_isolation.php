<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('treasuries')) {
            DB::statement("ALTER TABLE `treasuries` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'main'");

            if (!Schema::hasColumn('treasuries', 'guest_house_id')) {
                Schema::table('treasuries', function (Blueprint $table) {
                    $table->unsignedBigInteger('guest_house_id')->nullable()->after('is_active');
                });
            }

            // 1. الاستقبال -> reception
            DB::table('treasuries')
                ->where(function ($q) {
                    $q->where('name', 'like', '%استقبال%')
                      ->orWhere('code', 'like', 'REC%');
                })
                ->update([
                    'type' => 'reception',
                    'guest_house_id' => null,
                    'project_id' => null,
                    'campaign_id' => null,
                ]);

            // 2. دور الضيافة -> guest_house + ربط كل خزينة بدار الضيافة الخاصة بها
            $guestHouses = DB::table('guest_houses')->get(['id', 'name', 'location']);
            foreach ($guestHouses as $gh) {
                $ghName = (string) $gh->name;
                $ghLoc = (string) ($gh->location ?? '');
                if (str_contains($ghName, 'كفر') || str_contains($ghLoc, 'كفر')) {
                    DB::table('treasuries')
                        ->where(function ($q) {
                            $q->where('name', 'like', '%ضياف%كفر%')
                              ->orWhere('code', 'KFS-DH-001');
                        })
                        ->update([
                            'type' => 'guest_house',
                            'guest_house_id' => $gh->id,
                            'project_id' => null,
                            'campaign_id' => null,
                        ]);
                } elseif (str_contains($ghName, 'غربي') || str_contains($ghName, 'طنطا') || str_contains($ghLoc, 'غربي') || str_contains($ghLoc, 'طنطا')) {
                    DB::table('treasuries')
                        ->where(function ($q) {
                            $q->where('name', 'like', '%ضياف%غربي%')
                              ->orWhere('name', 'like', '%ضياف%طنطا%')
                              ->orWhere('code', 'TNT-001');
                        })
                        ->update([
                            'type' => 'guest_house',
                            'guest_house_id' => $gh->id,
                            'project_id' => null,
                            'campaign_id' => null,
                        ]);
                }
            }

            // 3. المشاريع -> project + ربط كل خزينة مشروع بالمشروع الخاص بها
            $projects = DB::table('projects')->get(['id', 'name']);
            foreach ($projects as $proj) {
                $pName = trim((string) $proj->name);
                if ($pName !== '') {
                    DB::table('treasuries')
                        ->where('name', 'like', '%' . $pName . '%')
                        ->update([
                            'type' => 'project',
                            'project_id' => $proj->id,
                            'guest_house_id' => null,
                            'campaign_id' => null,
                        ]);
                }
            }

            // 4. الحملات -> campaign + ربط كل خزينة حملة بالحملة الخاصة بها
            $campaigns = DB::table('campaigns')->get(['id', 'name']);
            foreach ($campaigns as $camp) {
                $cName = trim((string) $camp->name);
                if ($cName !== '') {
                    DB::table('treasuries')
                        ->where('name', 'like', '%' . $cName . '%')
                        ->update([
                            'type' => 'campaign',
                            'campaign_id' => $camp->id,
                            'guest_house_id' => null,
                        ]);
                }
            }
        }

        // 5. تنظيف جدول guest_house_custodies من أي ربط خاطئ بخزينة الاستقبال أو المشاريع أو الحملات
        if (Schema::hasTable('guest_house_custodies') && Schema::hasTable('treasuries')) {
            $nonGhTreasuryIds = DB::table('treasuries')
                ->whereIn('type', ['reception', 'project', 'campaign', 'main'])
                ->pluck('id')
                ->all();

            if (!empty($nonGhTreasuryIds)) {
                DB::table('guest_house_custodies')
                    ->whereIn('treasury_id', $nonGhTreasuryIds)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        // Safe non-destructive migration
    }
};

