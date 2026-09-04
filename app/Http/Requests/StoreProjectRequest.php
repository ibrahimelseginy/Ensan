<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['bail', 'required', 'string', 'max:255', Rule::unique(Project::class, 'name')],
            'fixed'       => 'required|boolean',
            'status'      => 'required|in:active,archived',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:255'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('fixed')) {
            $this->merge([
                'fixed' => filter_var($this->input('fixed'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);
        }
        if ($this->has('name')) {
            $this->merge([
                'name' => preg_replace('/\s+/u', ' ', trim((string) $this->input('name'))),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'يوجد مشروع بهذا الاسم بالفعل. يرجى اختيار اسم مختلف.',
        ];
    }
}
