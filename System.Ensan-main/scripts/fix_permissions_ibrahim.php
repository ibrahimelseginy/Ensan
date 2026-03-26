<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Logic
$user = \App\Models\User::where('name', 'LIKE', '%ابراهيم%')->first();

if (!$user) {
    echo "User 'Ibrahim' not found.\n";
    exit(1);
}

echo "Found user: {$user->name} (ID: {$user->id})\n";

// Find or Create HR Role
$hr = \App\Models\Role::firstOrCreate(['key' => 'hr'], ['name' => 'الموارد البشرية']);
echo "Role 'hr' ready (ID: {$hr->id})\n";

// Ensure Permissions exist and are attached to HR
$perms = ['hr.evaluations', 'roles.view'];
foreach ($perms as $key) {
    $p = \App\Models\Permission::firstOrCreate(['key' => $key], ['name' => $key]);
    if (!$hr->permissions->contains('id', $p->id)) {
        $hr->permissions()->attach($p->id);
        echo "Attached permission '$key' to HR role.\n";
    } else {
        echo "Permission '$key' already attached to HR role.\n";
    }
}

// Assign HR role to User
if (!$user->roles->contains('id', $hr->id)) {
    $user->roles()->attach($hr->id);
    echo "Attached 'hr' role to user.\n";
} else {
    echo "User already has 'hr' role.\n";
}

// Verify
$user->refresh();
echo "\nVerification:\n";
foreach ($perms as $key) {
    echo "$key: " . ($user->hasPermission($key) ? "YES" : "NO") . "\n";
}
