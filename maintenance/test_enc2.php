<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "DB HEX: " . bin2hex(\App\Models\Role::first()->name) . "\n";
echo "EXPECTED HEX: " . bin2hex('مدير المؤسسة بالكامل') . "\n";
