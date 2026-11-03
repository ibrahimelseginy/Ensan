<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Donor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donor_id'                 => 'nullable|exists:donors,id|required_without:new_donor_name',
            'new_donor_code'           => 'nullable|string|max:191|regex:/^[A-Z0-9_-]+$/|unique:donors,code',
            'new_donor_name'           => 'nullable|required_without:donor_id|string|min:3|unique:donors,name|regex:/^[\p{L}\s\.]+$/u',
            'new_donor_phone'          => 'nullable|required_with:new_donor_name|string|unique:donors,phone|regex:/^(01[0125][0-9]{8})$/',
            'new_donor_address'        => 'nullable|string',
            'new_donor_classification' => 'nullable|in:one_time,recurring',
            'new_donor_cycle'          => 'nullable|in:monthly,yearly',
            'new_donor_monthly_day'    => 'nullable|integer|min:1|max:31',
            'new_donor_sponsorship_amount' => 'nullable|numeric|min:0',
            'sponsorship_beneficiary_id'   => 'nullable|exists:beneficiaries,id',
            'paid_months'              => 'nullable|array',
            'paid_months.*'            => 'nullable|string',
            'type'                     => 'required|in:cash,in_kind',
            'cash_channel'             => 'required_if:type,cash|in:cash,instapay,vodafone_cash,bank_transfer,delegate',
            'operation_number'         => 'nullable|string|max:191',
            'transfer_receipt'         => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
            'amount'                   => 'required_if:type,cash|nullable|numeric|min:0.01',
            'currency'                 => 'nullable|string',
            'receipt_number'           => 'required_if:type,cash|nullable|string|max:64|unique:donations,receipt_number',
            'purpose'                  => 'required|in:kafalat_aytam,kafalat_awram,sadaqat,zakat_maal,sadaqa_jariya',
            'estimated_value'          => 'required_if:type,in_kind|nullable|numeric|min:0.01',
            'project_id'               => 'nullable|exists:projects,id',
            'campaign_id'              => 'nullable|exists:campaigns,id',
            'guest_house_id'           => 'nullable|exists:guest_houses,id',
            'add_to_inventory'          => 'nullable|boolean',
            'warehouse_id'              => 'nullable|required_if:add_to_inventory,1|exists:warehouses,id',
            'item_id'                   => 'nullable|required_if:add_to_inventory,1|exists:items,id',
            'quantity'                  => 'nullable|required_if:add_to_inventory,1|numeric|min:0.001',
            'treasury_id'              => 'required_if:type,cash|exists:treasuries,id',
            'delegate_id'              => 'nullable|exists:delegates,id',
            'route_id'                 => 'nullable|exists:travel_routes,id',
            'allocation_note'          => 'nullable|string',
            'allocation_type'          => 'nullable|string',
            'sponsorship_kind'         => 'nullable|string',
            'beneficiary_id'           => 'nullable|exists:beneficiaries,id',
            'beneficiary_allocations'  => 'nullable|array',
            'beneficiary_allocations.*.beneficiary_id' => 'required_with:beneficiary_allocations|exists:beneficiaries,id',
            'beneficiary_allocations.*.family_member_id' => 'nullable|exists:beneficiary_family_members,id',
            'beneficiary_allocations.*.amount'         => 'nullable|numeric|min:0',
            'beneficiary_allocations.*.notes'          => 'nullable|string',
            'split_allocations'        => 'nullable|array',
            'split_allocations.*.type' => 'required_with:split_allocations|in:project,campaign,guest_house,beneficiary,general',
            'split_allocations.*.target_id' => 'nullable',
            'split_allocations.*.purpose'   => 'nullable|string',
            'split_allocations.*.amount'    => 'required_with:split_allocations|numeric|min:0.01',
            'split_allocations.*.notes'     => 'nullable|string',
            'beneficiary_ids'          => 'nullable|required_with:sponsorship_kind|array|min:1',
            'beneficiary_ids.*'        => 'nullable|exists:beneficiaries,id',
            'family_member_id'         => [
                'nullable',
                'integer',
                'exists:beneficiary_family_members,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $value) {
                        return;
                    }

                    $beneficiaryIds = array_values(array_filter(array_map(
                        'intval',
                        (array) $this->input('beneficiary_ids', [])
                    )));
                    if ($this->filled('beneficiary_id')) {
                        $beneficiaryIds[] = $this->integer('beneficiary_id');
                    }

                    if (! \App\Models\BeneficiaryFamilyMember::query()
                        ->whereKey($value)
                        ->whereIn('beneficiary_id', array_unique($beneficiaryIds))
                        ->exists()) {
                        $fail('فرد الأسرة المختار لا يتبع المستفيدين المحددين.');
                    }
                },
            ],
            'family_member_ids'        => [
                'nullable',
                Rule::requiredIf(function (): bool {
                    $purpose = (string) $this->input('purpose');
                    if (! in_array($purpose, ['kafalat_aytam', 'kafalat_awram'], true)) {
                        return false;
                    }

                    $hasAllocations = ! empty(array_filter((array) $this->input('beneficiary_allocations', [])));
                    $hasBeneficiary = $this->filled('beneficiary_id') || ! empty(array_filter((array) $this->input('beneficiary_ids', [])));

                    return ! $hasAllocations && ! $hasBeneficiary;
                }),
                'array',
                Rule::when(
                    fn (): bool => in_array($this->input('purpose'), ['kafalat_aytam', 'kafalat_awram'], true)
                        && empty(array_filter((array) $this->input('beneficiary_allocations', [])))
                        && ! $this->filled('beneficiary_id')
                        && empty(array_filter((array) $this->input('beneficiary_ids', []))),
                    ['min:1']
                ),
            ],
            'family_member_ids.*'      => [
                'integer',
                'distinct',
                'exists:beneficiary_family_members,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $purpose = (string) $this->input('purpose');

                    $member = \App\Models\BeneficiaryFamilyMember::query()
                        ->whereKey((int) $value)
                        ->where('active', true)
                        ->first();

                    if (! $member) {
                        $fail('ملف الطفل أو المريض غير موجود أو غير نشط.');
                        return;
                    }

                    if ($purpose === 'kafalat_aytam' && $member->is_patient && ! in_array($member->relationship, ['child', 'son', 'daughter', 'orphan', 'ابن', 'ابنة', 'طفل', 'يتيم'], true)) {
                        $fail('كفالات الأيتام تقبل ملفات الأطفال المكفولين فقط.');
                    }

                    if ($purpose === 'kafalat_awram' && ! $member->is_patient && ! in_array($member->relationship, ['patient', 'child', 'son', 'daughter', 'orphan', 'ابن', 'ابنة', 'طفل', 'مريض'], true)) {
                        $fail('كفالات الأورام تقبل ملفات المرضى والأطفال المكفولين فقط.');
                    }
                },
            ],
            'received_at'              => 'nullable|date'
        ];
    }

    public function messages(): array
    {
        return [
            'donor_id.required_without'        => 'يرجى اختيار متبرع مسجل أو إدخال بيانات متبرع جديد.',
            'new_donor_code.regex'             => 'كود المتبرع يقبل الحروف الإنجليزية والأرقام والشرطة فقط.',
            'new_donor_code.unique'            => 'كود المتبرع مستخدم بالفعل، اختر كودًا آخر.',
            'new_donor_name.required_without'  => 'اسم المتبرع الجديد مطلوب عند عدم اختيار متبرع مسجل.',
            'new_donor_name.unique'            => 'اسم المتبرع هذا مسجل مسبقاً، يرجى اختياره من القائمة.',
            'new_donor_name.min'               => 'اسم المتبرع يجب أن يكون 3 أحرف على الأقل.',
            'new_donor_name.regex'             => 'اسم المتبرع يجب أن يحتوي على أحرف فقط.',
            'new_donor_phone.unique'           => 'رقم الهاتف هذا مسجل مسبقاً لمتبرع آخر.',
            'new_donor_phone.regex'            => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
            'receipt_number.required_if'       => 'رقم الإيصال مطلوب للتبرعات النقدية.',
            'receipt_number.unique'            => 'رقم الإيصال هذا مسجل مسبقاً لتبرع آخر.',
            'treasury_id.required_if'          => 'يرجى اختيار الخزينة للتبرع النقدي.',
            'amount.required_if'               => 'مطلوب مبلغ للتبرع النقدي.',
            'amount.min'                       => 'المبلغ يجب أن يكون أكبر من صفر.',
            'estimated_value.required_if'      => 'مطلوب قيمة تقديرية للتبرع العيني.',
            'estimated_value.min'              => 'القيمة التقديرية يجب أن تكون أكبر من صفر.',
            'warehouse_id.required_if'         => 'مطلوب تحديد المخزن عند إضافة التبرع للمخزون.',
            'item_id.required_if'              => 'مطلوب تحديد الصنف عند إضافة التبرع للمخزون.',
            'quantity.required_if'             => 'مطلوب تحديد الكمية عند إضافة التبرع للمخزون.',
            'beneficiary_id.required_with'     => 'يرجى البحث عن المستفيد واختياره عند تخصيص التبرع.',
            'beneficiary_id.exists'            => 'المستفيد المختار غير موجود.',
            'beneficiary_ids.required_with'    => 'اختر مستفيدًا واحدًا على الأقل عند تخصيص التبرع.',
            'family_member_id.exists'           => 'ملف فرد الأسرة المختار غير موجود.',
            'purpose.required'                  => 'اختر بند «وذلك قيمة» للإيصال.',
            'purpose.in'                        => 'بند «وذلك قيمة» المختار غير صحيح.',
            'family_member_ids.required'        => 'اختر طفلًا أو مريضًا واحدًا على الأقل من الملفات المكفولة للمتبرع أو حدد حالات في جدول التخصيص.',
            'family_member_ids.min'             => 'اختر ملفًا واحدًا على الأقل.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'add_to_inventory' => $this->boolean('add_to_inventory'),
            'new_donor_code' => Donor::normalizeCode($this->input('new_donor_code')),
        ]);

        $purpose = (string) $this->input('purpose');
        if (in_array($purpose, ['kafalat_aytam', 'kafalat_awram'], true)) {
            $memberIds = array_values(array_filter(array_map('intval', (array) $this->input('family_member_ids', []))));

            // 1. Extract any family_member_id from beneficiary_allocations
            $allocations = (array) $this->input('beneficiary_allocations', []);
            $beneficiaryIds = [];
            foreach ($allocations as $alloc) {
                if (!empty($alloc['family_member_id'])) {
                    $memberIds[] = (int) $alloc['family_member_id'];
                }
                if (!empty($alloc['beneficiary_id'])) {
                    $beneficiaryIds[] = (int) $alloc['beneficiary_id'];
                }
            }

            // 2. Also check direct beneficiary_id and beneficiary_ids
            if ($this->filled('beneficiary_id')) {
                $beneficiaryIds[] = (int) $this->input('beneficiary_id');
            }
            if ($this->filled('beneficiary_ids')) {
                foreach ((array) $this->input('beneficiary_ids') as $bId) {
                    if (!empty($bId)) {
                        $beneficiaryIds[] = (int) $bId;
                    }
                }
            }
            $beneficiaryIds = array_values(array_unique(array_filter($beneficiaryIds)));

            // 3. If no memberIds were provided directly, auto-resolve from beneficiary IDs
            if (empty($memberIds) && !empty($beneficiaryIds)) {
                $query = \App\Models\BeneficiaryFamilyMember::query()
                    ->whereIn('beneficiary_id', $beneficiaryIds)
                    ->where('active', true);

                if ($purpose === 'kafalat_awram') {
                    $resolved = (clone $query)->where('is_patient', true)->pluck('id')->all();
                    if (empty($resolved)) {
                        $resolved = $query->pluck('id')->all();
                    }
                } else {
                    $resolved = (clone $query)->where('is_patient', false)->where('relationship', 'child')->pluck('id')->all();
                    if (empty($resolved)) {
                        $resolved = (clone $query)->where('relationship', 'child')->pluck('id')->all();
                    }
                    if (empty($resolved)) {
                        $resolved = $query->pluck('id')->all();
                    }
                }

                $memberIds = array_merge($memberIds, $resolved);
            }

            $memberIds = array_values(array_unique(array_filter($memberIds)));
            if (!empty($memberIds)) {
                $this->merge(['family_member_ids' => $memberIds]);
            }
        }
    }
}
