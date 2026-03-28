<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateGuestHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'sometimes|string|max:255',
            'location'        => 'nullable|string|max:500',
            'phone'           => 'nullable|string|max:20',
            'capacity'        => 'nullable|integer|min:0',
            'status'          => 'sometimes|in:active,archived',
            'description'     => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id'
        ];
    }
}
