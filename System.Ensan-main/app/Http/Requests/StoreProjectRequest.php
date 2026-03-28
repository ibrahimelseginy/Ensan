<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'fixed'       => 'required|boolean',
            'status'      => 'required|in:active,archived',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:255'
        ];
    }
}
