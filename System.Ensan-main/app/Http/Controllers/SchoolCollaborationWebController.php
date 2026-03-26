<?php

namespace App\Http\Controllers;

use App\Models\SchoolCollaboration;
use Illuminate\Http\Request;

class SchoolCollaborationWebController extends Controller
{
    public function index()
    {
        $collaborations = SchoolCollaboration::latest()->paginate(50);
        return view('school_collaborations.index', compact('collaborations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'discount' => 'nullable|string|max:255',
            'transactions' => 'nullable|string',
            'campaign' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        SchoolCollaboration::create($validated);

        return redirect()->route('school-collaborations.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, SchoolCollaboration $school_collaboration)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'discount' => 'nullable|string|max:255',
            'transactions' => 'nullable|string',
            'campaign' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $school_collaboration->update($validated);

        return redirect()->route('school-collaborations.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(SchoolCollaboration $school_collaboration)
    {
        $school_collaboration->delete();

        return redirect()->route('school-collaborations.index')->with('success', 'تم الحذف بنجاح');
    }
}
