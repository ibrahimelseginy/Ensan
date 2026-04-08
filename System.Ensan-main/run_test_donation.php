<?php

use Illuminate\Contracts\Console\Kernel;
use App\Features\WebsiteDonations\Services\WebsiteDonationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$imagePath = 'C:\Users\HP\.gemini\antigravity\brain\042139f9-068a-4e6d-be37-8c4318f89cd8\test_donation_proof_1773582309265.png';
$tempPath = __DIR__ . '/test_proof.png';
copy($imagePath, $tempPath);

$file = new UploadedFile(
    $tempPath,
    'test_proof.png',
    'image/png',
    null,
    true
);

$data = [
    'donor_name' => 'Test Donor Website',
    'donor_phone' => '01012345678',
    'amount' => 500.00,
    'type' => 'campaign',
    'target_id' => 5, // Campaign ID from previous step
    'payment_method' => 'InstaPay',
    'notes' => 'Checking end-to-end donation flow from website.',
    'proof_file' => $file
];

echo "Simulating donation submission...\n";

$service = app(WebsiteDonationService::class);
$donation = $service->submitPublicDonation($data);

echo "Donation created! ID: " . $donation->id . "\n";
echo "Status: " . $donation->status . "\n";
echo "Source: " . $donation->source . "\n";

$donation->load('proof');
if ($donation->proof) {
    echo "Proof link saved: " . $donation->proof->image_path . "\n";
    if (Storage::disk('public')->exists($donation->proof->image_path)) {
        echo "File confirmed on disk: storage/app/public/" . $donation->proof->image_path . "\n";
    } else {
        echo "ERROR: File NOT found on disk!\n";
    }
} else {
    echo "ERROR: Proof link NOT saved to database!\n";
}

@unlink($tempPath);
