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
            'type'                     => 'required|in:cash,in_kind',
            'cash_channel'             => 'required_if:type,cash|in:cash,instapay,vodafone_cash,delegate',
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
                Rule::requiredIf(fn (): bool => in_array($this->input('purpose'), ['kafalat_aytam', 'kafalat_awram'], true)),
                'array',
                'min:1',
            ],
            'family_member_ids.*'      => [
                'integer',
                'distinct',
                'exists:beneficiary_family_members,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $donorId = $this->integer('donor_id');
                    $purpose = (string) $this->input('purpose');

                    $member = \App\Models\BeneficiaryFamilyMember::query()
                        ->whereKey((int) $value)
                        ->where('active', true)
                        ->first();

                    if (! $member) {
                        $fail('ملف الطفل أو المريض غير موجود أو غير نشط.');
                        return;
                    }

                    if ($purpose === 'kafalat_aytam' && ($member->is_patient || $member->relationship !== 'child')) {
                        $fail('كفالات الأيتام تقبل ملفات الأطفال المكفولين فقط.');
                    }

                    if ($purpose === 'kafalat_awram' && ! $member->is_patient) {
                        $fail('كفالات الأورام تقبل ملفات المرضى المكفولين فقط.');
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
            'family_member_ids.required'        => 'اختر طفلًا أو مريضًا واحدًا على الأقل من الملفات المكفولة للمتبرع.',
            'family_member_ids.min'             => 'اختر ملفًا واحدًا على الأقل.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'add_to_inventory' => $this->boolean('add_to_inventory'),
            'new_donor_code' => Donor::normalizeCode($this->input('new_donor_code')),
        ]);
    }
}
