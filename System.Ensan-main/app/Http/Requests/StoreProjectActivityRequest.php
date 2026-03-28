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
            'name'                => 'required|string|max:255',
            'activity_date'       => 'required|date',
            'responsible_user_id' => 'nullable|exists:users,id',
            'notes'               => 'nullable|string'
        ];
    }
}
