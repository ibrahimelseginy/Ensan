<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreFinancialClosureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'     => 'required|date',
            'branch'   => 'nullable|string',
            'approved' => 'nullable|boolean'
        ];
    }
}
