<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;

$roleKey = 'HR';
$role = Role::where('key', $roleKey)->first();

if (!$role) {
    die("Role $roleKey not found!\n");
}

echo "Updating permissions for role: {$role->name} ({$role->key})\n";

// List of allowed permissions keys based on user screenshots
$allowedKeys = [
    // TASKS
    'tasks.create', 'tasks.delete', 'tasks.edit', 'tasks.view',
    
    // PAYROLLS
    'payrolls.create', 'payrolls.delete', 'payrolls.edit', 'payrolls.view',
    
    // USERS
    'users.create', 'users.delete', 'users.edit', 'users.view',
    
    // EMPLOYEE_ATTENDANCE
    'employee_attendance.create', 'employee_attendance.edit', 'employee_attendance.view',
    
    // ACCOUNTS
    'accounts.create', 'accounts.delete', 'accounts.edit', 'accounts.view',
    
    // COMPLAINTS
    'complaints.view', 'complaints.edit',
    
    // LOG_VOLUNTEER_HOURS
    'log_volunteer_hours',
    
    // EVALUATIONS
    'evaluations.create', 'evaluations.edit', 'evaluations.view',
    
    // EMPLOYEE_TASKS
    'employee_tasks.create', 'employee_tasks.delete', 'employee_tasks.edit', 'employee_tasks.view',
    
    // MANAGE VOLUNTEERS HR
    'manage_volunteers_hr',
    
    // MANAGE PAYROLLS
    'manage_payrolls',
    
    // MANAGE EMPLOYEES
    'manage_employees',
    
    // REPORTS
    'reports.view',
    
    // PAYROLL
    'payroll.manage',
    
    // NOTIFICATIONS
    'notifications.create', 'notifications.view',
    
    // VOLUNTEER ATTENDANCE
    'volunteer_attendance.create', 'volunteer_attendance.edit', 'volunteer_attendance.view',
    
    // VIEW OWN TASKS
    'view_own_tasks',
    
    // ROLES
    'roles.create', 'roles.delete', 'roles.edit', 'roles.manage', 'roles.view',
    
    // VOLUNTEER TASKS
    'volunteer_tasks.create', 'volunteer_tasks.delete', 'volunteer_tasks.edit', 'volunteer_tasks.view',
    
    // VOLUNTEER HOURS
    'volunteer_hours.create', 'volunteer_hours.edit', 'volunteer_hours.view'
];

// Fetch IDs
$permissionIds = Permission::whereIn('key', $allowedKeys)->pluck('id')->toArray();

// Sync
$role->permissions()->sync($permissionIds);

echo "Synced " . count($permissionIds) . " permissions to HR role.\n";
echo "Allowed keys count: " . count($allowedKeys) . "\n";

if (count($permissionIds) < count($allowedKeys)) {
    echo "WARNING: Some permissions were not found in the database. Missing keys:\n";
    $foundKeys = Permission::whereIn('key', $allowedKeys)->pluck('key')->toArray();
    $missing = array_diff($allowedKeys, $foundKeys);
    print_r($missing);
}
