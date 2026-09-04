<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "ROLES:\n";
$roles = Role::all();
foreach ($roles as $r) {
    echo "ID: {$r->id} | Name: {$r->name} | Key: {$r->key}\n";
}

echo "\nPERMISSIONS (Partial Search):\n";
$searchKeys = ['tasks', 'log_volunteer', 'view_own', 'payroll', 'accounts', 'roles'];
$permissions = Permission::where(function($q) use ($searchKeys) {
    foreach ($searchKeys as $k) {
        $q->orWhere('key', 'like', "%$k%");
    }
})->get();

foreach ($permissions as $p) {
    echo "{$p->key}\n";
}
