<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku'                 => 'nullable|string|max:100|unique:items,sku',
            'name'                => 'required|string|max:255',
            'unit'                => 'nullable|string|max:50',
            'estimated_value'     => 'nullable|numeric|min:0',
            'original_price'      => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|between:0,100'
        ];
    }
}
