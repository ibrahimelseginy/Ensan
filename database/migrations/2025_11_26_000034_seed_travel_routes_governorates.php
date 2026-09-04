<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('travel_routes')) { return; }
        $govs = [
            'القاهرة','الجيزة','القليوبية','الإسكندرية','البحيرة','كفر الشيخ','الغربية','المنوفية','دمياط','الدقهلية','الشرقية','بورسعيد','الإسماعيلية','السويس','شمال سيناء','جنوب سيناء','بني سويف','الفيوم','المنيا','أسيوط','سوهاج','الأقصر','قنا','أسوان','مطروح','البحر الأحمر','الوادي الجديد'
        ];
        foreach ($govs as $name) {
            if (!DB::table('travel_routes')->where('name',$name)->exists()) {
                DB::table('travel_routes')->insert(['name' => $name]);
            }
        }
    }
    public function down(): void
    {
        if (!Schema::hasTable('travel_routes')) { return; }
        $govs = [
            'القاهرة','الجيزة','القليوبية','الإسكندرية','البحيرة','كفر الشيخ','الغربية','المنوفية','دمياط','الدقهلية','الشرقية','بورسعيد','الإسماعيلية','السويس','شمال سيناء','جنوب سيناء','بني سويف','الفيوم','المنيا','أسيوط','سوهاج','الأقصر','قنا','أسوان','مطروح','البحر الأحمر','الوادي الجديد'
        ];
        DB::table('travel_routes')->whereIn('name', $govs)->delete();
    }
};

