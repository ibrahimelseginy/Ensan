<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

$roleKey = 'volunteer';
$role = Role::where('key', $roleKey)->first();

if (!$role) {
    die("Role $roleKey not found!\n");
}

echo "Updating permissions for role: {$role->name} ({$role->key})\n";

// Exact permissions requested by user
$allowedKeys = [
    'volunteer_tasks.view',
    'complaints.create'
];

// Verify permissions exist
$existingPerms = Permission::whereIn('key', $allowedKeys)->get();
$foundKeys = $existingPerms->pluck('key')->toArray();
$missingKeys = array_diff($allowedKeys, $foundKeys);

if (!empty($missingKeys)) {
    echo "Creating missing permissions...\n";
    foreach ($missingKeys as $key) {
        Permission::create([
            'key' => $key,
            'name' => ucwords(str_replace('.', ' ', $key)) // Simple name generation
        ]);
        echo "Created permission: $key\n";
    }
    // Refetch to get IDs
    $existingPerms = Permission::whereIn('key', $allowedKeys)->get();
}

$ids = $existingPerms->pluck('id')->toArray();

// Sync permissions
$role->permissions()->sync($ids);

echo "Successfully synced " . count($ids) . " permissions to Volunteer role.\n";
echo "Permissions:\n";
foreach ($allowedKeys as $k) {
    echo "- $k\n";
}
