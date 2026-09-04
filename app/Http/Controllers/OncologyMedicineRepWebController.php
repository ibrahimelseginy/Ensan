<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\OncologyMedicineRep;
use Illuminate\Http\Request;

final class OncologyMedicineRepWebController extends Controller
{
    public function index()
    {
        $reps = OncologyMedicineRep::latest()->paginate(50);
        return view('oncology_medicine_reps.index', compact('reps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        OncologyMedicineRep::create($validated);

        return redirect()->route('oncology-medicine-reps.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, OncologyMedicineRep $oncology_medicine_rep)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $oncology_medicine_rep->update($validated);

        return redirect()->route('oncology-medicine-reps.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(OncologyMedicineRep $oncology_medicine_rep)
    {
        $oncology_medicine_rep->delete();

        return redirect()->route('oncology-medicine-reps.index')->with('success', 'تم الحذف بنجاح');
    }
}
