<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

final class ComplaintTrackingController extends Controller
{
    public function show()
    {
        return view('complaints.track');
    }

    public function search(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:20',
        ], [
            'tracking_code.required' => 'يرجى إدخال كود التتبع.',
        ]);

        $code = strtoupper(trim($request->tracking_code));
        $complaint = Complaint::where('tracking_code', $code)->first();

        return view('complaints.track', compact('complaint', 'code'));
    }
}
