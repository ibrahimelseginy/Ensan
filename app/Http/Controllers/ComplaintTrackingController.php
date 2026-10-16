<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

final class ComplaintTrackingController extends Controller
{
    /**
     * Display the tracking page, or perform an immediate lookup if ?code= or ?tracking_code= is passed.
     */
    public function show(Request $request)
    {
        $code = $request->query('code') ?: $request->query('tracking_code');
        if (!empty($code)) {
            return $this->performLookup((string) $code);
        }

        return view('complaints.track');
    }

    /**
     * Handle search form submission.
     */
    public function search(Request $request)
    {
        $request->validate([
            'tracking_code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/'],
        ], [
            'tracking_code.required' => 'يرجى إدخال كود التتبع.',
            'tracking_code.regex' => 'صيغة كود التتبع غير صالحة. يجب أن يحتوي على حروف وأرقام فقط.',
        ]);

        return $this->performLookup((string) $request->tracking_code);
    }

    /**
     * Unified secure lookup handler with privacy masking.
     */
    private function performLookup(string $rawCode)
    {
        // 1. Sanitize & Normalize code (Uppercase, alphanumeric + hyphen only)
        $clean = strtoupper(trim($rawCode));
        $code = (string) preg_replace('/[^A-Z0-9\-]/', '', $clean);

        // Security check: validate code length constraints
        if (strlen($code) < 4 || strlen($code) > 25) {
            return view('complaints.track', [
                'code' => $clean,
                'complaint' => null,
                'error' => 'كود التتبع غير صالح أو غير مكتمل.',
            ]);
        }

        // Query database
        $complaint = Complaint::where('tracking_code', $code)->first();

        // 2. Safe privacy masking for public display
        $maskedPhone = null;
        $maskedName = null;

        if ($complaint) {
            // Mask Phone: e.g. 01012345678 -> 010****5678
            if (!empty($complaint->complainant_phone)) {
                $phone = trim((string) $complaint->complainant_phone);
                $len = strlen($phone);
                if ($len >= 8) {
                    $maskedPhone = substr($phone, 0, 3) . '****' . substr($phone, -4);
                } elseif ($len >= 4) {
                    $maskedPhone = substr($phone, 0, 2) . '***' . substr($phone, -2);
                } else {
                    $maskedPhone = '***';
                }
            }

            // Complainant Name privacy formatting
            if (!empty($complaint->source_name)) {
                $nameParts = preg_split('/\s+/u', trim((string) $complaint->source_name));
                if (count($nameParts) > 1) {
                    $first = $nameParts[0];
                    $last = end($nameParts);
                    $maskedName = $first . ' ... ' . mb_substr($last, 0, 1) . '.';
                } else {
                    $maskedName = $complaint->source_name;
                }
            }
        }

        return view('complaints.track', compact('complaint', 'code', 'maskedPhone', 'maskedName'));
    }
}
