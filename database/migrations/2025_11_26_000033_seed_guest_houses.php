<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('guest_houses')) {
            $items = [
                ['name' => 'دار ضيافة طنطا', 'status' => 'active'],
                ['name' => 'دار ضيافة كفر الشيخ', 'status' => 'active'],
            ];
            foreach ($items as $gh) {
                if (!DB::table('guest_houses')->where('name',$gh['name'])->exists()) {
                    DB::table('guest_houses')->insert($gh);
                }
            }
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('guest_houses')) {
            DB::table('guest_houses')->whereIn('name', ['دار ضيافة طنطا','دار ضيافة كفر الشيخ'])->delete();
        }
    }
};

