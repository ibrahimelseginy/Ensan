<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:255',
            'fixed'       => 'sometimes|boolean',
            'status'      => 'sometimes|in:active,archived',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:255'
        ];
    }
}
