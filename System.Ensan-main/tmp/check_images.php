<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MobileHomeItem;

$items = MobileHomeItem::all();
echo "Total MobileHomeItems: " . $items->count() . "\n";

foreach ($items as $item) {
    if ($item->image_path) {
        $fullPath = public_path('storage/' . $item->image_path);
        $exists = file_exists($fullPath);
        echo "ID: {$item->id}, Type: {$item->type}, Path: {$item->image_path}, Exists: " . ($exists ? 'YES' : 'NO') . "\n";
        if (!$exists) {
            echo "  Full System Path searched: {$fullPath}\n";
        }
    } else {
        echo "ID: {$item->id}, Type: {$item->type}, Path: NULL\n";
    }
}
