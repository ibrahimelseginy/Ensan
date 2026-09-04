<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'merchant_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'source_name'   => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
            'website'       => 'nullable|url|max:255'
        ];
    }
}
