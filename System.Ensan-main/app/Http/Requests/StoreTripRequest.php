<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id'          => 'required|exists:travel_routes,id',
            'trip_title'        => 'required|string|max:255',
            'trip_description'  => 'nullable|string',
            'trip_date'         => 'required|date',
            'donation_type'     => 'required|in:cash,in_kind',
            'amount'            => 'nullable|numeric|min:0|required_if:donation_type,cash',
            'estimated_value'   => 'nullable|numeric|min:0|required_if:donation_type,in_kind',
            'donation_currency' => 'required|in:EGP,USD,SAR,EUR,AED',
            'donor_name'        => 'required|string|max:255',
            'donor_phone'       => ['required', 'string', 'regex:/^(01[0125][0-9]{8})$/'],
            'delegate_id'       => 'nullable|exists:delegates,id',
            'trip_location'     => 'nullable|string|max:255',
            'trip_city'         => 'nullable|string|max:255',
            'warehouse_id'      => 'nullable|exists:warehouses,id|required_if:donation_type,in_kind',
            'alloc_type'        => 'nullable|in:project,guest_house,campaign,sponsorship,sadaqa_jariya',
            'project_id'        => 'nullable|exists:projects,id',
            'campaign_id'       => 'nullable|exists:campaigns,id',
            'guest_house_id'    => 'nullable|exists:guest_houses,id',
            'sponsorship_type'  => 'nullable|in:طفل,أسرة|required_if:alloc_type,sponsorship',
            'beneficiary_id'    => 'nullable|exists:beneficiaries,id|required_if:alloc_type,sponsorship',
        ];
    }

    public function messages(): array
    {
        return [
            'donor_phone.regex' => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
        ];
    }
}
