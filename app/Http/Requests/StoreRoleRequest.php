<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasPermission('roles.create');
    }

    protected function prepareForValidation(): void
    {
        $rawKey = strtolower(trim((string) $this->input('key', '')));
        if ($rawKey === '' || !preg_match('/^[a-z][a-z0-9_.-]*$/', $rawKey)) {
            $slug = preg_replace('/[^a-z0-9_.-]+/', '_', $rawKey);
            $slug = trim((string) $slug, '_.-');
            if ($slug === '' || !preg_match('/^[a-z]/', $slug)) {
                $slug = 'custom_role_' . substr(md5((string) $this->input('name', uniqid())), 0, 8);
            }
            $this->merge(['key' => $slug]);
        } else {
            $this->merge(['key' => $rawKey]);
        }
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255|unique:roles,name',
            'key'             => ['required', 'string', 'max:255', 'regex:/^[a-z][a-z0-9_.-]*$/', 'unique:roles,key'],
            'description'     => 'nullable|string',
            'permissions'     => 'nullable|array',
            'permissions.*'   => 'exists:permissions,id',
            'users'           => 'nullable|array',
            'users.*'         => 'exists:users,id',
            'users_present'   => 'nullable|boolean',
            'exclusive_users' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'key.regex' => 'معرّف الدور يجب أن يبدأ بحرف إنجليزي ويحتوي على حروف صغيرة أو أرقام أو نقطة أو شرطة فقط.',
        ];
    }
}
