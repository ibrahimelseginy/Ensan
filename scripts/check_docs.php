<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Models\User;

$user = User::whereNotNull('contract_image')->first();
if ($user) {
    $raw = $user->contract_image;
    $url = $user->getFileUrl('contract_image');
    $exists = Storage::disk('public')->exists($raw) ? 'YES' : 'NO';
    $physPath = storage_path('app/public/' . $raw);
    $fileExists = file_exists($physPath) ? 'YES' : 'NO';

    echo "Raw path: {$raw}\n";
    echo "getFileUrl: {$url}\n";
    echo "Exists in disk 'public': {$exists}\n";
    echo "Physical path: {$physPath}\n";
    echo "File on disk: {$fileExists}\n";
} else {
    echo "No user with contract_image found\n";
    // فحص الـ middleware
    echo "\nChecking web middleware stack...\n";
    $routes = app('router')->getRoutes();
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'users')) {
            echo $route->uri() . ' | ' . implode(',', $route->middleware()) . "\n";
            break;
        }
    }
}

// فحص Policy المحتمل
echo "\nUser count with contract_image: " . User::whereNotNull('contract_image')->count() . "\n";
echo "Total users: " . User::count() . "\n";
