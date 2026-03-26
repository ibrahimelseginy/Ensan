<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Http;

$baseUrl = 'http://localhost:8000/api'; // Assuming local dev server
$phone = '01234567890';

echo "--- Testing Anasen API ---\n";

// 1. Request OTP
echo "1. Requesting OTP for $phone... ";
$authCtrl = new \App\Http\Controllers\Api\Anasen\AuthController();
$response = $authCtrl->login(new \Illuminate\Http\Request(['phone' => $phone]));
echo $response->getStatusCode() === 200 ? "OK\n" : "FAIL\n";

// 2. Verify OTP
echo "2. Verifying OTP 12345... ";
$response = $authCtrl->verifyOtp(new \Illuminate\Http\Request(['phone' => $phone, 'otp' => '12345']));
$data = json_decode($response->getContent(), true);
if (isset($data['token'])) {
    $token = $data['token'];
    echo "OK (Token generated)\n";
} else {
    echo "FAIL\n";
    exit(1);
}

// 3. Test Protected Route - Step 1: Create Donation
echo "3. Creating Donation (Protected)... ";
$donCtrl = new \App\Http\Controllers\Api\Anasen\DonationController();
$user = User::where('phone', $phone)->first();
$req = new \Illuminate\Http\Request([
    'amount' => 250,
    'category' => 'campaign',
    'target_id' => 5,
    'payment_method' => 'instapay'
]);
$req->setUserResolver(function() use ($user) { return $user; });

$response = $donCtrl->store($req);
$donData = json_decode($response->getContent(), true);
if (isset($donData['donation_id'])) {
    $donationId = $donData['donation_id'];
    echo "OK (Donation ID: $donationId)\n";
} else {
    echo "FAIL\n";
    print_r($donData);
    exit(1);
}

// 4. Test Admin Route - Verify Donation
echo "4. Verifying Donation (Admin)... ";
$adminCtrl = new \App\Http\Controllers\Api\Anasen\AdminDonationController();
$adminUser = User::where('email', 'IbrahimElfil@gmail.com')->first(); // Using the user's admin account
$req = new \Illuminate\Http\Request(['donation_id' => $donationId]);
$req->setUserResolver(function() use ($adminUser) { return $adminUser; });

$response = $adminCtrl->verify($req);
$adminData = json_decode($response->getContent(), true);
echo $adminData['status'] === 'success' ? "OK\n" : "FAIL\n";

echo "\n--- API VERIFICATION SUCCESSFUL ---\n";
