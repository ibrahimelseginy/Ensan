<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Permission;

$keys = ['manage_it', 'manage_media'];
$permissions = Permission::whereIn('key', $keys)->get();

if ($permissions->isEmpty()) {
    echo "Both permissions manage_it and manage_media are confirmed deleted.\n";
} else {
    foreach ($permissions as $p) {
        echo "Found remaining permission: " . $p->key . "\n";
        // Force delete again if found
        $p->delete();
        echo "Deleted: " . $p->key . "\n";
    }
}
