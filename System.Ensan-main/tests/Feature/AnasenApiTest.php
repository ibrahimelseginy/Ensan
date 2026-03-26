<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\Donation;
use App\Models\WebDonor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AnasenApiTest extends TestCase
{
    // Note: RefreshDatabase might be destructive if not configured correctly for local dev.
    // Using simple tests without refresh for now to avoid wiping the user's data.

    public function test_anasen_auth_login_and_verify_store_real_name_for_new_user()
    {
        $phone = '015' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT) . '101';
        $name = 'Ahmed Real Name';

        $loginResponse = $this->postJson('/api/auth/login', [
            'phone' => $phone,
            'name' => $name,
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $verifyResponse = $this->postJson('/api/auth/verify-otp', [
            'phone' => $phone,
            'otp' => '12345',
            'name' => $name,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.name', $name);

        $this->assertDatabaseHas('web_donors', [
            'phone' => $phone,
            'name' => $name,
        ]);
    }

    public function test_anasen_verify_otp_updates_existing_default_name()
    {
        $phone = '015' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT) . '202';
        $realName = 'Updated Real Name';

        $donor = WebDonor::create([
            'name' => 'Donor ' . substr($phone, -4),
            'phone' => $phone,
            'email' => $phone . '@anasen.tmp',
            'password' => bcrypt('secret'),
            'otp_code' => '12345',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'active' => true,
        ]);

        $verifyResponse = $this->postJson('/api/auth/verify-otp', [
            'phone' => $phone,
            'otp' => '12345',
            'name' => $realName,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.id', $donor->id)
            ->assertJsonPath('user.name', $realName);

        $this->assertDatabaseHas('web_donors', [
            'id' => $donor->id,
            'name' => $realName,
        ]);
    }
    
    public function test_phone_login_flow()
    {
        $phone = '01234567890';
        
        // 1. Request OTP
        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => $phone
        ]);
        
        $response->assertStatus(200)
                 ->assertJson(['message' => 'OTP sent successfully']);
        
        // 2. Verify OTP
        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'phone' => $phone,
            'otp' => '12345'
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user']);
                 
        return $response->json('token');
    }

    public function test_polymorphic_donation_creation()
    {
        $token = $this->test_phone_login_flow();
        
        // Create a dummy campaign
        $campaign = Campaign::first() ?: Campaign::create(['name' => 'Test Campaign', 'goal_amount' => 1000]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/donations', [
                'type' => 'campaign',
                'target_id' => $campaign->id,
                'amount' => 500,
                'payment_method' => 'instapay'
            ]);

        $response->assertStatus(201)
                 ->assertJsonPath('donation.donationable_type', Campaign::class)
                 ->assertJsonPath('donation.amount', 500);
    }

    public function test_fraud_detection()
    {
        $token = $this->test_phone_login_flow();
        
        // Submit 11 donations quickly
        for ($i = 0; $i < 11; $i++) {
            $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                ->postJson('/api/v1/donations', [
                    'type' => 'general',
                    'amount' => 10,
                    'payment_method' => 'vodafone'
                ]);
        }

        // The 11th donation should be flagged
        $response->assertStatus(201)
                 ->assertJsonPath('donation.is_flagged', true);
    }

    public function test_proof_upload()
    {
        Storage::fake('public');
        $token = $this->test_phone_login_flow();
        
        $donation = Donation::latest()->first();
        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/donations/upload-proof', [
                'donation_id' => $donation->id,
                'proof' => $file
            ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('donation_proofs/' . $file->hashName());
    }
}
