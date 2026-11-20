<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'sometimes|string|max:255',
            // A soft-deleted account must not reserve an email address forever.
            // Active accounts remain strictly unique, excluding the user being edited.
            'email'                 => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')
                ->ignore($this->route('user'))
                ->withoutTrashed()],
            'password'              => 'nullable|string|min:6',
            'phone'                 => 'nullable|string|max:20',
            'national_id'           => 'nullable|string|max:25',
            'is_employee'           => 'nullable|boolean',
            'is_volunteer'          => 'nullable|boolean',
            'active'                     => 'nullable|boolean',
            'roles'                      => 'nullable|array',
            'roles.*'                    => 'exists:roles,id',
            'roles_present'              => 'nullable|boolean',
            'direct_permissions'         => 'nullable|array',
            'direct_permissions.*'       => 'exists:permissions,id',
            'direct_permissions_present' => 'nullable|boolean',
            'department'                 => 'nullable|string|max:255',
            'job_title'             => 'nullable|string|max:255',
            'salary'                => 'nullable|numeric|min:0',
            'join_date'             => 'nullable|date',
            'contract_start_date'   => 'nullable|date',
            'contract_end_date'     => 'nullable|date|after_or_equal:contract_start_date',
            'profile_photo'         => 'nullable|any_image|max:51200',
            'contract_image'        => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,svg,tiff,heic,heif,avif,pdf|max:51200',
            'criminal_record_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,svg,tiff,heic,heif,avif,pdf|max:51200',
            'id_card_image'         => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,svg,tiff,heic,heif,avif,pdf|max:51200',
            'annual_leave_quota'    => 'nullable|integer|min:0',
            'leave_balance'         => 'nullable|integer|min:0'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }
    }
}
