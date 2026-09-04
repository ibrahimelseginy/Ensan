<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            $projects = [
                ['name' => 'مشروع كسوة', 'fixed' => true, 'status' => 'active'],
                ['name' => 'مشروع بعثاء الأمل', 'fixed' => true, 'status' => 'active'],
                ['name' => 'مشروع زاد', 'fixed' => true, 'status' => 'active'],
                ['name' => 'مشروع مدرار', 'fixed' => true, 'status' => 'active'],
                ['name' => 'دار ضيافة طنطا', 'fixed' => true, 'status' => 'active'],
                ['name' => 'دار ضيافة كفر الشيخ', 'fixed' => true, 'status' => 'active'],
            ];
            foreach ($projects as $p) {
                if (!DB::table('projects')->where('name', $p['name'])->exists()) {
                    DB::table('projects')->insert($p);
                }
            }
        }

        if (Schema::hasTable('campaigns')) {
            $year = (int) date('Y');
            $campaigns = [
                ['name' => 'حملة الشتاء', 'season_year' => $year, 'status' => 'active'],
                ['name' => 'حملة رمضان', 'season_year' => $year, 'status' => 'active'],
                ['name' => 'حملة المدارس', 'season_year' => $year, 'status' => 'active'],
                ['name' => 'عيد الفطر', 'season_year' => $year, 'status' => 'active'],
                ['name' => 'عيد الأضحى', 'season_year' => $year, 'status' => 'active'],
            ];
            foreach ($campaigns as $c) {
                if (!DB::table('campaigns')->where('name', $c['name'])->where('season_year', $c['season_year'])->exists()) {
                    DB::table('campaigns')->insert($c);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            DB::table('projects')->whereIn('name', [
                'مشروع كسوة','مشروع بعثاء الأمل','مشروع زاد','مشروع مدرار','دار ضيافة طنطا','دار ضيافة كفر الشيخ'
            ])->delete();
        }
        if (Schema::hasTable('campaigns')) {
            $year = (int) date('Y');
            DB::table('campaigns')->whereIn('name', [
                'حملة الشتاء','حملة رمضان','حملة المدارس','عيد الفطر','عيد الأضحى'
            ])->where('season_year', $year)->delete();
        }
    }
};

