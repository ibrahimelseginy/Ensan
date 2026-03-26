<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipWebController extends Controller
{
    public function index()
    {
        $memberships = Membership::latest()->paginate(50);
        return view('memberships.index', compact('memberships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_name' => 'required|string|max:255',
            'entity_type' => 'nullable|string|max:255',
            'service_provided' => 'nullable|string|max:255',
            'discount_percentage' => 'nullable|string|max:255',
            'discount_conditions' => 'nullable|string',
            'beneficiary_category' => 'nullable|string|max:255',
            'discount_activation_method' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'entity_address' => 'nullable|string|max:255',
            'entity_location' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'entity_contact_name' => 'nullable|string|max:255',
            'entity_source_name' => 'nullable|string|max:255',
            'cooperation_start_date' => 'nullable|date',
            'cooperation_end_date' => 'nullable|date',
            'cooperation_status' => 'nullable|string|max:255',
            'priority_level' => 'nullable|string|max:255',
            'beneficiaries_count' => 'nullable|integer',
            'entity_rating' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Membership::create($validated);

        return redirect()->route('memberships.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'entity_name' => 'required|string|max:255',
            'entity_type' => 'nullable|string|max:255',
            'service_provided' => 'nullable|string|max:255',
            'discount_percentage' => 'nullable|string|max:255',
            'discount_conditions' => 'nullable|string',
            'beneficiary_category' => 'nullable|string|max:255',
            'discount_activation_method' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'entity_address' => 'nullable|string|max:255',
            'entity_location' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'entity_contact_name' => 'nullable|string|max:255',
            'entity_source_name' => 'nullable|string|max:255',
            'cooperation_start_date' => 'nullable|date',
            'cooperation_end_date' => 'nullable|date',
            'cooperation_status' => 'nullable|string|max:255',
            'priority_level' => 'nullable|string|max:255',
            'beneficiaries_count' => 'nullable|integer',
            'entity_rating' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $membership->update($validated);

        return redirect()->route('memberships.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('memberships.index')->with('success', 'تم الحذف بنجاح');
    }
}
