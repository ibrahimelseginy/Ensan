<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Permission;

$p1 = Permission::where('key', 'manage_it')->first();
$p2 = Permission::where('key', 'manage_media')->first();

if (!$p1) echo "manage_it is GONE.\n";
else echo "manage_it EXISTS.\n";

if (!$p2) echo "manage_media is GONE.\n";
else echo "manage_media EXISTS.\n";
