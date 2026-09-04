<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\Beneficiary;
use App\Models\User;

final class VisitWebController extends Controller
{
    public function index()
    {
        $visits = FieldVisit::with(['beneficiary', 'researcher'])->latest('visit_date')->paginate(10);
        
        $stats = [
            'upcoming' => FieldVisit::where('status', 'scheduled')->whereDate('visit_date', '>=', today())->count(),
            'completed_month' => FieldVisit::where('status', 'completed')->whereMonth('visit_date', today()->month)->count(),
            'needs_review' => FieldVisit::where('recommendation', 'needs_review')->count(),
        ];

        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $researchers = User::where('is_employee', true)->orderBy('name')->get();

        return view('dashboard.visits.index', compact('visits', 'stats', 'beneficiaries', 'researchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'researcher_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
            'report' => 'nullable|string',
            'recommendation' => 'nullable|in:approve,reject,needs_review'
        ]);

        FieldVisit::create($validated);
        Beneficiary::where('id', $validated['beneficiary_id'])->update(['status' => 'under_review']);

        return redirect()->route('visits.index')->with('success', 'تم جدولة الزيارة بنجاح وتحويل حالة المستفيد إلى تحت المراجعة');
    }
}
