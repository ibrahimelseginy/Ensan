<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Donor;

final class UpdateDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $donorId = $this->route('donor') instanceof Donor
            ? $this->route('donor')->id
            : ($this->donor?->id ?? $this->route('donor'));

        return [
            'name'                       => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('donors', 'name')->ignore($donorId),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $parts = preg_split('/\s+/u', trim((string) $value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($parts) < 3) {
                        $fail('يجب أن يكون اسم المتبرع ثلاثياً على الأقل (مثال: محمد أحمد علي).');
                    }
                },
            ],
            'type'                       => 'sometimes|in:individual,organization',
            'phone'                      => 'nullable|string|max:20',
            'email'                      => 'nullable|email',
            'address'                    => 'nullable|string|max:500',
            'classification'             => 'sometimes|in:one_time,recurring',
            'recurring_cycle'            => 'nullable|required_if:classification,recurring|in:monthly,yearly',
            'monthly_donation_day'       => 'nullable|required_if:recurring_cycle,monthly|integer|min:1|max:31',
            'active'                     => 'boolean',
            'sponsorship_type'           => 'nullable|in:none,monthly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id'   => 'nullable|exists:beneficiaries,id',
            'sponsored_beneficiary_ids'  => 'nullable|array',
            'sponsored_beneficiary_ids.*'=> 'integer|distinct|exists:beneficiaries,id',
            'sync_sponsored_beneficiaries' => 'nullable|boolean',
            'sponsored_family_member_ids' => 'nullable|array',
            'sponsored_family_member_ids.*' => 'integer|distinct|exists:beneficiary_family_members,id',
            'sync_sponsored_family_members' => 'nullable|boolean',
            'monthly_allocation_target'  => 'nullable|string|max:500',
            'sponsorship_project_id'     => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric'
        ];
    }

    protected function prepareForValidation(): void
    {
        $isMonthly = $this->input('classification') === 'recurring'
            && $this->input('recurring_cycle') === 'monthly';

        if ($this->has('name')) {
            $this->merge([
                'name' => is_string($this->input('name'))
                    ? preg_replace('/\s+/u', ' ', trim($this->input('name')))
                    : $this->input('name'),
            ]);
        }

        if ($this->has('active')) {
            $this->merge([
                'active' => $this->boolean('active'),
            ]);
        }

        $this->merge([
            'sponsorship_type' => $isMonthly ? 'monthly_sponsor' : ($this->input('sponsorship_type') ?: 'none'),
            'sync_sponsored_beneficiaries' => $this->boolean('sync_sponsored_beneficiaries'),
            'sync_sponsored_family_members' => $this->boolean('sync_sponsored_family_members'),
            'monthly_allocation_target' => is_string($this->input('monthly_allocation_target'))
                ? trim($this->input('monthly_allocation_target'))
                : $this->input('monthly_allocation_target'),
            'sponsored_beneficiary_ids' => $isMonthly
                ? (array) $this->input('sponsored_beneficiary_ids', [])
                : [],
            'sponsored_family_member_ids' => $isMonthly
                ? (array) $this->input('sponsored_family_member_ids', [])
                : [],
        ]);

        if (! $isMonthly) {
            $this->merge([
                'monthly_allocation_target' => null,
                'monthly_donation_day' => null,
            ]);
        }
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $isMonthly = $this->input('classification') === 'recurring'
                && $this->input('recurring_cycle') === 'monthly';

            if (! $isMonthly) {
                return;
            }

            $beneficiaryIds = array_filter((array) $this->input('sponsored_beneficiary_ids', []));
            $familyMemberIds = array_filter((array) $this->input('sponsored_family_member_ids', []));
            if ($beneficiaryIds === [] && $familyMemberIds === [] && ! $this->filled('monthly_allocation_target')) {
                $validator->errors()->add(
                    'monthly_allocation_target',
                    'اختر طفلًا أو أكثر، أو اكتب وجهة التبرع الشهري إذا لم يكن محددًا لطفل.'
                );
            }
        }];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم المتبرع.',
            'name.unique' => 'اسم المتبرع مسجل مسبقاً، ولا يمكن تكرار اسم المتبرع.',
        ];
    }
}
