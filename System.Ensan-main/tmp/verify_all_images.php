<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function checkModel($modelClass, $name) {
    if (!class_exists($modelClass)) {
        echo "Class {$modelClass} not found.\n";
        return;
    }
    $items = $modelClass::all();
    echo "\n--- Checking {$name} ({$modelClass}) ---\n";
    echo "Count: " . $items->count() . "\n";
    foreach ($items as $item) {
        $path = $item->image_path ?? $item->image ?? $item->img ?? $item->thumbnail;
        if ($path) {
            $url = method_exists($item, 'getImageUrlAttribute') ? $item->getImageUrlAttribute() : (method_exists($item, 'getFileUrl') ? $item->getFileUrl('image_path') : 'N/A');
            $fullPath = public_path('storage/' . $path);
            $exists = file_exists($fullPath);
            echo "ID: {$item->id}, Path: {$path}, Exists locally: " . ($exists ? 'YES' : 'NO') . ", URL: {$url}\n";
        }
    }
}

checkModel(\App\Models\WebNews::class, 'Web News');
checkModel(\App\Models\MobileNews::class, 'Mobile News');
checkModel(\App\Models\Campaign::class, 'Campaigns');
checkModel(\App\Models\Project::class, 'Projects');
checkModel(\App\Models\MobileHomeItem::class, 'Mobile Home Items');
