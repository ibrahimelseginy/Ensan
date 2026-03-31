<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('registration_source', 'mobile')->first();
if ($user) {
    $oldHash = $user->password;
    $newPassword = 'newpassword123';
    
    // Simulate the controller update
    $user->update(['password' => Hash::make($newPassword)]);
    
    $newHash = User::find($user->id)->password;
    
    if ($oldHash !== $newHash && Hash::check($newPassword, $newHash)) {
        echo "SUCCESS: Password updated and hashed correctly.";
    } else {
        echo "FAILED: Password not updated correctly.";
    }
} else {
    echo "NO_MOBILE_USER_FOUND";
}
