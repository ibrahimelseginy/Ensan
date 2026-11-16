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

    protected function prepareForValidation(): void
    {
        if ($this->input('type') === 'permission') {
            if ($this->filled('start_date')) {
                $this->merge(['end_date' => $this->input('start_date')]);
            }
        } elseif ($this->has('type')) {
            $this->merge([
                'from_time' => null,
                'to_time'   => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'type'             => 'sometimes|in:annual,sick,unpaid,emergency,permission,other',
            'start_date'       => 'sometimes|date',
            'end_date'         => 'sometimes|date|after_or_equal:start_date',
            'from_time'        => 'nullable|required_if:type,permission|date_format:H:i',
            'to_time'          => 'nullable|required_if:type,permission|date_format:H:i|after:from_time',
            'reason'           => 'sometimes|string|max:500',
            'status'           => 'sometimes|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'from_time.required_if' => 'يرجى تحديد ساعة بداية الإذن.',
            'to_time.required_if'   => 'يرجى تحديد ساعة نهاية الإذن.',
            'to_time.after'         => 'ساعة نهاية الإذن يجب أن تكون بعد ساعة البداية.',
        ];
    }
}
