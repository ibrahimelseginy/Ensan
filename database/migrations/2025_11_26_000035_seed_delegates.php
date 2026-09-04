<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('delegates')) { return; }

        $pairs = [
            ['name' => 'مندوب راضي القاهرة', 'phone' => '01000000001', 'route' => 'القاهرة'],
            ['name' => 'مندوب راضي الجيزة',   'phone' => '01000000002', 'route' => 'الجيزة'],
            ['name' => 'مندوب راضي القليوبية','phone' => '01000000003', 'route' => 'القليوبية'],
            ['name' => 'مندوب راضي الإسكندرية','phone' => '01000000004', 'route' => 'الإسكندرية'],
            ['name' => 'مندوب راضي الشرقية',  'phone' => '01000000005', 'route' => 'الشرقية'],
        ];

        foreach ($pairs as $p) {
            if (!DB::table('delegates')->where('name', $p['name'])->exists()) {
                $routeId = DB::table('travel_routes')->where('name', $p['route'])->value('id');
                DB::table('delegates')->insert([
                    'name' => $p['name'],
                    'phone' => $p['phone'],
                    'email' => null,
                    'route_id' => $routeId,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('delegates')) { return; }
        $names = [
            'مندوب راضي القاهرة',
            'مندوب راضي الجيزة',
            'مندوب راضي القليوبية',
            'مندوب راضي الإسكندرية',
            'مندوب راضي الشرقية',
        ];
        DB::table('delegates')->whereIn('name', $names)->delete();
    }
};

