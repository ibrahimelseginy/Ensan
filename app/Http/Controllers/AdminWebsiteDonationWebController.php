<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

final class AdminWebsiteDonationWebController extends Controller
{
    protected $processor;

    public function __construct(\App\Features\WebsiteDonations\Interfaces\WebsiteDonationProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * List unique donors who have donated via the website
     */
    public function index()
    {
        $donors = Donor::whereHas('webDonations')
            ->withCount('webDonations')
            ->withSum(['webDonations as web_donations_sum_amount' => function ($query) {
                $query->where('status', 'verified');
            }], 'amount')
            ->latest()
            ->paginate(20);

        return view('website.donations.index', compact('donors'));
    }

    /**
     * Show donation history for a specific website donor
     */
    public function show(Donor $donor)
    {
        $result = $this->processor->getDonorHistory($donor);
        
        return view('website.donations.show', [
            'donor' => $result['donor'],
            'history' => $result['history']
        ]);
    }

    /**
     * Verify a specific donation from the web interface
     */
    public function verifyDonation(\App\Models\WebDonation $web_donation)
    {
        if ($this->processor->verifyDonation($web_donation)) {
            return back()->with('success', 'تم تأكيد التبرع بنجاح');
        }

        return back()->with('error', 'فشل في تأكيد التبرع');
    }

    /**
     * Reject a specific donation from the web interface
     */
    public function rejectDonation(Request $request, \App\Models\WebDonation $web_donation)
    {
        if ($this->processor->rejectDonation($web_donation)) {
            return back()->with('success', 'تم رفض التبرع بنجاح');
        }

        return back()->with('error', 'فشل في رفض التبرع');
    }
}
