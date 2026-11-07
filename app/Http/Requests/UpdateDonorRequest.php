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
            'phone_country'              => 'nullable|string|in:' . implode(',', array_keys(StoreDonationRequest::COUNTRY_PHONE_RULES)),
            'phone'                      => [
                'nullable',
                'string',
                'max:25',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $countryCode = strtoupper((string) $this->input('phone_country', 'EG'));
                    $rule = StoreDonationRequest::COUNTRY_PHONE_RULES[$countryCode] ?? StoreDonationRequest::COUNTRY_PHONE_RULES['EG'];
                    $national = (string) $this->input('_phone_national', '');

                    if (!preg_match('/^[0-9]+$/', $national)) {
                        $fail("رقم الهاتف لدولة {$rule['name']} (+{$rule['dial']}) يجب أن يحتوي على أرقام فقط.");
                        return;
                    }

                    $len = strlen($national);
                    if ($len < $rule['min'] || $len > $rule['max']) {
                        $expectedText = $rule['min'] === $rule['max']
                            ? "{$rule['min']} أرقام" . (!empty($rule['allow_zero']) ? " (أو " . ($rule['max'] + 1) . " بالصفر الابتدائي)" : "")
                            : "من {$rule['min']} إلى {$rule['max']} أرقام";
                        $fail("رقم الهاتف لدولة {$rule['name']} (+{$rule['dial']}) غير مكتمل أو غير صحيح: يجب أن يتكون من {$expectedText}.");
                        return;
                    }

                    if (!empty($rule['regex']) && !preg_match($rule['regex'], $national)) {
                        $fail("بداية رقم الهاتف غير صحيحة لدولة {$rule['name']} (+{$rule['dial']}).");
                    }
                },
            ],
            'national_id'                => 'nullable|string|max:20',
            'email'                      => 'nullable|email',
            'address'                    => 'nullable|string|max:500',
            'classification'             => 'sometimes|in:one_time,recurring',
            'recurring_cycle'            => 'nullable|required_if:classification,recurring|in:monthly,yearly',
            'monthly_donation_day'       => 'nullable|integer|min:1|max:31',
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
        if ($this->has('phone')) {
            $rawPhone = $this->input('phone');
            $countryCode = strtoupper((string) $this->input('phone_country', 'EG'));
            if (!isset(StoreDonationRequest::COUNTRY_PHONE_RULES[$countryCode])) {
                $countryCode = 'EG';
            }
            $rule = StoreDonationRequest::COUNTRY_PHONE_RULES[$countryCode];

            $normalizedPhone = $rawPhone;
            $nationalDigits = '';

            if (is_string($rawPhone) && trim($rawPhone) !== '') {
                $digits = strtr(trim($rawPhone), [
                    '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                    '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                    '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
                    '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
                ]);
                $digits = preg_replace('/[\s\-\(\)]+/', '', $digits);
                if (str_starts_with($digits, '+')) {
                    $digits = substr($digits, 1);
                } elseif (str_starts_with($digits, '00')) {
                    $digits = substr($digits, 2);
                }

                $dial = $rule['dial'];
                if (str_starts_with($digits, $dial) && strlen($digits) > $rule['max']) {
                    $digits = substr($digits, strlen($dial));
                }

                if (!empty($rule['allow_zero']) && str_starts_with($digits, '0') && strlen($digits) === ($rule['max'] + 1)) {
                    $digits = substr($digits, 1);
                } elseif ($countryCode === 'EG' && str_starts_with($digits, '0')) {
                    $digits = substr($digits, 1);
                }

                $nationalDigits = $digits;

                if ($countryCode === 'EG') {
                    $normalizedPhone = '0' . $nationalDigits;
                } else {
                    $normalizedPhone = '+' . $dial . $nationalDigits;
                }
            }

            $this->merge([
                'phone_country' => $countryCode,
                'phone' => $normalizedPhone,
                '_phone_national' => $nationalDigits,
            ]);
        }

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

        if ($isMonthly) {
            $day = $this->input('monthly_donation_day');
            $this->merge([
                'monthly_donation_day' => ($day !== null && $day !== '') ? (int) $day : 1,
            ]);

            $beneficiaryIds = array_filter((array) $this->input('sponsored_beneficiary_ids', []));
            $familyMemberIds = array_filter((array) $this->input('sponsored_family_member_ids', []));
            if (empty($beneficiaryIds) && empty($familyMemberIds) && ! $this->filled('monthly_allocation_target')) {
                $this->merge([
                    'monthly_allocation_target' => 'تبرع شهري عام',
                ]);
            }
        } else {
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
