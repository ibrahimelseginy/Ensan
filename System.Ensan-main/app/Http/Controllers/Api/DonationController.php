<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:campaign,project,general,sadaqa',
            'target_id' => 'required_unless:type,general,sadaqa|integer',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'donor_name' => 'nullable|string|max:255',
            'donor_phone' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        
        // 1. Reconcile Donor
        $donorPhone = $request->donor_phone;
        $donorName = $request->donor_name;

        // If not provided in request, try to get from logged-in user
        if (!$donorPhone && $user) {
            $donorPhone = $user->phone;
            $donorName = $user->name;
        }

        if (!$donorPhone) {
            return response()->json(['message' => 'رقم الهاتف مطلوب'], 422);
        }

        $donor = \App\Models\Donor::firstOrCreate(
            ['phone' => $donorPhone],
            ['name' => $donorName ?? 'متبرع من الموقع', 'type' => 'individual']
        );

        // Fraud Detection: More than 10 donations within a short period (e.g., 1 hour)
        $isFlagged = false;
        if ($user) {
            $recentDonationsCount = Donation::where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->count();
            
            if ($recentDonationsCount >= 10) {
                $isFlagged = true;
            }
        }

        $donation = new Donation();
        $donation->user_id = $user ? $user->id : null;
        $donation->donor_id = $donor->id;
        $donation->source = 'website';
        $donation->amount = $request->amount;
        $donation->payment_method = $request->payment_method;
        $donation->status = 'pending';
        $donation->is_flagged = $isFlagged;
        $donation->type = 'cash'; // Essential for DB enum
        $donation->allocation_note = $request->notes;

        if ($request->type === 'campaign') {
            $donation->donationable_type = Campaign::class;
            $donation->donationable_id = $request->target_id;
            $donation->campaign_id = $request->target_id;
        } elseif ($request->type === 'project') {
            $donation->donationable_type = Project::class;
            $donation->donationable_id = $request->target_id;
            $donation->project_id = $request->target_id;
        } else {
            $donation->donationable_type = $request->type; 
            $donation->donationable_id = 0;
        }

        $donation->save();

        return response()->json([
            'message' => 'تم تسجيل التبرع بنجاح',
            'donation' => $donation
        ], 201);
    }

    public function uploadProof(Request $request)
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB
        ]);

        $path = $request->file('proof')->store('donation_proofs', 'public');

        // Assuming a DonationProof relationship or table exists
        \DB::table('donation_proofs')->insert([
            'donation_id' => $request->donation_id,
            'image_path' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Proof uploaded successfully',
            'path' => $path
        ]);
    }

    public function myDonations(Request $request)
    {
        $user = $request->user();
        
        $internalDonations = $user->donations()->with(['donationable'])->get()->map(function($d) {
            return [
                'id' => $d->id,
                'date' => $d->created_at->format('d/m/Y'),
                'amount' => (float)$d->getValue(),
                'type' => $d->category ?: 'general',
                'payment_method' => $d->payment_method_label,
                'status' => $this->translateStatus($d->status),
                'status_key' => $d->status,
                'donationable' => $d->donationable
            ];
        });

        $webDonations = $user->webDonations()->with(['donationable'])->get()->map(function($d) {
            return [
                'id' => $d->id,
                'date' => $d->created_at->format('d/m/Y'),
                'amount' => (float)$d->amount,
                'type' => $d->category ?: 'general',
                'payment_method' => $d->payment_method_label,
                'status' => $this->translateStatus($d->status),
                'status_key' => $d->status,
                'donationable' => $d->donationable
            ];
        });

        return $internalDonations->concat($webDonations)
            ->sortByDesc('date')
            ->values();
    }

    protected function translateStatus($status)
    {
        $map = [
            'pending' => 'قيد المراجعة',
            'verified' => 'مكتمل',
            'rejected' => 'مرفوض',
            'flagged' => 'تحت المراجعة',
            'active' => 'نشط',
            'cancelled' => 'ملغي'
        ];
        return $map[$status] ?? $status;
    }

    public function publicIndex()
    {
        return Donation::where('status', 'verified')
            ->where('is_flagged', false)
            ->with(['donationable'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($d) {
                return [
                    'id'               => $d->id,
                    'amount'           => $d->amount,
                    'donor_name'       => $d->donor ? $this->anonymizeName($d->donor->name) : 'متبرع فاعل خير',
                    'category'         => $d->category,
                    'donationable_name'=> $d->donationable ? ($d->donationable->name ?? $d->donationable->title) : null,
                    'date'             => $d->created_at->diffForHumans(),
                ];
            });
    }

    protected function anonymizeName($name)
    {
        if (!$name || $name === 'متبرع من الموقع') return 'متبرع فاعل خير';
        $parts = explode(' ', trim($name));
        if (count($parts) > 1) {
            return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '...';
        }
        return mb_substr($parts[0], 0, 1) . '...';
    }
}
