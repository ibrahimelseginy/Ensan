<?php
/**
 * سكريبت لتنظيف المسارات القديمة في قاعدة البيانات
 * يُزيل أي URL كامل (localhost أو production) ويُبقي المسار النسبي فقط
 *
 * طريقة التشغيل:
 *   php artisan tinker --execute="require base_path('scripts/normalize_image_paths.php');"
 * أو:
 *   php scripts/normalize_image_paths.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use Illuminate\Support\Facades\DB;
use App\Traits\UploadsImages;

$urlPatterns = [
    'http://127.0.0.1:8000/storage/',
    'http://localhost:8000/storage/',
    'http://localhost:8002/storage/',
    'https://system.ensaneg.com/storage/',
    'https://ensaneg.com/storage/',
    '/storage/',
    'storage/',
];

$tables = [
    'web_settings'         => ['value'],
    'web_news'             => ['image_path'],
    'web_board_members'    => ['image_path'],
    'web_partners'         => ['logo_path'],
    'web_pages'            => ['image_path'],
    'web_testimonials'     => ['image_path'],
    'web_volunteer_walls'  => ['image_path'],
    'web_events'           => ['image_path'],
    'web_contact_messages' => ['image_path'],
    'mobile_banners'       => ['image_path'],
    'mobile_news'          => ['image_path'],
    'mobile_home_items'    => ['image_path', 'icon'],
    'ensan_pillars'        => ['image_path', 'cover_path'],
    'ensan_pillar_cards'   => ['image_path'],
    'donation_items'       => ['icon', 'image'],
    'projects'             => ['image_path', 'icon_path'],
    'campaigns'            => ['image_path', 'icon_path'],
    'users'                => ['profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image'],
    'delegates'            => ['profile_photo_path'],
];

$totalFixed = 0;

foreach ($tables as $table => $columns) {
    foreach ($columns as $column) {
        // تحقق من وجود الجدول والعمود
        if (!DB::getSchemaBuilder()->hasTable($table)) continue;
        if (!DB::getSchemaBuilder()->hasColumn($table, $column)) continue;

        $rows = DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->get(['id', $column]);

        foreach ($rows as $row) {
            $original = $row->{$column};
            $cleaned  = UploadsImages::normalizeImagePath($original);

            if ($cleaned !== null && $cleaned !== $original) {
                DB::table($table)->where('id', $row->id)->update([$column => $cleaned]);
                echo "✅ [{$table}] id={$row->id} | {$column}: \n   OLD: {$original}\n   NEW: {$cleaned}\n\n";
                $totalFixed++;
            }
        }
    }
}

echo "\n🎉 تم تنظيف {$totalFixed} سجل في قاعدة البيانات.\n";
