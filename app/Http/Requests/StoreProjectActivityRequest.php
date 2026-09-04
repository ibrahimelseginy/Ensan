<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProjectActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'          => 'required|in:exhibition,advertising',
            'activity_date' => 'required|date',
            'location'      => 'nullable|string|max:255',
            'revenue'       => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
        ];
    }
}
