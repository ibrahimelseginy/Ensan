<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Anasen;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

final class AdminDonationController extends Controller
{
    /**
     * GET /api/admin/donations
     * List donations with status pending
     */
    public function index()
    {
        try {
            $donations = Donation::select([
                                    'id', 'user_id', 'donor_id', 'amount', 
                                    'payment_method', 'type', 'status', 
                                    'category', 'target_id', 'source', 'created_at'
                                ])
                                ->with(['user:id,name,phone', 'donor:id,name', 'proof', 'project:id,name', 'campaign:id,name'])
                                ->latest()
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $donations
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list pending donations', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve donations'
            ], 500);
        }
    }

    /**
     * POST /api/admin/donations/verify
     * Mark donation as verified
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donation = Donation::find($request->donation_id);
            $donation->status = 'verified';
            $donation->save();

            if ($donation->proof) {
                $donation->proof->verified_by = auth()->id();
                $donation->proof->verified_at = Carbon::now();
                $donation->proof->save();
            }

            Log::info('Donation verified by admin', [
                'donation_id' => $donation->id,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donation verified successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Donation verification failed', [
                'error' => $e->getMessage(),
                'donation_id' => $request->donation_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Verification failed'
            ], 500);
        }
    }

    /**
     * POST /api/admin/donations/reject
     * Reject donation
     */
    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donation = Donation::find($request->donation_id);
            $donation->status = 'rejected';
            $donation->cancellation_reason = $request->reason;
            $donation->cancelled_at = Carbon::now();
            $donation->cancelled_by = auth()->id();
            $donation->save();

            Log::info('Donation rejected by admin', [
                'donation_id' => $donation->id,
                'admin_id' => auth()->id(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donation rejected successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Donation rejection failed', [
                'error' => $e->getMessage(),
                'donation_id' => $request->donation_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Rejection failed'
            ], 500);
        }
    }
}
