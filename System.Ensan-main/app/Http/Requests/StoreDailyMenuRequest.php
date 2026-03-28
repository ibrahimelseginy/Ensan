<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDailyMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_date'            => 'required|date',
            'responsible_user_id' => 'nullable|exists:users,id',
            'meal_type'           => 'nullable|string|max:255',
            'menu'                => 'nullable|string',
            'meal_count'          => 'nullable|integer|min:0',
            'ingredients'         => 'nullable|string',
        ];
    }
}
