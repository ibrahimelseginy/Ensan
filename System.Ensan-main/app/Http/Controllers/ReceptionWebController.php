<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceptionLog;

final class ReceptionWebController extends Controller
{
    public function index()
    {
        $logs = ReceptionLog::latest()->paginate(10);
        $todayStats = [
            'total' => ReceptionLog::whereDate('created_at', today())->count(),
            'personal' => ReceptionLog::whereDate('created_at', today())->where('visit_type', 'personal')->count(),
            'phone' => ReceptionLog::whereDate('created_at', today())->where('visit_type', 'phone')->count(),
            'completed' => ReceptionLog::whereDate('created_at', today())->where('status', 'completed')->count(),
        ];
        
        return view('dashboard.reception.index', compact('logs', 'todayStats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'visit_type' => 'required|in:personal,phone',
            'purpose' => 'required|in:help_request,complaint,donation,inquiry,other',
            'notes' => 'nullable|string',
            'directed_to' => 'nullable|string'
        ]);

        $log = ReceptionLog::create($validated);

        if ($request->has('directed_to') && $request->directed_to) {
            $log->update(['status' => 'directed']);
        }

        return redirect()->route('reception.index')->with('success', 'تم تسجيل السجل بنجاح');
    }
}
