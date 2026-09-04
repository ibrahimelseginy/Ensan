<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email',
            'password'        => 'required|string|min:6',
            'phone'           => 'nullable|string|max:20',
            'active'          => 'nullable|boolean',
            'college'         => 'nullable|string|max:255',
            'governorate'     => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:255',
            'project_role'    => 'nullable|string|max:255',
            'volunteer_hours' => 'nullable|numeric|min:0',
            'project_id'      => 'nullable|exists:projects,id',
            'campaign_id'     => 'nullable|exists:campaigns,id',
            'guest_house_id'  => 'nullable|exists:guest_houses,id',
            'join_date'       => 'nullable|date',
            'profile_photo'   => 'nullable|any_image|max:10240'
        ];
    }
}
