<?php
header("Content-Type: text/html; charset=utf-8");
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "<html><body>";
echo "<h1>Test Encoding</h1>";
echo "Hardcoded Arabic: مرحبا بالعالم<br>";
echo "DB User Name: " . \App\Models\User::first()->name . "<br>";
echo "DB Role Name: " . \App\Models\Role::first()->name . "<br>";
echo "</body></html>";
