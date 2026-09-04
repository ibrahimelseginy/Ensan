<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'              => 'required|exists:users,id',
            'month'                => 'required|string',
            'amount'               => 'required|numeric|min:0',
            'bonuses'              => 'nullable|numeric|min:0',
            'deductions'           => 'nullable|numeric|min:0',
            'currency'             => 'nullable|string',
            'paid_at'              => 'nullable|date',
            'notes'                => 'nullable|string',
            'create_journal_entry' => 'nullable|boolean'
        ];
    }
}
