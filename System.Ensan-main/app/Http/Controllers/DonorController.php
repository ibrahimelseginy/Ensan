<?php
namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index()
    {
        return Donor::paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:individual,organization',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'classification' => 'required|in:one_time,recurring',
            'recurring_cycle' => 'nullable|in:monthly,yearly',
            'active' => 'boolean',
            'sponsorship_type' => 'nullable|in:none,monthly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'sponsorship_project_id' => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric'
        ]);
        $email = isset($data['email']) ? trim((string)$data['email']) : null;
        $phone = isset($data['phone']) ? trim((string)$data['phone']) : null;
        $existing = null;
        if ($email || $phone) {
            $existing = Donor::query()
                ->when($email, fn($q) => $q->where('email', $email))
                ->when($phone, fn($q) => $q->orWhere('phone', $phone))
                ->first();
        }
        if ($existing) {
            $updateData = array_filter($data, fn($v) => !is_null($v));
            $existing->update($updateData);
            return $existing;
        }
        if (($data['sponsorship_type'] ?? 'none') !== 'none' && empty($data['sponsorship_project_id'])) {
            $defaultProjId = \App\Models\Project::where('name','بعثاء الامل')->value('id');
            if ($defaultProjId) { $data['sponsorship_project_id'] = $defaultProjId; }
        }
        return Donor::create($data);
    }

    public function show(Donor $donor)
    {
        return $donor;
    }

    public function update(Request $request, Donor $donor)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:individual,organization',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'classification' => 'sometimes|in:one_time,recurring',
            'recurring_cycle' => 'nullable|in:monthly,yearly',
            'active' => 'boolean',
            'sponsorship_type' => 'nullable|in:none,monthly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'sponsorship_project_id' => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric'
        ]);
        $donor->update($data);
        return $donor;
    }

    public function destroy(Donor $donor)
    {
        $donor->delete();
        return response()->noContent();
    }
}
