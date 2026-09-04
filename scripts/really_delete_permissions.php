<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

echo "Starting cleanup...\n";

// 1. Delete Permissions
$permKeys = ['manage_it', 'manage_media'];
foreach ($permKeys as $key) {
    $deleted = DB::table('permissions')->where('key', $key)->delete();
    if ($deleted) {
        echo "Deleted permission: $key\n";
    } else {
        echo "Permission $key not found in DB.\n";
    }
}

// 2. Delete Roles (optional but likely intended if 'administrations' are being removed)
$roleKeys = ['it_manager', 'media_manager'];
foreach ($roleKeys as $key) {
    $deleted = DB::table('roles')->where('key', $key)->delete();
    if ($deleted) {
        echo "Deleted role: $key\n";
    } else {
        echo "Role $key not found in DB.\n";
    }
}

// 3. Double Check for any lingering references
$checkPerms = DB::table('permissions')->whereIn('key', $permKeys)->count();
$checkRoles = DB::table('roles')->whereIn('key', $roleKeys)->count();

if ($checkPerms == 0 && $checkRoles == 0) {
    echo "SUCCESS: All target permissions and roles are gone.\n";
} else {
    echo "WARNING: Some items might still exist.\n";
}
