<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => 'sometimes|string',
            'type'                       => 'sometimes|in:individual,organization',
            'phone'                      => 'nullable|string',
            'email'                      => 'nullable|email',
            'address'                    => 'nullable|string',
            'classification'             => 'sometimes|in:one_time,recurring',
            'recurring_cycle'            => 'nullable|in:monthly,yearly',
            'active'                     => 'boolean',
            'sponsorship_type'           => 'nullable|in:none,monthly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id'   => 'nullable|exists:beneficiaries,id',
            'sponsorship_project_id'     => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric'
        ];
    }
}
