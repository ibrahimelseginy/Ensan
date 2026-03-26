<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'IbrahimElfil@gmail.com';
$password = 'password';

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "NO USER FOUND FOR $email\n";
    exit(1);
}

echo "User ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "Password Hash (starts with): " . substr($user->password, 0, 10) . "...\n";

if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
    echo "HASH MATCHES!\n";
} else {
    echo "HASH MISMATCH!\n";
    // Reset it again
    $user->password = \Illuminate\Support\Facades\Hash::make($password);
    $user->save();
    echo "Reset password again... New hash starts with: " . substr($user->password, 0, 10) . "...\n";
}

echo "Is Employee: " . ($user->is_employee ? 'Yes' : 'No') . "\n";
echo "Is Active: " . ($user->active ? 'Yes' : 'No') . "\n";

