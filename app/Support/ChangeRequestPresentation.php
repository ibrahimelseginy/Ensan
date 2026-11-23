<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ChangeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class ChangeRequestPresentation
{
    /** @return array{label: string, icon: string, color: string} */
    public static function modelInfo(ChangeRequest $request): array
    {
        $name = class_basename($request->model_type);

        return [
            'Beneficiary' => ['label' => 'مستفيد', 'icon' => 'people-fill', 'color' => 'info'],
            'Donation' => ['label' => 'تبرع', 'icon' => 'cash-coin', 'color' => 'success'],
            'Expense' => ['label' => 'مصروف', 'icon' => 'receipt', 'color' => 'danger'],
            'Delegate' => ['label' => 'مندوب', 'icon' => 'person-badge', 'color' => 'warning'],
            'DelegateTrip' => ['label' => 'مشوار مندوب', 'icon' => 'truck', 'color' => 'info'],
            'Campaign' => ['label' => 'حملة', 'icon' => 'megaphone-fill', 'color' => 'primary'],
            'Project' => ['label' => 'مشروع', 'icon' => 'briefcase-fill', 'color' => 'primary'],
            'User' => ['label' => 'مستخدم', 'icon' => 'person-circle', 'color' => 'primary'],
            'Role' => ['label' => 'دور وصلاحيات', 'icon' => 'shield-lock-fill', 'color' => 'warning'],
            'Task' => ['label' => 'مهمة', 'icon' => 'list-check', 'color' => 'primary'],
            'Donor' => ['label' => 'متبرع', 'icon' => 'heart-fill', 'color' => 'danger'],
            'BeneficiaryFamilyMember' => ['label' => 'فرد أسرة / يتيم', 'icon' => 'person-heart', 'color' => 'info'],
            'EmployeeAttendance' => ['label' => 'حضور وانصراف موظف', 'icon' => 'calendar-check-fill', 'color' => 'success'],
            'AttendanceDevice' => ['label' => 'جهاز حضور معتمد', 'icon' => 'laptop', 'color' => 'primary'],
            'Volunteer' => ['label' => 'متطوع', 'icon' => 'hand-thumbs-up-fill', 'color' => 'primary'],
            'VolunteerAttendance' => ['label' => 'حضور متطوع', 'icon' => 'clock-fill', 'color' => 'info'],
            'VolunteerHour' => ['label' => 'ساعات تطوع', 'icon' => 'hourglass-split', 'color' => 'warning'],
            'FieldVisit' => ['label' => 'بحث ميداني', 'icon' => 'clipboard2-pulse-fill', 'color' => 'primary'],
            'TreasuryTransaction' => ['label' => 'حركة خزينة', 'icon' => 'cash-stack', 'color' => 'success'],
            'GuestHouse' => ['label' => 'دار ضيافة', 'icon' => 'house-heart-fill', 'color' => 'info'],
            'GuestHouseBed' => ['label' => 'سرير دار ضيافة', 'icon' => 'hospital', 'color' => 'info'],
            'Treasury' => ['label' => 'خزنة', 'icon' => 'safe2-fill', 'color' => 'success'],
            'Account' => ['label' => 'حساب مالي', 'icon' => 'bank', 'color' => 'secondary'],
            'JournalEntry' => ['label' => 'قيد يومية', 'icon' => 'journal-text', 'color' => 'secondary'],
            'Leave' => ['label' => 'إجازة / إذن', 'icon' => 'calendar-event', 'color' => 'warning'],
            'FinancialClosure' => ['label' => 'إغلاق مالي', 'icon' => 'file-lock2-fill', 'color' => 'danger'],
            'Warehouse' => ['label' => 'مخزن', 'icon' => 'boxes', 'color' => 'warning'],
            'Supplier' => ['label' => 'مورد', 'icon' => 'building', 'color' => 'secondary'],
            'Purchase' => ['label' => 'مشتريات', 'icon' => 'cart-check-fill', 'color' => 'primary'],
            'Item' => ['label' => 'صنف مخزني', 'icon' => 'box-seam', 'color' => 'secondary'],
            'Payroll' => ['label' => 'راتب', 'icon' => 'wallet2', 'color' => 'success'],
            'TravelRoute' => ['label' => 'خط سير', 'icon' => 'map', 'color' => 'dark'],
            'KafrElSheikhService' => ['label' => 'خدمة كفر الشيخ', 'icon' => 'tools', 'color' => 'info'],
            'KafrElSheikhBroker' => ['label' => 'وسيط كفر الشيخ', 'icon' => 'person-vcard', 'color' => 'info'],
            'KafrElSheikhDelivery' => ['label' => 'توصيل كفر الشيخ', 'icon' => 'bicycle', 'color' => 'info'],
            'Workspace' => ['label' => 'مساحة عمل', 'icon' => 'buildings', 'color' => 'primary'],
        ][$name] ?? ['label' => Str::headline($name), 'icon' => 'box-seam', 'color' => 'secondary'];
    }

    /** @return array{label: string, verb: string, icon: string, color: string, effect: string} */
    public static function actionInfo(ChangeRequest $request): array
    {
        $model = self::modelInfo($request)['label'];

        return [
            'create' => [
                'label' => 'إضافة',
                'verb' => "إضافة {$model} جديد",
                'icon' => 'plus-circle-fill',
                'color' => 'success',
                'effect' => "عند الموافقة سيتم إنشاء {$model} جديد بالبيانات الموضحة أدناه.",
            ],
            'update' => [
                'label' => 'تعديل',
                'verb' => "تعديل بيانات {$model}",
                'icon' => 'pencil-square',
                'color' => 'primary',
                'effect' => 'عند الموافقة ستُستبدل القيم الحالية بالقيم الجديدة الموضحة أدناه.',
            ],
            'delete' => [
                'label' => 'حذف',
                'verb' => "حذف {$model}",
                'icon' => 'trash3-fill',
                'color' => 'danger',
                'effect' => "عند الموافقة سيتم حذف {$model} من النظام.",
            ],
            'cancel' => [
                'label' => 'إلغاء',
                'verb' => "إلغاء {$model}",
                'icon' => 'x-octagon-fill',
                'color' => 'warning',
                'effect' => "عند الموافقة سيتم إلغاء {$model} وتطبيق الآثار المرتبطة به.",
            ],
        ][$request->action] ?? [
            'label' => $request->action,
            'verb' => "{$request->action} {$model}",
            'icon' => 'gear-fill',
            'color' => 'secondary',
            'effect' => 'عند الموافقة سيتم تنفيذ هذا الإجراء.',
        ];
    }

    public static function subjectName(ChangeRequest $request): string
    {
        $subject = $request->model_id ? $request->subject : null;

        if ($subject instanceof Model) {
            foreach (['full_name', 'name', 'title', 'description', 'code'] as $attribute) {
                $value = $subject->getAttribute($attribute);
                if (filled($value)) {
                    return (string) $value;
                }
            }

            if (filled($subject->getAttribute('amount'))) {
                return number_format((float) $subject->getAttribute('amount'), 2) . ' ج.م';
            }
        }

        $payload = self::payload($request);
        foreach (['full_name', 'name', 'title', 'description', 'code', 'email', 'phone'] as $key) {
            if (filled($payload[$key] ?? null)) {
                return (string) $payload[$key];
            }
        }

        return $request->model_id
            ? self::modelInfo($request)['label'] . ' #' . $request->model_id
            : self::modelInfo($request)['label'] . ' جديد';
    }

    /** @return array<string, mixed> */
    public static function payload(ChangeRequest $request): array
    {
        $payload = is_array($request->payload) ? $request->payload : [];

        if (isset($payload['__is_wrapped']) || isset($payload['data'])) {
            return is_array($payload['data'] ?? null) ? $payload['data'] : [];
        }

        return $payload;
    }

    /** @return array<string, array{from: mixed, to: mixed}> */
    public static function diff(ChangeRequest $request): array
    {
        $diff = is_array($request->payload) ? ($request->payload['diff'] ?? []) : [];

        return is_array($diff) ? $diff : [];
    }

    /**
     * @return array<int, array{key: string, label: string, value: string, multiline: bool}>
     */
    public static function fields(ChangeRequest $request): array
    {
        $rows = [];

        foreach (self::payload($request) as $key => $value) {
            if (self::shouldHide((string) $key, $value)) {
                continue;
            }

            $formatted = self::formatValue((string) $key, $value);
            $rows[] = [
                'key' => (string) $key,
                'label' => self::fieldLabel((string) $key),
                'value' => $formatted,
                'multiline' => str_contains($formatted, "\n"),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{key: string, label: string, from: string, to: string, multiline: bool}>
     */
    public static function changes(ChangeRequest $request): array
    {
        $rows = [];

        foreach (self::diff($request) as $key => $values) {
            if ($key === 'password' || ! is_array($values)) {
                continue;
            }

            $from = self::formatValue((string) $key, $values['from'] ?? null);
            $to = self::formatValue((string) $key, $values['to'] ?? null);
            $rows[] = [
                'key' => (string) $key,
                'label' => self::fieldLabel((string) $key),
                'from' => $from,
                'to' => $to,
                'multiline' => str_contains($from, "\n") || str_contains($to, "\n"),
            ];
        }

        return $rows;
    }

    public static function fieldLabel(string $key): string
    {
        return [
            'name' => 'الاسم',
            'full_name' => 'الاسم الكامل',
            'title' => 'العنوان',
            'amount' => 'المبلغ',
            'type' => 'النوع',
            'service_type' => 'نوع الخدمة',
            'cash_channel' => 'طريقة الدفع',
            'payment_method' => 'طريقة الدفع',
            'description' => 'الوصف',
            'notes' => 'الملاحظات',
            'phone' => 'رقم الهاتف',
            'email' => 'البريد الإلكتروني',
            'address' => 'العنوان',
            'status' => 'الحالة',
            'category' => 'التصنيف',
            'date' => 'التاريخ',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'received_at' => 'تاريخ الاستلام',
            'reason' => 'السبب',
            'quantity' => 'الكمية',
            'estimated_value' => 'القيمة التقديرية',
            'salary' => 'الراتب',
            'allowances' => 'البدلات',
            'active' => 'نشط',
            'is_active' => 'الحالة',
            'is_employee' => 'موظف',
            'is_volunteer' => 'متطوع',
            'department' => 'القسم',
            'job_title' => 'المسمى الوظيفي',
            'join_date' => 'تاريخ الانضمام',
            'governorate' => 'المحافظة',
            'city' => 'المدينة',
            'branch' => 'الفرع',
            'project_role' => 'الدور في المشروع',
            'project_id' => 'المشروع',
            'campaign_id' => 'الحملة',
            'donor_id' => 'المتبرع',
            'delegate_id' => 'المندوب',
            'warehouse_id' => 'المخزن',
            'item_id' => 'الصنف',
            'treasury_id' => 'الخزنة',
            'guest_house_id' => 'دار الضيافة',
            'role_id' => 'الدور',
            'user_id' => 'المستخدم',
            'parent_id' => 'الحساب الأب',
            'permissions' => 'الصلاحيات',
            'allocated_beneficiary_ids' => 'المستفيدون المختارون',
            'sponsored_beneficiary_ids' => 'الحالات والأسر المكفولة',
            'direct_sponsored_beneficiary_ids' => 'الحالات المكفولة مباشرة',
            'sponsored_family_member_ids' => 'الأيتام وأفراد الأسرة المكفولين',
            'sponsorship_beneficiary_amounts' => 'توزيع مبالغ كفالة الأسر',
            'sponsorship_family_member_amounts' => 'توزيع مبالغ كفالة الأيتام',
            'sponsorship_monthly_amount' => 'إجمالي مبلغ الكفالة الشهري',
            'monthly_sponsorship_amount' => 'مبلغ الكفالة الشهري',
            'sponsored_beneficiary_id' => 'الحالة المكفولة الأساسية',
            'sponsorship_type' => 'نوع الكفالة',
            'sponsorship_scope_type' => 'نطاق الكفالة',
            'sponsorship_project_id' => 'مشروع الكفالة المرتبط',
            'monthly_donation_day' => 'يوم التبرع الشهري',
            'recurring_cycle' => 'دورية التكرار',
            'monthly_allocation_target' => 'وجهة التبرع الشهري',
            'sync_sponsored_beneficiaries' => 'تحديث كفالات الحالات والأسر',
            'sync_sponsored_family_members' => 'تحديث كفالات الأيتام وأفراد الأسرة',
            'sponsor_ids' => 'الكفلاء المختارون',
            'allocation_type' => 'نوع تخصيص المستفيدين',
            'child_sponsorship_type' => 'نوع كفالة الطفل',
            'allocation_note' => 'توجيه التبرع',
            'receipt_number' => 'رقم الإيصال',
            'code' => 'الكود',
            'entry_type' => 'نوع القيد',
            'lines' => 'سطور القيد',
            'check_in_at' => 'وقت الحضور',
            'check_out_at' => 'وقت الانصراف',
            'late_minutes' => 'دقائق التأخير',
            'is_late' => 'متأخر عن العمل',
            'delay_text' => 'وصف الحضور / التأخير',
            'device_name' => 'اسم الجهاز',
            'device_token' => 'معرف الجهاز',
            'ip_address' => 'عنوان IP',
            'user_agent' => 'المتصفح / النظام',
            'is_approved' => 'حالة الاعتماد',
            'evaluation_notes' => 'ملاحظات التقييم',
            'rating' => 'التقييم',
            'locked' => 'حالة الإقفال',
            'rejection_reason' => 'سبب الرفض',
            'review_notes' => 'ملاحظات المراجعة',
            'reviewed_at' => 'تاريخ المراجعة',
            'donor_name' => 'اسم المتبرع',
            'donor_phone' => 'هاتف المتبرع',
            'patient_name' => 'اسم المريض',
            'patient_code' => 'كود المريض',
            'patient_age' => 'سن المريض',
            'patient_phone' => 'هاتف المريض',
            'patient_relationship' => 'صلة قرابة المريض',
            'notes_cases' => 'تشخيص الحالة والتقرير الطبي',
            'marital_status' => 'الحالة الاجتماعية',
            'health_status' => 'الحالة الصحية',
            'housing_type' => 'نوع المسكن',
            'home_ownership' => 'ملكية السكن',
            'family_members_count' => 'عدد أفراد الأسرة',
            'income_source' => 'مصدر الدخل',
            'monthly_income' => 'الدخل الشهري',
            'assistance_type' => 'نوع المساعدة',
            'assistance_amount' => 'مبلغ المساعدة',
            'collection_method' => 'طريقة استلام الكفالة',
            'collection_day' => 'يوم استلام الكفالة',
        ][$key] ?? Str::headline($key);
    }

    public static function formatValue(string $key, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'فارغ';
        }

        if ($key === 'password' || (is_string($value) && str_starts_with($value, '$2y$'))) {
            return 'بيانات مشفرة';
        }

        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        if (is_array($value)) {
            $resolved = self::resolveList($key, $value);
            if ($resolved !== null) {
                return implode('، ', $resolved);
            }

            if (array_is_list($value) && collect($value)->every(fn ($item) => is_scalar($item))) {
                return implode('، ', array_map(static fn ($item) => (string) $item, $value));
            }

            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $translated = [
            'cash' => 'نقدي',
            'in_kind' => 'عيني',
            'instapay' => 'إنستا باي',
            'vodafone_cash' => 'فودافون كاش',
            'bank_transfer' => 'تحويل بنكي',
            'operational' => 'تشغيلي',
            'aid' => 'مساعدات',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'pending' => 'قيد المراجعة',
            'new' => 'جديد',
            'accepted' => 'مقبول',
            'under_review' => 'تحت المراجعة',
            'single_person' => 'شخص واحد',
            'multiple_beneficiaries' => 'أكثر من مستفيد',
            'single_sponsor' => 'كافل واحد',
            'multiple_sponsors' => 'أكثر من كافل',
            'individual' => 'فرد',
            'corporate' => 'مؤسسة / شركة',
            'recurring' => 'متكرر',
            'one_time' => 'لمرة واحدة',
            'monthly' => 'شهري',
            'monthly_sponsor' => 'كافل شهري',
            'sponsorship' => 'كفالة أيتام / حالات',
            'sadaqa_jariya' => 'صدقة جارية',
            'general' => 'عام',
            'zakat' => 'زكاة',
            'sadaqa' => 'صدقة',
            'male' => 'ذكر',
            'female' => 'أنثى',
            'single' => 'أعزب',
            'married' => 'متزوج',
            'widowed' => 'أرمل / أرملة',
            'divorced' => 'مطلق / مطلقة',
        ][(string) $value] ?? null;

        if ($translated !== null) {
            return $translated;
        }

        if (in_array($key, ['amount', 'estimated_value', 'cost', 'salary', 'allowances', 'sponsorship_monthly_amount', 'monthly_sponsorship_amount', 'monthly_income', 'assistance_amount'], true) && is_numeric($value)) {
            return number_format((float) $value, 2) . ' ج.م';
        }

        if (str_ends_with($key, '_id') && is_numeric($value)) {
            return self::resolveForeignKey($key, (int) $value) ?? '#' . $value;
        }

        if (in_array($key, ['date', 'received_at', 'start_date', 'end_date', 'join_date', 'created_at', 'updated_at'], true)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable) {
                // Keep the original value when it is not a date.
            }
        }

        return (string) $value;
    }

    private static function shouldHide(string $key, mixed $value): bool
    {
        if (in_array($key, [
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'created_by',
            'updated_by',
            'form_context',
            'edit_action',
            'id',
            'sync_sponsored_beneficiaries',
            'sync_sponsored_family_members',
        ], true)) {
            return true;
        }

        return $value === null || $value === '' || (is_array($value) && $value === []);
    }

    /** @return array<int, string>|null */
    private static function resolveList(string $key, array $values): ?array
    {
        $model = [
            'permissions' => [\App\Models\Permission::class, 'name'],
            'allocated_beneficiary_ids' => [\App\Models\Beneficiary::class, 'full_name'],
            'sponsored_beneficiary_ids' => [\App\Models\Beneficiary::class, 'full_name'],
            'direct_sponsored_beneficiary_ids' => [\App\Models\Beneficiary::class, 'full_name'],
            'sponsored_family_member_ids' => [\App\Models\BeneficiaryFamilyMember::class, 'full_name'],
            'sponsor_ids' => [\App\Models\Donor::class, 'name'],
        ][$key] ?? null;

        if ($model === null) {
            return null;
        }

        [$class, $label] = $model;
        $names = $class::query()->whereIn('id', $values)->pluck($label, 'id');

        return collect($values)
            ->map(fn ($id) => (string) ($names[$id] ?? "#{$id}"))
            ->all();
    }

    private static function resolveForeignKey(string $key, int $id): ?string
    {
        $model = [
            'project_id' => [\App\Models\Project::class, 'name'],
            'campaign_id' => [\App\Models\Campaign::class, 'name'],
            'donor_id' => [\App\Models\Donor::class, 'name'],
            'delegate_id' => [\App\Models\Delegate::class, 'name'],
            'warehouse_id' => [\App\Models\Warehouse::class, 'name'],
            'treasury_id' => [\App\Models\Treasury::class, 'name'],
            'guest_house_id' => [\App\Models\GuestHouse::class, 'name'],
            'role_id' => [\App\Models\Role::class, 'name'],
            'user_id' => [\App\Models\User::class, 'name'],
            'beneficiary_id' => [\App\Models\Beneficiary::class, 'full_name'],
            'account_id' => [\App\Models\Account::class, 'name'],
            'parent_id' => [\App\Models\Account::class, 'name'],
        ][$key] ?? null;

        if ($model === null || ! class_exists($model[0])) {
            return null;
        }

        [$class, $label] = $model;
        $record = $class::query()->find($id);

        if (! $record) {
            return "#{$id}";
        }

        return (string) ($record->getAttribute($label) ?: "#{$id}") . " (#{$id})";
    }
}
