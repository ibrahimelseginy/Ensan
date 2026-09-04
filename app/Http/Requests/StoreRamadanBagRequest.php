<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRamadanBagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beneficiary_name' => 'required|string|max:255',
            'national_id'      => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:255',
            'phone_2'          => 'nullable|string|max:255',
            'marital_status'   => 'nullable|string|max:255',
            'spouse_name'      => 'nullable|string|max:255',
            'family_members'   => 'nullable|integer',
            'case_conditions'  => 'nullable|string',
            'region'           => 'nullable|string|max:255',
            'bags_count'       => 'nullable|integer',
            'address'          => 'nullable|string',
            'bag_contents'     => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|string|max:255',
            'project_id'       => 'nullable|exists:projects,id',
            'campaign_id'      => 'nullable|exists:campaigns,id',
        ];
    }
}
