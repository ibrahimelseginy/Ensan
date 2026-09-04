<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $campaign = $this->route('campaign');

        return [
            'name'        => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(Campaign::class, 'name')->ignore($campaign?->getKey()),
            ],
            'season_year' => 'sometimes|integer|min:2000|max:2100',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'sometimes|in:active,archived',
            'project_id'  => 'nullable|exists:projects,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => preg_replace('/\s+/u', ' ', trim((string) $this->input('name'))),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'اسم الحملة مطلوب.',
            'name.unique'             => 'توجد حملة بنفس الاسم بالفعل، يرجى اختيار اسم آخر.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية أو مساويًا له.',
        ];
    }
}
