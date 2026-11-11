<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Beneficiary;
use App\Rules\UniqueBeneficiaryName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasExisting = $this->filled('existing_beneficiary_id');
        $existingId = $hasExisting ? (int) $this->input('existing_beneficiary_id') : null;
        $isGuestHouse = $this->input('form_context') === 'guest_house_beneficiary' || $this->has('treatment_type') || $hasExisting;

        return [
            'existing_beneficiary_id'   => 'nullable|exists:beneficiaries,id',
            'code'                      => [
                'nullable',
                'string',
                'max:50',
                $existingId
                    ? Rule::unique('beneficiaries', 'code')->ignore($existingId)
                    : Rule::unique('beneficiaries', 'code'),
            ],
            'classification'            => 'nullable|string|in:A,B,a,b',
            'full_name'                 => $hasExisting 
                                            ? 'nullable|string|max:255' 
                                            : ['required', 'string', 'max:255', new UniqueBeneficiaryName()],
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
            'assistance_type'           => $isGuestHouse 
                                            ? 'nullable|string|in:monthly,one_time,in_kind,service,financial' 
                                            : 'required|string|in:monthly,one_time,in_kind,service,financial',
            'collection_day'            => 'nullable|integer|min:1|max:31',
            'collection_method'         => 'nullable|string|max:191',
            'status'                    => 'nullable|string|in:pending,new,under_review,accepted,rejected,archived_improved,archived_deceased',
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
            'allocated_beneficiary_ids.*' => 'integer|distinct|exists:beneficiaries,id',
            'treatment_type'        => 'nullable|in:chemotherapy,radiation,other',
            'medical_center'        => 'nullable|string|max:255',
            'sessions_count'        => 'nullable|integer|min:1|max:1000',
            'gender'                => 'nullable|string|max:50',
            'medical_notes'         => 'nullable|string|max:2000',
            'companion_name'        => 'nullable|string|max:255',
            'companion_relation'    => 'nullable|string|max:100',
            'companion_national_id' => 'nullable|string|max:20',
            'companion_phone'       => 'nullable|string|max:25',
            'companion_address'     => 'nullable|string|max:500',
            'companion_id_front'    => 'nullable|any_image|max:51200',
            'companion_id_back'     => 'nullable|any_image|max:51200',
            'manager_notes'         => 'nullable|string|max:4000',
            'patient_id_front'      => 'nullable|any_image|max:51200',
            'patient_id_back'       => 'nullable|any_image|max:51200',
            'followup_card'         => 'nullable|file|max:51200',
            'referral_letter'       => 'nullable|file|max:51200',
            'guest_house_bed_id'    => 'nullable|exists:guest_house_beds,id',
            'arrival_date'          => 'nullable|date',
            'expected_days'         => 'nullable|integer|min:1|max:365',
            'attachments'           => 'nullable|array',
            'attachments.*'         => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv,zip|max:51200',
            'attachment_titles'     => 'nullable|array',
            'attachment_titles.*'   => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('full_name'))) {
            $this->merge([
                'full_name' => trim(preg_replace('/\s+/u', ' ', $this->input('full_name')) ?? $this->input('full_name')),
            ]);
        }

        if (is_string($this->input('code'))) {
            $this->merge([
                'code' => trim($this->input('code')),
            ]);
        }

        if (! $this->filled('allocation_type')) {
            $this->merge(['allocated_beneficiary_ids' => []]);
        }

        // في نموذج دار الضيافة: الحفاظ التام على كود المستفيد الأصلي وعدم تغييره، ومنع تعارض الكود التلقائي
        $isGuestHouseForm = $this->input('form_context') === 'guest_house_beneficiary' || $this->has('guest_house_bed_id') || $this->has('treatment_type');
        if ($isGuestHouseForm) {
            if (! $this->filled('existing_beneficiary_id')) {
                $matchedBeneficiary = null;
                $nidInput = trim((string) $this->input('national_id', ''));
                $nameInput = trim((string) $this->input('full_name', ''));
                $codeInput = trim((string) $this->input('code', ''));

                if ($nidInput !== '' && strlen($nidInput) >= 10) {
                    $matchedBeneficiary = Beneficiary::query()
                        ->where('national_id', $nidInput)
                        ->first();
                }

                if (! $matchedBeneficiary && $nameInput !== '') {
                    $matchedBeneficiary = Beneficiary::query()
                        ->where('full_name', $nameInput)
                        ->orWhere('patient_name', $nameInput)
                        ->first();
                    if (! $matchedBeneficiary) {
                        $dupId = UniqueBeneficiaryName::existingBeneficiaryId(UniqueBeneficiaryName::normalize($nameInput));
                        if ($dupId) {
                            $matchedBeneficiary = Beneficiary::find($dupId);
                        }
                    }
                }

                if (! $matchedBeneficiary && $codeInput !== '' && ! str_starts_with($codeInput, 'G-')) {
                    $matchedBeneficiary = Beneficiary::query()
                        ->where('code', $codeInput)
                        ->orWhere('patient_code', $codeInput)
                        ->first();
                }

                if ($matchedBeneficiary) {
                    $this->merge([
                        'existing_beneficiary_id' => $matchedBeneficiary->id,
                        'code' => $matchedBeneficiary->code ?: $codeInput,
                    ]);
                } elseif ($codeInput !== '' && Beneficiary::where('code', $codeInput)->exists()) {
                    // إذا كان الكود التلقائي G-XXXXX محجوزاً بالفعل لحالة أخرى، يتم توليد الكود التالي المتاح تلقائياً
                    $this->merge([
                        'code' => Beneficiary::nextGuestCode(),
                    ]);
                }
            } else {
                $existingBen = Beneficiary::find((int) $this->input('existing_beneficiary_id'));
                if ($existingBen && !empty($existingBen->code)) {
                    // الحفاظ على كود المستفيد الأصلي كما هو دون أي تغيير عند دخوله نزيل بالدار
                    $this->merge([
                        'code' => $existingBen->code,
                    ]);
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'code.unique'                        => 'كود المستفيد مسجل بالفعل لحالة أخرى.',
            'project_id.prohibits'               => 'يمكن ربط المستفيد بجهة واحدة فقط.',
            'campaign_id.prohibits'              => 'يمكن ربط المستفيد بجهة واحدة فقط.',
            'guest_house_id.prohibits'           => 'يمكن ربط المستفيد بجهة واحدة فقط.',
            'allocated_beneficiary_ids.required' => 'يرجى اختيار المستفيد أو المستفيدين.',
            'allocated_beneficiary_ids.size'     => 'يجب اختيار مستفيد واحد فقط.',
            'allocated_beneficiary_ids.min'      => 'يجب اختيار مستفيدين على الأقل.',
            'rejection_reason.required'          => 'سبب الرفض مطلوب عند نقل الحالة إلى مرفوض.',
            'archived_reason.required'           => 'سبب الأرشفة مطلوب للحالات المؤرشفة.',
            'family_members.max'                 => 'يمكن إضافة الزوج/الزوجة وحتى 5 أطفال.',
        ];
    }
}
