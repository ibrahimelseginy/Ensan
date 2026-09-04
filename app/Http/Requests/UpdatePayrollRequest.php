<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdatePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month'      => 'nullable|string',
            'amount'     => 'nullable|numeric|min:0',
            'bonuses'    => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'currency'   => 'nullable|string',
            'paid_at'    => 'nullable|date',
            'notes'      => 'nullable|string'
        ];
    }
}
