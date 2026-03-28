<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDelegateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'                 => 'required|date',
            'description'          => 'nullable|string',
            'cost'                 => 'required|numeric|min:0',
            'fuel_cost'            => 'nullable|numeric|min:0',
            'other_expenses'       => 'nullable|numeric|min:0',
            'from_location'        => 'nullable|string|max:255',
            'to_location'          => 'nullable|string|max:255',
            'distance_km'          => 'nullable|numeric|min:0',
            'payment_method'       => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
            'status'               => 'required|in:pending,paid',
            'create_journal_entry' => 'nullable|boolean'
        ];
    }
}
