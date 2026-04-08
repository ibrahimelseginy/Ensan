<?php

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Duplicate Roles ---\n";
$duplicateRoles = DB::table('roles')
    ->select('name', DB::raw('count(*) as count'))
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

foreach ($duplicateRoles as $role) {
    echo "Name: {$role->name} (Count: {$role->count})\n";
    $items = DB::table('roles')->where('name', $role->name)->get();
    foreach ($items as $item) {
        echo "  - ID: {$item->id}, Key: {$item->key}\n";
    }
}

echo "\n--- Duplicate Permissions ---\n";
$duplicatePermissions = DB::table('permissions')
    ->select('name', DB::raw('count(*) as count'))
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

foreach ($duplicatePermissions as $perm) {
    echo "Name: {$perm->name} (Count: {$perm->count})\n";
    $items = DB::table('permissions')->where('name', $perm->name)->get();
    foreach ($items as $item) {
        echo "  - ID: {$item->id}, Key: {$item->key}\n";
    }
}
