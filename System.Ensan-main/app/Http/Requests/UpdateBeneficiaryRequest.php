<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Beneficiary;
use App\Rules\UniqueBeneficiaryName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $beneficiary = $this->route('beneficiary');
        $beneficiaryId = $beneficiary instanceof Beneficiary ? (int) $beneficiary->id : null;

        return [
            'code'                      => 'nullable|string|max:50|unique:beneficiaries,code,' . ($beneficiaryId ?? ''),
            'full_name'                 => [
                'sometimes',
                'string',
                'max:255',
                new UniqueBeneficiaryName(
                    $beneficiaryId,
                    $beneficiary instanceof Beneficiary ? (string) $beneficiary->full_name : null,
                ),
            ],
            'guardian_name'            => 'nullable|string|max:255',
            'patient_name'             => 'nullable|string|max:255',
            'patient_age'              => 'nullable|integer|min:0|max:120',
            'patient_code'             => 'nullable|string|max:191',
            'patient_relationship'     => 'nullable|in:husband,wife,child,patient,other',
            'patient_birth_date'       => 'nullable|date|before_or_equal:today',
            'patient_phone'            => 'nullable|string|max:30',
            'patient_backup_phone'     => 'nullable|string|max:30',
            'national_id'               => 'nullable|string|max:20',
            'visa_card_number'          => 'nullable|string|max:191',
            'phone'                     => 'nullable|string|max:20',
            'backup_phone'              => 'nullable|string|max:20',
            'address'                   => 'nullable|string|max:500',
            'assistance_type'           => 'sometimes|string|in:monthly,one_time,in_kind,service,financial',
            'collection_day'            => 'nullable|integer|min:1|max:31',
            'collection_method'         => 'nullable|string|max:191',
            'status'                    => 'sometimes|string|in:pending,new,under_review,accepted,rejected,archived_improved,archived_deceased',
            'project_id'                => 'nullable|exists:projects,id',
            'campaign_id'               => 'nullable|exists:campaigns,id',
            'guest_house_id'            => 'nullable|exists:guest_houses,id',
            'notes'                     => 'nullable|string',
            'rejection_reason'          => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('status') === 'rejected'),
                'string',
                'max:2000',
            ],
            'archived_reason'           => [
                'nullable',
                Rule::requiredIf(fn () => in_array($this->input('status'), ['archived_improved', 'archived_deceased'], true)),
                'string',
                'max:2000',
            ],
            'family_members_data'       => 'nullable|array',
            'family_members'            => 'nullable|array|max:6',
            'family_members.*.id'       => 'nullable|integer|exists:beneficiary_family_members,id',
            'family_members.*.relationship' => 'nullable|in:husband,wife,child,other',
            'family_members.*.full_name' => 'nullable|string|max:255',
            'family_members.*.birth_date' => 'nullable|date|before_or_equal:today',
            'family_members.*.age'      => 'nullable|integer|min:0|max:120',
            'family_members.*.code'     => 'nullable|string|max:64',
            'family_members.*.national_id' => 'nullable|string|max:20',
            'family_members.*.phone'    => 'nullable|string|max:30',
            'family_members.*.backup_phone' => 'nullable|string|max:30',
            'family_members.*.sponsorship_amount' => 'nullable|numeric|min:0',
            'family_members.*.education_level' => 'nullable|string|max:255',
            'family_members.*.case_details' => 'nullable|string|max:2000',
            'monthly_sponsorship_amount'=> 'nullable|numeric',
            'brothers_count'            => 'nullable|integer',
            'adult_children_count'      => 'nullable|integer',
            'adult_children_ages'       => 'nullable|string',
            'notes_cases'               => 'nullable|string|max:5000',
            'sponsorship_scope_type'    => 'nullable|string',
            'allocation_type'           => 'nullable|in:شخص واحد,أكثر من مستفيد',
            'allocated_beneficiary_ids' => [
                'nullable',
                Rule::requiredIf(fn () => $this->filled('allocation_type')),
                'array',
                Rule::when($this->input('allocation_type') === 'شخص واحد', ['size:1']),
                Rule::when($this->input('allocation_type') === 'أكثر من مستفيد', ['min:2']),
            ],
            'allocated_beneficiary_ids.*' => [
                'integer',
                'distinct',
                'exists:beneficiaries,id',
                Rule::notIn(array_filter([$beneficiaryId])),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('full_name'))) {
            $this->merge([
                'full_name' => trim(preg_replace('/\s+/u', ' ', $this->input('full_name')) ?? $this->input('full_name')),
            ]);
        }

        if (! $this->filled('allocation_type')) {
            $this->merge(['allocated_beneficiary_ids' => []]);
        }

    }

    public function messages(): array
    {
        return [
            'allocated_beneficiary_ids.required' => 'يرجى اختيار المستفيد أو المستفيدين.',
            'allocated_beneficiary_ids.size'     => 'يجب اختيار مستفيد واحد فقط.',
            'allocated_beneficiary_ids.min'      => 'يجب اختيار مستفيدين على الأقل.',
            'allocated_beneficiary_ids.*.not_in' => 'لا يمكن ربط المستفيد بنفسه.',
            'rejection_reason.required'           => 'سبب الرفض مطلوب عند نقل الحالة إلى مرفوض.',
            'archived_reason.required'            => 'سبب الأرشفة مطلوب للحالات المؤرشفة.',
            'family_members.max'                  => 'يمكن إضافة الزوج/الزوجة وحتى 5 أطفال.',
        ];
    }
}
