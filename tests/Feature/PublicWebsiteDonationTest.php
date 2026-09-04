<?php

namespace Tests\Feature;

use App\Features\WebsiteDonations\Interfaces\WebsiteDonationProcessorInterface;
use App\Models\DonationProof;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicWebsiteDonationTest extends TestCase
{
    public function test_public_website_donation_proof_is_linked_to_web_donation(): void
    {
        Storage::fake('public');

        DB::beginTransaction();

        try {
            $service = app(WebsiteDonationProcessorInterface::class);
            $phone = '011' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT) . '501';

            $donation = $service->submitPublicDonation([
                'donor_name' => 'Website Donor',
                'donor_phone' => $phone,
                'amount' => 150,
                'type' => 'general',
                'payment_method' => 'instapay',
                'notes' => 'Test website donation proof flow',
                'proof_file' => UploadedFile::fake()->image('proof.jpg'),
            ]);

            $proof = DonationProof::where('web_donation_id', $donation->id)->latest('id')->first();

            $this->assertNotNull($proof);
            $this->assertNull($proof->donation_id);
            $this->assertSame($donation->id, $proof->web_donation_id);
            Storage::disk('public')->assertExists($proof->image_path);
        } finally {
            DB::rollBack();
        }
    }
}
