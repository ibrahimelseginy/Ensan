<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreInventoryReconcileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'      => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type'         => 'required|in:stock_count_shortage,stock_count_increase,reconciliation',
            'quantity'     => 'required|numeric|min:0.01',
            'project_id'   => 'nullable|exists:projects,id',
            'campaign_id'  => 'nullable|exists:campaigns,id',
            'notes'        => 'required|string',
            'date'         => 'nullable|date'
        ];
    }
}
