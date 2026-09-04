<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// List all permissions to see if there are any similar ones
$perms = DB::table('permissions')->select('key', 'name')->get();
echo "All Permissions:\n";
foreach ($perms as $p) {
    if (strpos($p->key, 'manage') !== false || strpos($p->key, 'media') !== false || strpos($p->key, 'it') !== false) {
        echo $p->key . " (" . $p->name . ")\n";
    }
}
