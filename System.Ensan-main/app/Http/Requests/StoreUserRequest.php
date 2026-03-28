<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:6',
            'phone'                 => 'nullable|string|max:20',
            'is_employee'           => 'nullable|boolean',
            'is_volunteer'          => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'roles'                 => 'nullable|array',
            'roles.*'               => 'exists:roles,id',
            'department'            => 'nullable|string|max:255',
            'job_title'             => 'nullable|string|max:255',
            'salary'                => 'nullable|numeric|min:0',
            'join_date'             => 'nullable|date',
            'contract_start_date'   => 'nullable|date',
            'contract_end_date'     => 'nullable|date|after_or_equal:contract_start_date',
            'profile_photo'         => 'nullable|image|max:10240',
            'contract_image'        => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'criminal_record_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'id_card_image'         => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'annual_leave_quota'    => 'nullable|integer|min:0',
            'leave_balance'         => 'nullable|integer|min:0'
        ];
    }
}
