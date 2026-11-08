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
            'name'                => 'sometimes|string|max:255|unique:warehouses,name,' . ($warehouseId ?? ''),
            'location'            => 'nullable|string|max:500',
            'department'          => 'nullable|string|max:255',
            'guest_house_id'      => 'nullable|exists:guest_houses,id',
            'project_id'          => 'nullable|exists:projects,id',
            'manager_id'          => 'nullable|exists:users,id',
            'assigned_user_ids'   => 'nullable|array',
            'assigned_user_ids.*' => 'exists:users,id',
        ];
    }
}
