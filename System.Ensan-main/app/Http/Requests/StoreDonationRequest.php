<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'estimated_value'          => 'required_if:type,in_kind|nullable|numeric|min:0.01',
            'project_id'               => 'nullable|exists:projects,id',
            'campaign_id'              => 'nullable|exists:campaigns,id',
            'guest_house_id'           => 'nullable|exists:guest_houses,id',
            'warehouse_id'             => 'required_if:type,in_kind|exists:warehouses,id',
            'treasury_id'              => 'required_if:type,cash|exists:treasuries,id',
            'delegate_id'              => 'nullable|exists:delegates,id',
            'route_id'                 => 'nullable|exists:travel_routes,id',
            'allocation_note'          => 'nullable|string',
            'allocation_type'          => 'nullable|string',
            'sponsorship_kind'         => 'nullable|string',
            'beneficiary_id'           => 'nullable|exists:beneficiaries,id',
            'received_at'              => 'nullable|date'
        ];
    }

    public function messages(): array
    {
        return [
            'donor_id.required_without'        => 'يرجى اختيار متبرع مسجل أو إدخال بيانات متبرع جديد.',
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
            'warehouse_id.required_if'         => 'مطلوب تحديد المخزن للتبرع العيني.',
        ];
    }
}
