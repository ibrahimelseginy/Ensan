<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTreasuryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:255|unique:treasuries,code',
            'type'            => 'nullable|string|max:50',
            'guest_house_id'  => 'nullable|exists:guest_houses,id',
            'project_id'      => 'nullable|exists:projects,id',
            'campaign_id'     => 'nullable|exists:campaigns,id',
            'description'     => 'nullable|string',
            'manager_id'      => 'nullable|exists:users,id',
            'location'        => 'nullable|string',
            'currency'        => 'required|string|max:10',
            'opening_balance' => 'required|numeric|min:0',
            'is_active'       => 'boolean'
        ];
    }
}
