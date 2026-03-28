<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'               => 'required|date',
            'branch'             => 'nullable|string|max:255',
            'gate'               => 'nullable|string|max:255',
            'entry_type'         => 'required|string|max:255',
            'description'        => 'nullable|string',
            'locked'             => 'nullable|boolean',
            'lines'              => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit'      => 'nullable|numeric|min:0',
            'lines.*.credit'     => 'nullable|numeric|min:0'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = collect($this->input('lines', []));
            $totalDebit  = (float) $lines->sum('debit');
            $totalCredit = (float) $lines->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                $validator->errors()->add('lines', 'القيد غير متزن. إجمالي المدين: ' . $totalDebit . ' - إجمالي الدائن: ' . $totalCredit);
            }
        });
    }
}
