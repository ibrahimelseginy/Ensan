<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name'          => 'required|string|max:255|unique:roles,name,' . ($roleId ?? ''),
            'key'           => 'required|string|max:255|unique:roles,key,' . ($roleId ?? ''),
            'description'   => 'nullable|string',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ];
    }
}
