<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreInventoryTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'           => 'required|exists:items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'   => 'required|exists:warehouses,id',
            'quantity'          => 'required|numeric|min:0.01',
            'project_id'        => 'nullable|exists:projects,id',
            'campaign_id'       => 'nullable|exists:campaigns,id',
            'notes'             => 'nullable|string',
            'date'              => 'nullable|date'
        ];
    }
}
