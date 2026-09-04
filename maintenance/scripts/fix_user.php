<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'IbrahimElfil@gmail.com';
$password = 'ibrahim';

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    $user = new \App\Models\User();
    $user->name = 'Ibrahim Elfil';
    $user->email = $email;
    echo "Creating new user...\n";
} else {
    echo "Updating existing user...\n";
}

$user->password = \Illuminate\Support\Facades\Hash::make($password);
$user->is_employee = true;
$user->active = true;
$user->save();

$role = \App\Models\Role::firstOrCreate(['key'=>'admin'], ['name'=>'Administrator']);
$user->roles()->syncWithoutDetaching([$role->id]);

echo "User fixed successfully.\n";
echo "Email: " . $user->email . "\n";
echo "Password: " . $password . "\n";
echo "Active: " . ($user->active ? 'Yes' : 'No') . "\n";
echo "Employee: " . ($user->is_employee ? 'Yes' : 'No') . "\n";
