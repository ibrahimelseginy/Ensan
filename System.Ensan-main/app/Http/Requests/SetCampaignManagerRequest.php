<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SetCampaignManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_photo'   => 'nullable|any_image|max:5120'
        ];
    }
}
