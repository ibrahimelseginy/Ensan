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

// List of allowed permissions keys based on user screenshots (20 permissions)
$allowedKeys = [
    // FINANCIAL_CLOSURES
    'financial_closures.create',
    'financial_closures.delete',
    'financial_closures.edit',
    'financial_closures.view',
    
    // PAYROLLS
    'payrolls.create',
    'payrolls.delete',
    'payrolls.edit',
    'payrolls.view',
    
    // EXPENSES
    'expenses.create',
    'expenses.delete',
    'expenses.edit',
    'expenses.manage',
    'expenses.view',
    
    // FINANCE
    'finance.view',
    
    // COMPLAINTS
    'complaints.create',
    
    // INVENTORY_TRANSACTIONS
    'inventory_transactions.view',
    
    // JOURNAL_ENTRIES
    'journal_entries.create',
    'journal_entries.delete',
    'journal_entries.edit',
    'journal_entries.view',
];

// Fetch IDs
$permissionIds = Permission::whereIn('key', $allowedKeys)->pluck('id')->toArray();

// Sync
$role->permissions()->sync($permissionIds);

echo "Synced " . count($permissionIds) . " permissions to Finance role.\n";
echo "Allowed keys count: " . count($allowedKeys) . "\n";

if (count($permissionIds) < count($allowedKeys)) {
    echo "WARNING: Some permissions were not found in the database. Missing keys:\n";
    $foundKeys = Permission::whereIn('key', $allowedKeys)->pluck('key')->toArray();
    $missing = array_diff($allowedKeys, $foundKeys);
    print_r($missing);
}
