<?php
namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        return Donation::with(['donor','project','campaign','warehouse','delegate','route'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'donor_id' => 'nullable|exists:donors,id',
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:cash,in_kind,campaign,project,general,sadaqa',
            'target_id' => 'nullable|integer',
            'cash_channel' => 'nullable|in:cash,instapay,vodafone_cash,delegate,hq',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'received_at' => 'nullable|date'
        ]);

        $user = $request->user();
        $isFlagged = false;
        if ($user) {
            $recentCount = Donation::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subHour())
                ->count();
            if ($recentCount >= 10) $isFlagged = true;
        }

        $donation = new Donation();
        $donation->user_id = $user ? $user->id : ($data['user_id'] ?? null);
        $donation->donor_id = $data['donor_id'] ?? null;
        $donation->amount = $data['amount'];
        $donation->status = 'pending';
        $donation->is_flagged = $isFlagged;
        $donation->payment_method = $data['payment_method'] ?? $data['cash_channel'] ?? 'cash';

        if (in_array($data['type'], ['campaign', 'project'])) {
            $donation->donationable_type = $data['type'] === 'campaign' ? \App\Models\Campaign::class : \App\Models\Project::class;
            $donation->donationable_id = $data['target_id'] ?? $data[$data['type'].'_id'] ?? 0;
        } else {
            $donation->donationable_type = $data['type'];
            $donation->donationable_id = 0;
            $donation->type = in_array($data['type'], ['general', 'sadaqa']) ? 'cash' : $data['type'];
        }

        $donation->save();
        return $donation->load(['donor','user','donationable']);
    }

    public function show(Donation $donation)
    {
        return $donation->load(['donor','project','campaign','warehouse','delegate','route']);
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:cash,in_kind',
            'cash_channel' => 'nullable|in:cash,instapay,vodafone_cash,delegate,hq',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:64',
            'estimated_value' => 'nullable|numeric',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'delegate_id' => 'nullable|exists:delegates,id',
            'route_id' => 'nullable|exists:travel_routes,id',
            'allocation_note' => 'nullable|string',
            'received_at' => 'nullable|date'
        ]);
        if (!\Illuminate\Support\Facades\Schema::hasColumn('donations','guest_house_id')) {
            unset($data['guest_house_id']);
        }
        // نحتفظ بالقناة كما هي في التحديث أيضًا
        $donation->update($data);
        return $donation->load(['donor','project','campaign','warehouse','delegate','route']);
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return response()->noContent();
    }
}
