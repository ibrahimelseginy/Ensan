<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Permission;

$permissions = Permission::where('key', 'like', 'accounts.%')
                ->orWhere('key', 'like', 'journal_entries.%')
                ->get();

foreach ($permissions as $p) {
    echo $p->key . " - " . $p->name . "\n";
}
