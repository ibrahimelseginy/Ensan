<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $donationId = $this->route('donation')?->id;
        
        return [
            'type'            => 'sometimes|in:cash,in_kind',
            'cash_channel'    => 'nullable|in:cash,instapay,vodafone_cash,delegate',
            'amount'          => 'nullable|numeric|min:0.01',
            'currency'        => 'nullable|string',
            'receipt_number'  => 'nullable|string|max:64|unique:donations,receipt_number,' . ($donationId ?? ''),
            'estimated_value' => 'nullable|numeric|min:0.01',
            'project_id'      => 'nullable|exists:projects,id',
            'campaign_id'     => 'nullable|exists:campaigns,id',
            'guest_house_id'  => 'nullable|exists:guest_houses,id',
            'warehouse_id'    => 'nullable|exists:warehouses,id',
            'treasury_id'     => 'nullable|exists:treasuries,id',
            'delegate_id'     => 'nullable|exists:delegates,id',
            'route_id'        => 'nullable|exists:travel_routes,id',
            'allocation_note' => 'nullable|string',
            'received_at'     => 'nullable|date'
        ];
    }
}
