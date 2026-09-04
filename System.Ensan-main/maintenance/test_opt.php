<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\UploadedFile;

$path = __DIR__ . '/test.jpg';
if (!file_exists($path)) {
    $img = imagecreatetruecolor(100, 100);
    imagejpeg($img, $path);
    imagedestroy($img);
}

if (!file_exists($path)) {
    echo "test.jpg could not be created\n";
    exit;
}

$file = new UploadedFile($path, 'test.jpg', 'image/jpeg', null, true);
$res = app(\App\Services\ImageUploadService::class)->upload($file, 'website/test');
echo "Result: " . ($res ?: 'NULL') . "\n";
