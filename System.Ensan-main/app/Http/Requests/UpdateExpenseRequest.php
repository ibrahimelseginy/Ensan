<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => 'sometimes|in:operational,aid,logistics',
            'category'       => 'nullable|string|max:100',
            'amount'         => 'sometimes|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'description'    => 'nullable|string',
            'project_id'     => 'nullable|exists:projects,id',
            'campaign_id'    => 'nullable|exists:campaigns,id',
            'workspace_id'   => 'nullable|exists:workspaces,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'payment_method' => 'nullable|string|max:255',
            'paid_at'        => 'nullable|date'
        ];
    }
}
