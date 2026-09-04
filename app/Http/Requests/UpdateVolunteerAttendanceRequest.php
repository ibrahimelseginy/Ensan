<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateVolunteerAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'             => 'nullable|date',
            'check_in_at'      => 'nullable|date_format:H:i',
            'check_out_at'     => 'nullable|date_format:H:i|after:check_in_at',
            'notes'            => 'nullable|string',
            'rating'           => 'nullable|integer|min:1|max:5',
            'evaluation_notes' => 'nullable|string'
        ];
    }
}
