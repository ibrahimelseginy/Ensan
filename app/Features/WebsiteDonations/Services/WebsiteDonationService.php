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
            if (isset($data['metadata'])) {
                $donation->metadata = $data['metadata'];
            }
            
            // Resolve category from ID
            $categoryRecord = \App\Models\DonationCategory::findOrFail($data['category_id']);
            $type = $categoryRecord->slug;
            
            $donation->category = $type;
            $donation->target_id = $data['target_id'] ?? 0;

            $this->applyDonationable($donation, $type, $data['target_id'] ?? 0);
            
            $donation->save();

            // 3. Handle Proof of Payment
            if (isset($data['proof_file'])) {
                $path = app(\App\Services\PrivateDocumentService::class)->store($data['proof_file'], 'website/donations/proofs');
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
        return DB::transaction(function () use ($donation) {
            $locked = \App\Models\WebDonation::query()->lockForUpdate()->findOrFail($donation->id);
            if ($locked->status !== 'pending') {
                return false;
            }
            $target = $locked->donationable;
            if ($target && array_key_exists('current_amount', $target->getAttributes())) {
                $target->increment('current_amount', $locked->amount);
            }
            $locked->update(['status' => 'verified']);
            $donation->setRawAttributes($locked->getAttributes(), true);
            return true;
        });
    }

    /** Rejection is only valid before approval; approved funds need a reversal. */
    public function rejectDonation(\App\Models\WebDonation $donation): bool
    {
        return DB::transaction(function () use ($donation) {
            $locked = \App\Models\WebDonation::query()->lockForUpdate()->findOrFail($donation->id);
            if ($locked->status !== 'pending') {
                return false;
            }
            $locked->update(['status' => 'rejected']);
            $donation->setRawAttributes($locked->getAttributes(), true);
            return true;
        });
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
