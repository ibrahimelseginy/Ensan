<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebSetting;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Support\Facades\Storage;

echo "--- Checking WebSettings ---\n";
$settings = WebSetting::all();
foreach ($settings as $s) {
    if (str_contains($s->key, 'image') || str_contains($s->key, 'slider') || str_contains($s->key, 'icon') || str_contains($s->key, 'logo') || str_contains($s->key, 'banner')) {
        $path = $s->value;
        if ($path && !Storage::disk('public')->exists($path)) {
            echo "[MISSING] WebSetting key: {$s->key}, path: {$path}\n";
        } elseif ($path) {
            echo "[OK] WebSetting key: {$s->key}, path: {$path}\n";
        }
    }
}

echo "\n--- Checking Projects ---\n";
$projects = Project::all();
foreach ($projects as $p) {
    $cols = ['image_path', 'icon_path', 'badge_icon', 'action_icon'];
    foreach ($cols as $col) {
        $path = $p->$col;
        if ($path && !Storage::disk('public')->exists($path)) {
            echo "[MISSING] Project ID: {$p->id}, Name: {$p->name}, Column: {$col}, Path: {$path}\n";
        }
    }
}

echo "\n--- Checking Campaigns ---\n";
$campaigns = Campaign::all();
foreach ($campaigns as $c) {
    $path = $c->image_path;
    if ($path && !Storage::disk('public')->exists($path)) {
        echo "[MISSING] Campaign ID: {$c->id}, Name: {$c->name}, Path: {$path}\n";
    }
}
