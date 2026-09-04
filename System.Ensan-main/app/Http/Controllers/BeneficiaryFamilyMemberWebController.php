<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BeneficiaryFamilyMember;
use Illuminate\View\View;

final class BeneficiaryFamilyMemberWebController extends Controller
{
    public function show(BeneficiaryFamilyMember $familyMember): View
    {
        $familyMember->load(['beneficiary.project', 'sponsors']);

        return view('beneficiaries.family-members.show', compact('familyMember'));
    }
}
