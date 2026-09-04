<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'IbrahimElfil@gmail.com')->first();

if ($user) {
    $user->password = Hash::make('IbrahimElfil');
    $user->save();
    echo "Password updated successfully for IbrahimElfil@gmail.com\n";
} else {
    // Create the user if doesn't exist
    User::create([
        'name' => 'Ibrahim El-Fil',
        'email' => 'IbrahimElfil@gmail.com',
        'password' => Hash::make('IbrahimElfil'),
        'active' => true,
    ]);
    echo "User created and password set for IbrahimElfil@gmail.com\n";
}
