<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name'           => 'required|string|max:255',
            'quantity'            => 'required|integer|min:1',
            'original_price'      => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'purchase_date'       => 'required|date',
            'notes'               => 'nullable|string'
        ];
    }
}
