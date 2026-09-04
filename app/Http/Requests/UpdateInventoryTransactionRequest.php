<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateInventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => 'sometimes|in:in,transfer,out,transfer_in,transfer_out,stock_count_shortage,stock_count_increase,reconciliation',
            'quantity'       => 'nullable|numeric|min:0.01',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'project_id'     => 'nullable|exists:projects,id',
            'campaign_id'    => 'nullable|exists:campaigns,id',
            'reference'      => 'nullable|string'
        ];
    }
}
