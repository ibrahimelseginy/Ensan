<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('item')?->id;
        
        return [
            'sku'                 => 'sometimes|nullable|string|max:100|unique:items,sku,' . ($itemId ?? ''),
            'name'                => 'sometimes|string|max:255',
            'unit'                => 'nullable|string|max:50',
            'estimated_value'     => 'nullable|numeric|min:0',
            'original_price'      => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|between:0,100',
            'category'            => 'nullable|string',
            'min_stock_level'     => 'nullable|integer',
            'max_stock_level'     => 'nullable|integer',
            'barcode'             => 'nullable|string'
        ];
    }
}
