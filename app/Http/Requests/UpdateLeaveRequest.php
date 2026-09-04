<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'             => 'sometimes|in:annual,sick,unpaid,emergency,other',
            'start_date'       => 'sometimes|date',
            'end_date'         => 'sometimes|date|after_or_equal:start_date',
            'reason'           => 'sometimes|string|max:500',
            'status'           => 'sometimes|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string'
        ];
    }
}
