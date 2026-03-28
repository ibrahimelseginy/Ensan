<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

final class AdminDonationController extends Controller
{
    public function index()
    {
        return Donation::with(['user', 'donationable'])->latest()->paginate(20);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
        ]);

        $donation = Donation::findOrFail($request->donation_id);
        $donation->status = 'verified';
        $donation->save();

        // If it's a campaign or project, we might need to update the raised_amount
        if ($donation->donationable) {
            $target = $donation->donationable;
            if (isset($target->current_amount)) {
                $target->current_amount += $donation->amount;
                $target->save();
            }
        }

        return response()->json(['message' => 'Donation verified successfully']);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'reason' => 'nullable|string'
        ]);

        $donation = Donation::findOrFail($request->donation_id);
        $donation->status = 'rejected';
        $donation->save();

        return response()->json(['message' => 'Donation rejected successfully']);
    }
}
