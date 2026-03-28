<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateDelegateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'route_id'      => 'nullable|exists:travel_routes,id',
            'user_id'       => 'nullable|exists:users,id',
            'profile_photo' => 'nullable|image|max:2048'
        ];
    }
}
