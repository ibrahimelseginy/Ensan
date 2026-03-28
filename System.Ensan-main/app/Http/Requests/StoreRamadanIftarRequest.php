<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRamadanIftarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beneficiary_name' => 'required|string|max:255',
            'nickname'         => 'nullable|string|max:255',
            'national_id'      => 'nullable|string|max:255',
            'region'           => 'nullable|string|max:255',
            'meals_count'      => 'required|integer|min:1',
            'guide_name'       => 'nullable|string|max:255',
            'guide_phone'      => 'nullable|string|max:255',
            'guide_phone_2'    => 'nullable|string|max:255',
            'delivery_method'  => 'nullable|string|max:255',
            'delivery_cost'    => 'nullable|numeric|min:0',
            'address'          => 'nullable|string',
            'notes'            => 'nullable|string',
            'project_id'       => 'nullable|exists:projects,id',
            'campaign_id'      => 'nullable|exists:campaigns,id',
        ];
    }
}
