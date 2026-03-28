<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'                   => 'nullable|string|max:50|unique:beneficiaries,code',
            'full_name'              => 'required|string|max:255',
            'national_id'            => 'nullable|string|max:20',
            'phone'                  => 'nullable|string|max:20',
            'address'                => 'nullable|string|max:500',
            'assistance_type'        => 'required|in:financial,in_kind,service',
            'status'                 => 'nullable|in:new,under_review,accepted,rejected',
            'project_id'             => 'nullable|exists:projects,id',
            'campaign_id'            => 'nullable|exists:campaigns,id',
            'guest_house_id'         => 'nullable|exists:guest_houses,id',
            'notes'                  => 'nullable|string',
            'rejection_reason'       => 'nullable|string',
            'allocation_type'        => 'nullable|string|max:100',
            'child_sponsorship_type' => 'nullable|string|max:100'
        ];
    }
}
