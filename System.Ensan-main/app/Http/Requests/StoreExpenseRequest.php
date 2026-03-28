<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => 'required|in:operational,aid,logistics',
            'category'       => 'nullable|string|max:100',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'treasury_id'    => 'required|exists:treasuries,id',
            'description'    => 'nullable|string',
            'project_id'     => 'nullable|exists:projects,id',
            'campaign_id'    => 'nullable|exists:campaigns,id',
            'workspace_id'   => 'nullable|exists:workspaces,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'paid_at'        => 'nullable|date'
        ];
    }
}
