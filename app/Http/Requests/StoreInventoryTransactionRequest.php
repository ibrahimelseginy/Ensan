<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreInventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'            => 'nullable|required_without:new_item_name',
            'new_item_name'      => 'nullable|string|max:191',
            'new_item_unit'      => 'nullable|string|max:50',
            'warehouse_id'       => 'required|exists:warehouses,id',
            'type'               => 'required|in:in,transfer,out,transfer_in,transfer_out,stock_count_shortage,stock_count_increase,reconciliation',
            'quantity'           => 'required|numeric|min:0.01',
            'source_donation_id' => 'nullable|exists:donations,id',
            'beneficiary_id'     => 'nullable|exists:beneficiaries,id',
            'project_id'         => 'nullable|exists:projects,id',
            'campaign_id'        => 'nullable|exists:campaigns,id',
            'reference'          => 'nullable|string',
            'notes'              => 'nullable|string',
            'date'               => 'nullable|date'
        ];
    }
}
