<?php

namespace App\Features\WebsiteDonations\Interfaces;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

interface WebsiteDonationProcessorInterface
{
    /**
     * Submit a donation from the public website
     */
    public function submitPublicDonation(array $data): \App\Models\WebDonation;

    /**
     * Verify a pending donation
     */
    public function verifyDonation(\App\Models\WebDonation $donation): bool;

    /**
     * Reject a donation
     */
    public function rejectDonation(\App\Models\WebDonation $donation): bool;

    /**
     * Get website donor statistics/history
     */
    public function getDonorHistory(Donor $donor): array;
}
