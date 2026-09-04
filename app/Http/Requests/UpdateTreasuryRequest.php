<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTreasuryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $treasuryId = $this->route('treasury')?->id;
        return [
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:255|unique:treasuries,code,' . ($treasuryId ?? ''),
            'description'     => 'nullable|string',
            'manager_id'      => 'nullable|exists:users,id',
            'location'        => 'nullable|string',
            'currency'        => 'required|string|max:10',
            'is_active'       => 'boolean'
        ];
    }
}
