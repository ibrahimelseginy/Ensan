<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddTreasuryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'             => 'required|in:in,out',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'required|string',
            'transaction_date' => 'required|date',
            'reference'        => 'nullable|string'
        ];
    }
}
