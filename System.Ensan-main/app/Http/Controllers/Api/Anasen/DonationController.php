<?php

namespace App\Http\Controllers\Api\Anasen;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationProof;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    /**
     * POST /api/donations
     * Step 1: Create a donation record
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'category_id' => 'required|exists:donation_categories,id',
            'target_id' => 'nullable|integer',
            'payment_method' => 'required|string|in:instapay,vodafone,representative',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Resolve category from ID
            $categoryRecord = \App\Models\DonationCategory::find($request->category_id);
            $categorySlug = $categoryRecord->slug;

            // Strict Validation for target_id based on category
            if (in_array($categorySlug, ['campaign', 'project']) && !$request->target_id) {
                return response()->json([
                    'success' => false,
                    'errors' => ['target_id' => ['The target_id field is required for the selected category.']],
                ], 422);
            }

            // Find or create a matching Donor record
            $donor = Donor::where('phone', $user->phone)->first();
            if (!$donor) {
                $donor = Donor::create([
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => 'active',
                ]);
            }

            $donationData = [
                'user_id' => $user->id,
                'donor_id' => $donor->id,
                'type' => 'cash',
                'amount' => $request->amount,
                'category' => $categorySlug, // Use resolved slug internally
                'target_id' => $request->target_id ?? 0,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'source' => 'mobile',
            ];

            // Map category to donationable type
            if ($categorySlug === 'campaign' && $request->target_id) {
                $donationData['donationable_type'] = Campaign::class;
                $donationData['donationable_id'] = $request->target_id;
                $donationData['campaign_id'] = $request->target_id;
            } elseif ($categorySlug === 'project' && $request->target_id) {
                $donationData['donationable_type'] = Project::class;
                $donationData['donationable_id'] = $request->target_id;
                $donationData['project_id'] = $request->target_id;
            } else {
                $donationData['donationable_type'] = null;
                $donationData['donationable_id'] = null;

                if ($categorySlug === 'kafala') {
                    $donationData['allocation_note'] = "sponsorship=orphan";
                }
            }

            $donation = Donation::create($donationData);

            Log::info('Donation created', ['donation_id' => $donation->id, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'donation_id' => $donation->id,
                'status' => 'pending',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Donation creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create donation. Please try again.',
            ], 500);
        }
    }

    /**
     * POST /api/donations/upload-proof
     * Step 2: Upload payment proof image
     */
    public function uploadProof(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $donation = Donation::find($request->donation_id);

            // Security check: Ensure the donation belongs to the authenticated user
            if ($donation->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('anasen/donations/proofs', 'public');
                
                DonationProof::create([
                    'donation_id' => $donation->id,
                    'image_path' => $path,
                ]);

                Log::info('Donation proof uploaded', ['donation_id' => $donation->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment proof uploaded successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image provided',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Proof upload failed', [
                'error' => $e->getMessage(),
                'donation_id' => $request->donation_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload proof. Please try again.',
            ], 500);
        }
    }
}
