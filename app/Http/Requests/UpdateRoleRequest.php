<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasPermission('roles.edit');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('key')) {
            $this->merge(['key' => strtolower(trim((string) $this->input('key')))]);
        }
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name'          => 'required|string|max:255|unique:roles,name,' . ($roleId ?? ''),
            'key'           => ['required', 'string', 'max:255', 'regex:/^[a-z][a-z0-9_.-]*$/', 'unique:roles,key,' . ($roleId ?? '')],
            'description'   => 'nullable|string',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ];
    }

    public function messages(): array
    {
        return [
            'key.regex' => 'معرّف الدور يجب أن يبدأ بحرف إنجليزي ويحتوي على حروف صغيرة أو أرقام أو نقطة أو شرطة فقط.',
        ];
    }
}
