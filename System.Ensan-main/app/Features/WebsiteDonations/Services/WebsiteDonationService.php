<?php

namespace App\Features\WebsiteDonations\Services;

use App\Features\WebsiteDonations\Interfaces\WebsiteDonationProcessorInterface;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\DonationProof;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WebsiteDonationService implements WebsiteDonationProcessorInterface
{
    /**
     * Submit a donation from the public website
     */
    public function submitPublicDonation(array $data): \App\Models\WebDonation
    {
        return DB::transaction(function () use ($data) {
            // 1. Reconcile Donor
            $donor = Donor::firstOrCreate(
                ['phone' => $data['donor_phone']],
                ['name' => $data['donor_name'], 'type' => 'individual']
            );

            // 2. Map and Create Web Donation
            $donation = new \App\Models\WebDonation();
            $donation->donor_id = $donor->id;
            
            // Link with WebDonor account if authenticated
            if (auth('web_donor')->check()) {
                $donation->web_donor_id = auth('web_donor')->id();
            }

            $donation->amount = $data['amount'];
            $donation->payment_method = $data['payment_method'];
            $donation->status = 'pending';
            $donation->allocation_note = $data['notes'] ?? null;
            
            // Resolve category from ID
            $categoryRecord = \App\Models\DonationCategory::find($data['category_id']);
            $type = $categoryRecord->slug;
            
            $donation->category = $type;
            $donation->target_id = $data['target_id'] ?? 0;

            $this->applyDonationable($donation, $type, $data['target_id'] ?? 0);
            
            $donation->save();

            // 3. Handle Proof of Payment
            if (isset($data['proof_file'])) {
                $path = $data['proof_file']->store('website/donations/proofs', 'public');
                DonationProof::create([
                    'donation_id' => null,
                    'web_donation_id' => $donation->id,
                    'image_path' => $path
                ]);
            }

            return $donation;
        });
    }

    /**
     * Map donationable types based on selection
     */
    protected function applyDonationable(\App\Models\WebDonation $donation, string $type, int $targetId): void
    {
        if ($type === 'campaign') {
            $donation->donationable_type = Campaign::class;
            $donation->donationable_id = $targetId;
            $donation->campaign_id = $targetId;
        } elseif ($type === 'project') {
            $donation->donationable_type = Project::class;
            $donation->donationable_id = $targetId;
            $donation->project_id = $targetId;
        } else {
            $donation->donationable_type = null;
            $donation->donationable_id = 0;
        }
    }

    /**
     * Verify a pending donation
     */
    public function verifyDonation(\App\Models\WebDonation $donation): bool
    {
        if ($donation->status !== 'pending') {
            return false;
        }

        $donation->status = 'verified';
        $donation->save();

        // Update target amount if applicable
        if ($donation->donationable_type && $donation->donationable_id && $donation->donationable) {
            $target = $donation->donationable;
            if (isset($target->current_amount)) {
                $target->current_amount += $donation->amount;
                $target->save();
            }
        }

        return true;
    }

    /**
     * Reject a donation
     */
    public function rejectDonation(\App\Models\WebDonation $donation): bool
    {
        $donation->status = 'rejected';
        return $donation->save();
    }

    /**
     * Get website donor statistics/history
     */
    public function getDonorHistory(Donor $donor): array
    {
        $history = \App\Models\WebDonation::where('donor_id', $donor->id)
            ->with(['donationable', 'proof'])
            ->latest()
            ->get();

        return [
            'donor' => $donor,
            'history' => $history
        ];
    }
}
