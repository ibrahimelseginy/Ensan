<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')?->id;
        
        return [
            'name'     => 'sometimes|string|max:255|unique:warehouses,name,' . ($warehouseId ?? ''),
            'location' => 'nullable|string|max:500'
        ];
    }
}
