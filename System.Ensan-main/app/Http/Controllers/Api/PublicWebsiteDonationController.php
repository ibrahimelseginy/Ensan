<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\DonationProof;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class PublicWebsiteDonationController extends Controller
{
    protected $processor;

    public function __construct(\App\Features\WebsiteDonations\Interfaces\WebsiteDonationProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Submit a donation from the website (guest)
     */
    public function submit(Request $request)
    {
        $isRepresentative = $request->input('payment_method') === 'representative';

        $validator = Validator::make($request->all(), [
            'donor_name'     => 'required|string|max:255',
            'donor_phone'    => 'required|string|max:20',
            'amount'         => 'required|numeric|min:1',
            'category_id'    => 'required|exists:donation_categories,id',
            'target_id'      => 'nullable|integer',
            'payment_method' => 'required|string',
            'proof'          => $isRepresentative ? 'nullable' : 'required|image|max:5120',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Strict Validation for target_id based on category
        $categoryRecord = \App\Models\DonationCategory::find($request->category_id);
        if (in_array($categoryRecord->slug, ['campaign', 'project']) && !$request->target_id) {
            return response()->json([
                'status' => 'error',
                'errors' => ['target_id' => ['The target_id field is required for the selected category.']],
            ], 422);
        }

        try {
            $data = $request->only(['donor_name', 'donor_phone', 'amount', 'category_id', 'target_id', 'payment_method', 'notes']);
            if ($request->hasFile('proof')) {
                $data['proof_file'] = $request->file('proof');
            }

            // Link with authenticated user if present (resolved by AnasenApiAuth middleware)
            $user = $request->user();
            if ($user && $user instanceof \App\Models\WebDonor) {
                $data['web_donor_id'] = $user->id;
            }

            $donation = $this->processor->submitPublicDonation($data);

            return response()->json([
                'status' => 'success',
                'message' => 'تم استلام طلب التبرع بنجاح، بانتظار المراجعة.',
                'donation_id' => $donation->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage()
            ], 500);
        }
    }
}
