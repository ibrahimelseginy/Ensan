المستندات المرفقة
<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$urlBases = [
    'http://127.0.0.1:8000/storage/',
    'http://localhost:8000/storage/',
    'http://localhost:8002/storage/',
    'https://system.ensaneg.com/storage/',
    'https://ensaneg.com/storage/',
];

$tables = [
    'web_settings' => ['value'],
    'web_news' => ['image_path'],
    'web_board_members' => ['image_path'],
    'web_partners' => ['logo_path'],
    'web_pages' => ['image_path'],
    'web_testimonials' => ['image_path'],
    'mobile_banners' => ['image_path'],
    'mobile_news' => ['image_path'],
    'donation_items' => ['icon', 'image'],
    'projects' => ['image_path', 'icon_path'],
    'campaigns' => ['image_path', 'icon_path'],
    'users' => ['profile_photo_path'],
    'delegates' => ['profile_photo_path'],
];

$total = 0;

foreach ($tables as $table => $columns) {
    foreach ($columns as $column) {
        try {
            if (!DB::getSchemaBuilder()->hasTable($table))
                continue;
            if (!DB::getSchemaBuilder()->hasColumn($table, $column))
                continue;

            foreach ($urlBases as $base) {
                $rows = DB::table($table)
                    ->where($column, 'like', $base . '%')
                    ->get(['id', $column]);

                foreach ($rows as $row) {
                    $newPath = ltrim(str_replace($base, '', $row->{$column}), '/');
                    DB::table($table)->where('id', $row->id)->update([$column => $newPath]);
                    echo "Fixed [{$table}][{$column}] id={$row->id}: {$row->{$column} } -> {$newPath}\n";
                    $total++;
                }
            }
        } catch (Exception $e) {
            echo "Skip [{$table}][{$column}]: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nTotal fixed: {$total} records\n";
