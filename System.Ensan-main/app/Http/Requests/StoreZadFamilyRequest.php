<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreZadFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mother_name'              => 'required|string|max:255',
            'children_names'           => 'nullable|string',
            'phone'                    => 'nullable|string|max:20',
            'backup_phone'             => 'nullable|string|max:20',
            'address'                  => 'nullable|string|max:500',
            'children_count'           => 'nullable|integer|min:0',
            'sponsored_children_count' => 'nullable|integer|min:0',
            'study_grade'              => 'nullable|string|max:255',
            'poultry_type'             => 'nullable|string|max:255',
            'notes_cases'              => 'nullable|string',
            'meat'                     => 'nullable|string|max:255'
        ];
    }
}
