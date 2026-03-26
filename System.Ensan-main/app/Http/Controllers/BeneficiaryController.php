<?php
namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index()
    {
        return Beneficiary::with(['project','campaign'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'national_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assistance_type' => 'required|in:financial,in_kind,service',
            'status' => 'in:new,under_review,accepted',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id'
        ]);
        return Beneficiary::create($data);
    }

    public function show(Beneficiary $beneficiary)
    {
        return $beneficiary->load(['project','campaign']);
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $data = $request->validate([
            'full_name' => 'sometimes|string',
            'national_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assistance_type' => 'sometimes|in:financial,in_kind,service',
            'status' => 'in:new,under_review,accepted',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id'
        ]);
        if (isset($data['status'])) {
            $allowed = [
                'new' => ['under_review'],
                'under_review' => ['accepted'],
                'accepted' => [],
            ];
            $current = $beneficiary->status;
            $next = $data['status'];
            if ($current === $next) {
                unset($data['status']);
            } else {
                $can = in_array($next, $allowed[$current] ?? [], true);
                if (!$can) {
                    return response()->json(['message' => 'invalid status transition'], 422);
                }
            }
        }
        $beneficiary->update($data);
        return $beneficiary->load(['project','campaign']);
    }

    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return response()->noContent();
    }
}
