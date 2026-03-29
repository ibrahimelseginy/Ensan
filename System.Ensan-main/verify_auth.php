<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

$controller = new AuthController();

echo "--- Testing Login by Phone ---\n";
$phone = '01234567890';
$request = Request::create('/api/v1/mobile/auth/login-phone', 'POST', ['phone' => $phone]);
$response = $controller->loginByPhone($request);
echo "Response: " . $response->getContent() . "\n";

$user = User::where('phone', $phone)->first();
if ($user) {
    echo "User found: {$user->name}, Role: {$user->role}, Source: {$user->registration_source}\n";
    $otp = $user->otp_code;
    echo "Generated OTP: {$otp}\n";

    echo "\n--- Testing Verify OTP ---\n";
    $verifyRequest = Request::create('/api/v1/mobile/auth/verify-otp', 'POST', [
        'phone' => $phone,
        'otp' => $otp
    ]);
    $verifyResponse = $controller->verifyOtp($verifyRequest);
    echo "Verify Response: " . $verifyResponse->getContent() . "\n";
}

echo "\n--- Testing Manual Register ---\n";
$email = 'mobile_test_' . time() . '@example.com';
$registerRequest = Request::create('/api/v1/mobile/auth/register', 'POST', [
    'name' => 'Mobile Test User',
    'email' => $email,
    'password' => 'password123'
]);
$registerResponse = $controller->register($registerRequest);
echo "Register Response: " . $registerResponse->getContent() . "\n";

$newUser = User::where('email', $email)->first();
if ($newUser) {
    echo "New User: {$newUser->name}, Source: {$newUser->registration_source}, Role: {$newUser->role}\n";
    $hasDonorRole = $newUser->roles()->where('key', 'donor')->exists();
    echo "Has 'donor' Role in pivot table: " . ($hasDonorRole ? 'Yes' : 'No') . "\n";
}
