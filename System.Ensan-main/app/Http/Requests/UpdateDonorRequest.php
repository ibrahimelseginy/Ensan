<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class UpdateDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => 'sometimes|string|max:255',
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
}
