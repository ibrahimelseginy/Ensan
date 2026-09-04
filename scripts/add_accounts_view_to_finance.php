<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;

$roleKey = 'finance';
$role = Role::where('key', $roleKey)->first();

if (!$role) {
    die("Role $roleKey not found!\n");
}

echo "Updating permissions for role: {$role->name} ({$role->key})\n";

// Add accounts.view to existing permissions
$permissionKey = 'accounts.view';

// Ensure permission exists
$perm = Permission::firstOrCreate(['key' => $permissionKey], ['name' => 'عرض دليل الحسابات']);

if (!$role->permissions->contains('key', $permissionKey)) {
    $role->permissions()->attach($perm->id);
    echo "Added permission: $permissionKey\n";
} else {
    echo "Permission $permissionKey already exists for this role.\n";
}

echo "Current permissions for {$role->name}:\n";
foreach ($role->permissions as $p) {
    echo "- {$p->key} ({$p->name})\n";
}
