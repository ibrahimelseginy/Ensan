<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Permission;

$keysToDelete = ['manage_it', 'manage_media'];

foreach ($keysToDelete as $key) {
    $permission = Permission::where('key', $key)->first();
    if ($permission) {
        $permission->delete();
        echo "Deleted permission: {$key}\n";
    } else {
        echo "Permission not found: {$key}\n";
    }
}
