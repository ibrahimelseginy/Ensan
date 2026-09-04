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
            'description'     => 'nullable|string',
            'manager_id'      => 'nullable|exists:users,id',
            'location'        => 'nullable|string',
            'currency'        => 'required|string|max:10',
            'opening_balance' => 'required|numeric|min:0',
            'is_active'       => 'boolean'
        ];
    }
}
