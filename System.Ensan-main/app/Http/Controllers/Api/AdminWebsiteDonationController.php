<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Http\Request;

class AdminWebsiteDonationController extends Controller
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
        $donors = Donor::whereHas('donations', function ($query) {
            $query->where('source', 'website');
        })
        ->withCount(['donations' => function ($query) {
            $query->where('source', 'website');
        }])
        ->withSum(['donations' => function ($query) {
            $query->where('source', 'website')->where('status', 'verified');
        }], 'amount')
        ->latest()
        ->paginate(20);

        return response()->json($donors);
    }

    /**
     * Show donation history for a specific website donor
     */
    public function donorHistory(Donor $donor)
    {
        $result = $this->processor->getDonorHistory($donor);
        
        $history = collect($result['history'])->map(function ($donation) {
            return [
                'id' => $donation->id,
                'date' => $donation->created_at->format('Y-m-d H:i'),
                'amount' => $donation->amount,
                'type' => $this->getDonationTypeLabel($donation),
                'payment_method' => $donation->payment_method,
                'status' => $donation->status,
                'proof_url' => $donation->proof ? $donation->proof->image_url : null,
                'notes' => $donation->allocation_note
            ];
        });

        return response()->json([
            'donor' => [
                'id' => $donor->id,
                'name' => $donor->name,
                'phone' => $donor->phone,
                'email' => $donor->email,
                'donations_count' => $result['donor']->donations_count
            ],
            'history' => $history
        ]);
    }

    /**
     * Update donor information
     */
    public function updateDonor(Request $request, Donor $donor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:donors,phone,' . $donor->id,
        ]);

        $donor->update($request->only(['name', 'phone']));

        return response()->json([
            'message' => 'Donor updated successfully',
            'donor' => $donor
        ]);
    }

    /**
     * Verify a specific donation
     */
    public function verifyDonation(Request $request, Donation $donation)
    {
        if ($this->processor->verifyDonation($donation)) {
            return response()->json(['message' => 'Donation verified successfully']);
        }

        return response()->json(['message' => 'Donation verification failed or not applicable'], 422);
    }

    /**
     * Reject a specific donation
     */
    public function rejectDonation(Request $request, Donation $donation)
    {
        if ($this->processor->rejectDonation($donation)) {
            return response()->json(['message' => 'Donation rejected successfully']);
        }

        return response()->json(['message' => 'Donation rejection failed'], 422);
    }

    /**
     * List all donations from the website
     */
    public function allDonations()
    {
        $donations = Donation::where('source', 'website')
            ->with(['donor', 'donationable', 'proof'])
            ->latest()
            ->paginate(20);

        $transformed = collect($donations->items())->map(function ($donation) {
            return [
                'id' => $donation->id,
                'date' => $donation->created_at->format('Y-m-d H:i'),
                'amount' => $donation->amount,
                'donor_name' => $donation->donor->name ?? 'غير معروف',
                'donor_phone' => $donation->donor->phone ?? '',
                'type' => $this->getDonationTypeLabel($donation),
                'payment_method' => $donation->payment_method,
                'status' => $donation->status,
                'proof_url' => $donation->proof ? $donation->proof->image_url : null,
                'notes' => $donation->allocation_note
            ];
        });

        return response()->json([
            'data' => $transformed,
            'total' => $donations->total(),
            'current_page' => $donations->currentPage(),
            'last_page' => $donations->lastPage(),
        ]);
    }

    private function getDonationTypeLabel($donation)
    {
        if ($donation->donationable_type === 'App\Models\Campaign') {
            return 'حملة: ' . ($donation->donationable->name ?? 'غير معروف');
        } elseif ($donation->donationable_type === 'App\Models\Project') {
            return 'مشروع: ' . ($donation->donationable->name ?? 'غير معروف');
        }
        return $donation->donationable_type === 'sadaqa' ? 'صدقة جارية' : 'تبرع عام';
    }
}
