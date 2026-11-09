<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BeneficiaryFamilyMember;
use App\Models\Donor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BeneficiaryFamilyMemberWebController extends Controller
{
    public function show(BeneficiaryFamilyMember $familyMember): View
    {
        $familyMember->load(['beneficiary.project', 'sponsors', 'psychologicalSupportReports.creator']);
        $allDonors = Donor::orderBy('name')->get(['id', 'name', 'code', 'phone']);
        $psychReports = $familyMember->psychologicalSupportReports;

        return view('beneficiaries.family-members.show', compact('familyMember', 'allDonors', 'psychReports'));
    }

    public function attachSponsor(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'monthly_amount' => 'nullable|numeric|min:0',
            'required_sponsorship_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $donorId = (int) $validated['donor_id'];
        $amount = isset($validated['monthly_amount']) && $validated['monthly_amount'] !== '' ? (float) $validated['monthly_amount'] : null;
        $requiredAmount = isset($validated['required_sponsorship_amount']) && $validated['required_sponsorship_amount'] !== ''
            ? (float) $validated['required_sponsorship_amount']
            : null;
        $notes = $validated['notes'] ?? null;

        $familyMember->sponsors()->syncWithoutDetaching([
            $donorId => [
                'monthly_amount' => $amount,
                'notes' => $notes,
            ]
        ]);

        if ($requiredAmount !== null && $requiredAmount > 0) {
            $familyMember->update(['sponsorship_amount' => $requiredAmount]);
        } elseif ($amount !== null && $amount > 0 && ((float) ($familyMember->sponsorship_amount ?? 0)) <= 0) {
            $familyMember->update(['sponsorship_amount' => $amount]);
        }

        // Also reflect sponsor on beneficiary/family for legal/administrative completeness
        if ($familyMember->beneficiary) {
            $familyMember->beneficiary->sponsors()->syncWithoutDetaching([$donorId]);
        }

        BeneficiaryWebController::recalculateDonorMonthlySponsorship($donorId);

        return back()->with('success', "تم ربط الكفيل بالفرد [{$familyMember->full_name}] بنجاح وتحديث توزيع الكفالة.");
    }

    public function detachSponsor(BeneficiaryFamilyMember $familyMember, Donor $donor): RedirectResponse
    {
        $familyMember->sponsors()->detach($donor->id);
        BeneficiaryWebController::recalculateDonorMonthlySponsorship($donor->id);

        return back()->with('success', "تم إلغاء ربط الكفيل بالفرد [{$familyMember->full_name}].");
    }

    public function updateSponsorship(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $validated = $request->validate([
            'sponsorship_amount' => 'nullable|numeric|min:0',
        ]);

        $amount = isset($validated['sponsorship_amount']) && $validated['sponsorship_amount'] !== '' ? (float)$validated['sponsorship_amount'] : null;
        $familyMember->update(['sponsorship_amount' => $amount]);

        return back()->with('success', "تم تحديث مبلغ كفالة الطفل [{$familyMember->full_name}] بنجاح، والإجمالي يسمّع تلقائياً في ملف الأسرة.");
    }
}
