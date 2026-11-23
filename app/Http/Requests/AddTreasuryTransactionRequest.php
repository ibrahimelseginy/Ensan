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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'source_treasury_id'      => $this->filled('source_treasury_id') ? (int) $this->input('source_treasury_id') : null,
            'destination_treasury_id' => $this->filled('destination_treasury_id') ? (int) $this->input('destination_treasury_id') : null,
            'from_treasury_id'        => $this->filled('from_treasury_id') ? (int) $this->input('from_treasury_id') : null,
            'to_treasury_id'          => $this->filled('to_treasury_id') ? (int) $this->input('to_treasury_id') : null,
            'treasury_id'             => $this->filled('treasury_id') ? (int) $this->input('treasury_id') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'treasury_id'             => 'nullable|exists:treasuries,id',
            'from_treasury_id'        => 'nullable|exists:treasuries,id',
            'to_treasury_id'          => 'nullable|exists:treasuries,id',
            'source_treasury_id'      => 'nullable|exists:treasuries,id',
            'destination_treasury_id' => 'nullable|exists:treasuries,id',
            'type'                    => 'required|in:in,out,transfer',
            'amount'                  => 'required|numeric|min:0.01',
            'description'             => 'required|string',
            'transaction_date'        => 'required|date',
            'reference'               => 'nullable|string'
        ];
    }
}
