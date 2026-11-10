<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Repositories\RoleRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final readonly class RoleService
{
    private const PROTECTED_ROLE_KEYS = ['admin'];

    public function __construct(
        private RoleRepository $roleRepository
    ) {}

    public function getFilteredRoles(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->roleRepository->paginateFiltered($filters, $perPage);
    }

    public function findRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function syncEntityPermissions(): void
    {
        $catalog = [
            // لوحة التحكم والمؤشرات
            ['key' => 'dashboard.view', 'name' => 'عرض لوحة التحكم الرئيسية'],
            ['key' => 'dashboard.analytics', 'name' => 'عرض المؤشرات والتحليلات الإحصائية'],
            ['key' => 'internal_chat.view', 'name' => 'استخدام المحادثات الداخلية للموظفين'],
            ['key' => 'notifications.view', 'name' => 'عرض الإشعارات والتنبيهات'],
            ['key' => 'audits.view', 'name' => 'عرض سجلات النظام والعمليات (Audit Logs)'],
            ['key' => 'reports.view', 'name' => 'عرض وتصدير التقارير العامة'],
            ['key' => 'data_import.view', 'name' => 'استيراد وتصدير ملفات الإكسيل (Excel Hub)'],
            ['key' => 'manage_change_requests', 'name' => 'إدارة طلبات المراجعة والاعتماد (الإلغاء والتعديل)'],
            ['key' => 'attachments.create', 'name' => 'رفع وإضافة المرفقات والملفات'],
            ['key' => 'attachments.delete', 'name' => 'حذف المرفقات والملفات'],

            // الموظفون والمستخدمون
            ['key' => 'users.view', 'name' => 'عرض سجل الموظفين والمستخدمين'],
            ['key' => 'users.create', 'name' => 'إضافة موظف / مستخدم جديد'],
            ['key' => 'users.edit', 'name' => 'تعديل بيانات الموظفين والمستخدمين'],
            ['key' => 'users.delete', 'name' => 'حذف الموظفين والمستخدمين'],

            // الأدوار والصلاحيات
            ['key' => 'roles.view', 'name' => 'عرض الأدوار والصلاحيات'],
            ['key' => 'roles.create', 'name' => 'إنشاء دور وظيفي جديد'],
            ['key' => 'roles.edit', 'name' => 'تعديل الأدوار وتخصيص الصلاحيات'],
            ['key' => 'roles.delete', 'name' => 'حذف الأدوار الوظيفية'],

            // المتبرعون
            ['key' => 'donors.view', 'name' => 'عرض سجل المتبرعين والكفلاء'],
            ['key' => 'donors.create', 'name' => 'إضافة متبرع / كفيل جديد'],
            ['key' => 'donors.edit', 'name' => 'تعديل بيانات المتبرعين والكفلاء'],
            ['key' => 'donors.delete', 'name' => 'حذف المتبرعين والكفلاء'],

            // التبرعات
            ['key' => 'donations.view', 'name' => 'عرض سجل التبرعات والإيصالات'],
            ['key' => 'donations.create', 'name' => 'تسجيل تبرع جديد (نقدي / عيني)'],
            ['key' => 'donations.edit', 'name' => 'تعديل بيانات التبرعات'],
            ['key' => 'donations.delete', 'name' => 'حذف التبرعات'],

            // المستفيدون والحالات
            ['key' => 'beneficiaries.view', 'name' => 'عرض سجل المستفيدين والأسر'],
            ['key' => 'beneficiaries.create', 'name' => 'إضافة مستفيد / حالة جديدة'],
            ['key' => 'beneficiaries.edit', 'name' => 'تعديل بيانات المستفيدين والأسر'],
            ['key' => 'beneficiaries.delete', 'name' => 'حذف المستفيدين'],

            // الدعم النفسي (بعثاء الأمل)
            ['key' => 'psychological_support.view', 'name' => 'عرض متابعات الدعم النفسي'],
            ['key' => 'psychological_support.create', 'name' => 'إضافة جلسة / متابعة دعم نفسي'],
            ['key' => 'psychological_support.edit', 'name' => 'تعديل متابعة دعم نفسي'],
            ['key' => 'psychological_support.delete', 'name' => 'حذف متابعة دعم نفسي'],

            // المشاريع (عام)
            ['key' => 'projects.view', 'name' => 'عرض جميع المشاريع (عام)'],
            ['key' => 'projects.create', 'name' => 'إنشاء مشروع جديد'],
            ['key' => 'projects.edit', 'name' => 'تعديل بيانات المشاريع'],
            ['key' => 'projects.delete', 'name' => 'حذف المشاريع'],
            ['key' => 'manage_project_volunteers', 'name' => 'إدارة متطوعي المشروع'],
            ['key' => 'assist_project_management', 'name' => 'مساعدة في إدارة المشروع'],

            // الحملات (عام)
            ['key' => 'campaigns.view', 'name' => 'عرض جميع الحملات (عام)'],
            ['key' => 'campaigns.create', 'name' => 'إنشاء حملة جديدة'],
            ['key' => 'campaigns.edit', 'name' => 'تعديل بيانات الحملات'],
            ['key' => 'campaigns.delete', 'name' => 'حذف الحملات'],

            // حملات رمضان
            ['key' => 'ramadan_bags.view', 'name' => 'عرض شنط رمضان'],
            ['key' => 'ramadan_bags.create', 'name' => 'إضافة شنطة رمضان'],
            ['key' => 'ramadan_bags.edit', 'name' => 'تعديل شنطة رمضان'],
            ['key' => 'ramadan_bags.delete', 'name' => 'حذف شنطة رمضان'],
            ['key' => 'ramadan_iftars.view', 'name' => 'عرض إفطارات رمضان'],
            ['key' => 'ramadan_iftars.create', 'name' => 'إضافة إفطار رمضان'],
            ['key' => 'ramadan_iftars.edit', 'name' => 'تعديل إفطار رمضان'],
            ['key' => 'ramadan_iftars.delete', 'name' => 'حذف إفطار رمضان'],

            // دور الضيافة (عام) ومساحات العمل
            ['key' => 'guest_houses.view', 'name' => 'عرض جميع دور الضيافة (عام)'],
            ['key' => 'guest_houses.create', 'name' => 'إضافة دار ضيافة جديدة'],
            ['key' => 'guest_houses.edit', 'name' => 'تعديل بيانات وعمليات دور الضيافة'],
            ['key' => 'guest_houses.delete', 'name' => 'حذف دور الضيافة'],
            ['key' => 'guest_houses.set_manager', 'name' => 'تعيين مدير دار الضيافة'],
            ['key' => 'guest_houses.manage_volunteers', 'name' => 'إدارة متطوعي دار الضيافة'],
            ['key' => 'guest_houses.manage_monthly_volunteers', 'name' => 'إدارة متطوعي الشهر بدار الضيافة'],
            ['key' => 'workspaces.view', 'name' => 'عرض مساحات العمل والحجوزات'],
            ['key' => 'workspaces.create', 'name' => 'إضافة مساحة عمل / حجز'],
            ['key' => 'workspaces.edit', 'name' => 'تعديل مساحات العمل والحجوزات'],
            ['key' => 'workspaces.delete', 'name' => 'حذف مساحات العمل والحجوزات'],

            // الحسابات والخزائن والمالية
            ['key' => 'treasuries.view', 'name' => 'عرض الخزائن النقدية وحركاتها'],
            ['key' => 'treasuries.create', 'name' => 'إضافة خزينة / حركة خزينة'],
            ['key' => 'treasuries.edit', 'name' => 'تعديل الخزائن وتسوية الأرصدة'],
            ['key' => 'treasuries.delete', 'name' => 'حذف الخزائن النقدية'],
            ['key' => 'accounts.view', 'name' => 'عرض شجرة الحسابات المالية'],
            ['key' => 'accounts.create', 'name' => 'إضافة حساب مالي جديد'],
            ['key' => 'accounts.edit', 'name' => 'تعديل الحسابات المالية'],
            ['key' => 'accounts.delete', 'name' => 'حذف الحسابات المالية'],
            ['key' => 'revenues.view', 'name' => 'عرض تقارير الإيرادات والتحليل المالي'],
            ['key' => 'journal_entries.view', 'name' => 'عرض القيود اليومية المحاسبية'],
            ['key' => 'journal_entries.create', 'name' => 'إضافة قيد يومية جديد'],
            ['key' => 'journal_entries.edit', 'name' => 'تعديل وترحيل القيود اليومية'],
            ['key' => 'journal_entries.delete', 'name' => 'حذف القيود اليومية'],
            ['key' => 'expenses.view', 'name' => 'عرض سجل المصروفات'],
            ['key' => 'expenses.create', 'name' => 'تسجيل مصروف جديد'],
            ['key' => 'expenses.edit', 'name' => 'تعديل بيانات المصروفات'],
            ['key' => 'expenses.delete', 'name' => 'حذف المصروفات'],
            ['key' => 'financial_closures.view', 'name' => 'عرض الإغلاقات المالية الشهرية والسنوية'],
            ['key' => 'financial_closures.create', 'name' => 'إنشاء إغلاق مالي جديد'],
            ['key' => 'financial_closures.edit', 'name' => 'تعديل واعتماد الإغلاقات المالية'],
            ['key' => 'financial_closures.delete', 'name' => 'حذف الإغلاقات المالية'],

            // المخازن والأصناف والموردين
            ['key' => 'warehouses.view', 'name' => 'عرض المخازن وأرصدتها'],
            ['key' => 'warehouses.create', 'name' => 'إضافة مخزن جديد'],
            ['key' => 'warehouses.edit', 'name' => 'تعديل بيانات المخازن'],
            ['key' => 'warehouses.delete', 'name' => 'حذف المخازن'],
            ['key' => 'items.view', 'name' => 'عرض دليل الأصناف المخزنية'],
            ['key' => 'items.create', 'name' => 'إضافة صنف مخزني جديد'],
            ['key' => 'items.edit', 'name' => 'تعديل بيانات الأصناف'],
            ['key' => 'items.delete', 'name' => 'حذف الأصناف'],
            ['key' => 'inventory_transactions.view', 'name' => 'عرض حركات المخزون (وارد / منصرف / تحويل)'],
            ['key' => 'inventory_transactions.create', 'name' => 'تسجيل إذن حركة مخزنية'],
            ['key' => 'inventory_transactions.edit', 'name' => 'تعديل حركة مخزنية'],
            ['key' => 'inventory_transactions.delete', 'name' => 'حذف حركة مخزنية'],
            ['key' => 'suppliers.view', 'name' => 'عرض سجل الموردين'],
            ['key' => 'suppliers.create', 'name' => 'إضافة مورد جديد'],
            ['key' => 'suppliers.edit', 'name' => 'تعديل بيانات الموردين'],
            ['key' => 'suppliers.delete', 'name' => 'حذف الموردين'],

            // اللوجستيات والمناديب وخطوط السير
            ['key' => 'delegates.view', 'name' => 'عرض سجل المناديب'],
            ['key' => 'delegates.create', 'name' => 'إضافة مندوب جديد'],
            ['key' => 'delegates.edit', 'name' => 'تعديل بيانات المناديب'],
            ['key' => 'delegates.delete', 'name' => 'حذف المناديب'],
            ['key' => 'travel_routes.view', 'name' => 'عرض خطوط السير'],
            ['key' => 'travel_routes.create', 'name' => 'إضافة خط سير جديد'],
            ['key' => 'travel_routes.edit', 'name' => 'تعديل خطوط السير'],
            ['key' => 'travel_routes.delete', 'name' => 'حذف خطوط السير'],
            ['key' => 'trips.view', 'name' => 'عرض سجل الرحلات والمأموريات'],
            ['key' => 'trips.create', 'name' => 'تسجيل رحلة جديدة'],
            ['key' => 'trips.edit', 'name' => 'تعديل بيانات الرحلات'],
            ['key' => 'trips.delete', 'name' => 'حذف الرحلات'],

            // الموارد البشرية والرواتب والحضور والإجازات
            ['key' => 'payrolls.view', 'name' => 'عرض كشوف الرواتب والأجور'],
            ['key' => 'payrolls.create', 'name' => 'إعداد وصرف راتب جديد'],
            ['key' => 'payrolls.edit', 'name' => 'تعديل كشوف الرواتب'],
            ['key' => 'payrolls.delete', 'name' => 'حذف سجلات الرواتب'],
            ['key' => 'employee_attendance.view', 'name' => 'عرض حضور وانصراف جميع الموظفين'],
            ['key' => 'employee_attendance.view_own', 'name' => 'عرض سجل الحضور الخاص بالموظف فقط'],
            ['key' => 'employee_attendance.create', 'name' => 'تسجيل بصمة حضور / انصراف موظف'],
            ['key' => 'employee_attendance.edit', 'name' => 'تعديل سجلات حضور الموظفين'],
            ['key' => 'employee_attendance.delete', 'name' => 'حذف سجلات حضور الموظفين'],
            ['key' => 'employee_tasks.view', 'name' => 'عرض مهام جميع الموظفين'],
            ['key' => 'employee_tasks.view_own', 'name' => 'عرض مهام الموظف الشخصية فقط'],
            ['key' => 'employee_tasks.create', 'name' => 'إسناد مهمة جديدة لموظف'],
            ['key' => 'employee_tasks.edit', 'name' => 'تعديل مهام الموظفين'],
            ['key' => 'employee_tasks.delete', 'name' => 'حذف مهام الموظفين'],
            ['key' => 'leaves.view', 'name' => 'عرض سجل جميع الإجازات'],
            ['key' => 'leaves.create', 'name' => 'تقديم طلب إجازة'],
            ['key' => 'leaves.edit', 'name' => 'تعديل بيانات الإجازات'],
            ['key' => 'leaves.delete', 'name' => 'حذف طلبات الإجازات'],
            ['key' => 'leaves.manage', 'name' => 'مراجعة واعتماد/رفض طلبات الإجازات'],
            ['key' => 'hr.evaluations', 'name' => 'عرض وإدارة تقييمات الأداء الوظيفي'],

            // شؤون المتطوعين
            ['key' => 'volunteers.view', 'name' => 'عرض سجل المتطوعين'],
            ['key' => 'volunteers.create', 'name' => 'إضافة متطوع جديد'],
            ['key' => 'volunteers.edit', 'name' => 'تعديل بيانات المتطوعين'],
            ['key' => 'volunteers.delete', 'name' => 'حذف المتطوعين'],
            ['key' => 'volunteer_attendance.view', 'name' => 'عرض حضور جميع المتطوعين'],
            ['key' => 'volunteer_attendance.view_own', 'name' => 'عرض سجل حضور المتطوع الشخصي فقط'],
            ['key' => 'volunteer_attendance.create', 'name' => 'تسجيل حضور متطوع'],
            ['key' => 'volunteer_attendance.edit', 'name' => 'تعديل حضور المتطوعين'],
            ['key' => 'volunteer_tasks.view', 'name' => 'عرض مهام المتطوعين'],
            ['key' => 'volunteer_tasks.create', 'name' => 'إضافة مهمة متطوع'],
            ['key' => 'volunteer_tasks.edit', 'name' => 'تعديل مهام المتطوعين'],
            ['key' => 'volunteer_tasks.delete', 'name' => 'حذف مهام المتطوعين'],
            ['key' => 'volunteer_hours.view', 'name' => 'عرض ساعات التطوع'],
            ['key' => 'volunteer_hours.create', 'name' => 'تسجيل ساعات تطوع'],
            ['key' => 'volunteer_hours.edit', 'name' => 'تعديل ساعات التطوع'],
            ['key' => 'log_volunteer_hours', 'name' => 'تسجيل ساعات التطوع المباشرة'],

            // المهام العامة والشكاوى والاستقبال والزيارات
            ['key' => 'tasks.view', 'name' => 'عرض جميع المهام العامة'],
            ['key' => 'tasks.create', 'name' => 'إضافة مهمة عامة'],
            ['key' => 'tasks.edit', 'name' => 'تعديل المهام العامة'],
            ['key' => 'tasks.delete', 'name' => 'حذف المهام العامة'],
            ['key' => 'view_own_tasks', 'name' => 'عرض المهام المسندة للموظف فقط'],
            ['key' => 'complaints.view', 'name' => 'عرض سجل الشكاوى والمقترحات'],
            ['key' => 'complaints.create', 'name' => 'تقديم / تسجيل شكوى جديدة'],
            ['key' => 'complaints.edit', 'name' => 'الرد على الشكاوى وتحديث حالتها'],
            ['key' => 'complaints.delete', 'name' => 'حذف الشكاوى'],
            ['key' => 'reception.view', 'name' => 'عرض سجل الاستقبال وخدمة المواطنين'],
            ['key' => 'reception.create', 'name' => 'تسجيل زائر / حركة في الاستقبال'],
            ['key' => 'reception.edit', 'name' => 'تعديل سجلات الاستقبال'],
            ['key' => 'reception.delete', 'name' => 'حذف سجلات الاستقبال'],
            ['key' => 'visits.view', 'name' => 'عرض الزيارات والأبحاث الميدانية'],
            ['key' => 'visits.create', 'name' => 'إضافة زيارة ميدانية جديدة'],
            ['key' => 'visits.edit', 'name' => 'تعديل تقارير الزيارات الميدانية'],
            ['key' => 'visits.delete', 'name' => 'حذف الزيارات الميدانية'],

            // التعاونات والشراكات والخدمات المتخصصة
            ['key' => 'collaborations.view', 'name' => 'عرض دليل التعاونات والشراكات'],
            ['key' => 'collaborations.create', 'name' => 'إضافة جهة في دليل الشراكات'],
            ['key' => 'collaborations.edit', 'name' => 'تعديل جهة في دليل الشراكات'],
            ['key' => 'collaborations.delete', 'name' => 'حذف جهة من دليل الشراكات'],
            ['key' => 'school_collaborations.view', 'name' => 'عرض تعاونات المدارس'],
            ['key' => 'school_collaborations.create', 'name' => 'إضافة تعاون مدرسة'],
            ['key' => 'school_collaborations.edit', 'name' => 'تعديل تعاون مدرسة'],
            ['key' => 'school_collaborations.delete', 'name' => 'حذف تعاون مدرسة'],
            ['key' => 'memberships.view', 'name' => 'عرض العضويات والجمعيات العمومية'],
            ['key' => 'memberships.create', 'name' => 'إضافة عضوية جديدة'],
            ['key' => 'memberships.edit', 'name' => 'تعديل بيانات العضويات'],
            ['key' => 'memberships.delete', 'name' => 'حذف العضويات'],
            ['key' => 'oncology_medicine_reps.view', 'name' => 'عرض مناديب أدوية الأورام'],
            ['key' => 'oncology_medicine_reps.create', 'name' => 'إضافة مندوب أدوية أورام'],
            ['key' => 'oncology_medicine_reps.edit', 'name' => 'تعديل مندوب أدوية أورام'],
            ['key' => 'oncology_medicine_reps.delete', 'name' => 'حذف مندوب أدوية أورام'],
            ['key' => 'kafr_el_sheikh_brokers.view', 'name' => 'عرض سماسرة كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_brokers.create', 'name' => 'إضافة سمسار كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_brokers.edit', 'name' => 'تعديل سمسار كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_brokers.delete', 'name' => 'حذف سمسار كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_deliveries.view', 'name' => 'عرض توصيلات كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_deliveries.create', 'name' => 'إضافة توصيل كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_deliveries.edit', 'name' => 'تعديل توصيل كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_deliveries.delete', 'name' => 'حذف توصيل كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_services.view', 'name' => 'عرض خدمات كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_services.create', 'name' => 'إضافة خدمة كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_services.edit', 'name' => 'تعديل خدمة كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_services.delete', 'name' => 'حذف خدمة كفر الشيخ'],
            ['key' => 'tanta_workers.view', 'name' => 'عرض عمال اليومية (طنطا)'],
            ['key' => 'tanta_workers.create', 'name' => 'إضافة عامل يومية (طنطا)'],
            ['key' => 'tanta_workers.edit', 'name' => 'تعديل عامل يومية (طنطا)'],
            ['key' => 'tanta_workers.delete', 'name' => 'حذف عامل يومية (طنطا)'],

            // إدارة الموقع الإلكتروني
            ['key' => 'website.view', 'name' => 'فتح لوحة إدارة الموقع الإلكتروني'],
            ['key' => 'website.create', 'name' => 'إضافة محتوى عام للموقع'],
            ['key' => 'website.edit', 'name' => 'تعديل محتوى الموقع'],
            ['key' => 'website.delete', 'name' => 'حذف محتوى الموقع'],
            ['key' => 'website.settings.view_edit', 'name' => 'إدارة محتوى الصفحة الرئيسية للموقع'],
            ['key' => 'website.headquarters.view', 'name' => 'عرض المقر والفروع بالموقع'],
            ['key' => 'website.headquarters.create', 'name' => 'إضافة مقر/فرع بالموقع'],
            ['key' => 'website.headquarters.edit', 'name' => 'تعديل مقر/فرع بالموقع'],
            ['key' => 'website.headquarters.delete', 'name' => 'حذف مقر/فرع بالموقع'],
            ['key' => 'website.partners.view', 'name' => 'عرض جدار الشرف والشركاء بالموقع'],
            ['key' => 'website.partners.create', 'name' => 'إضافة شريك لجدار الشرف'],
            ['key' => 'website.partners.edit', 'name' => 'تعديل شريك بجدار الشرف'],
            ['key' => 'website.partners.delete', 'name' => 'حذف شريك من جدار الشرف'],
            ['key' => 'website.board.view', 'name' => 'عرض مجلس الأمناء بالموقع'],
            ['key' => 'website.board.create', 'name' => 'إضافة عضو مجلس أمناء بالموقع'],
            ['key' => 'website.board.edit', 'name' => 'تعديل عضو مجلس أمناء بالموقع'],
            ['key' => 'website.board.delete', 'name' => 'حذف عضو مجلس أمناء بالموقع'],
            ['key' => 'website.content.view_edit', 'name' => 'إدارة محتوى المشاريع على الموقع'],
            ['key' => 'website.campaigns_content.view_edit', 'name' => 'إدارة محتوى الحملات على الموقع'],
            ['key' => 'website.guest_house_content.view_edit', 'name' => 'إدارة محتوى دار الضيافة وحجوزات الغرف على الموقع'],
            ['key' => 'website.news.view', 'name' => 'عرض أخبار الموقع'],
            ['key' => 'website.news.create', 'name' => 'إضافة خبر للموقع'],
            ['key' => 'website.news.edit', 'name' => 'تعديل خبر بالموقع'],
            ['key' => 'website.news.delete', 'name' => 'حذف خبر من الموقع'],
            ['key' => 'website.contact_messages.view', 'name' => 'عرض رسائل تواصل معنا (الموقع)'],
            ['key' => 'website.contact_messages.delete', 'name' => 'حذف رسائل تواصل معنا (الموقع)'],
            ['key' => 'website.subscriptions.view', 'name' => 'عرض مشتركي النشرة البريدية'],
            ['key' => 'website.subscriptions.delete', 'name' => 'حذف مشترك من النشرة البريدية'],
            ['key' => 'website.volunteer_requests.view', 'name' => 'عرض طلبات التطوع الواردة من الموقع'],
            ['key' => 'website.volunteer_requests.delete', 'name' => 'حذف طلب تطوع وارد من الموقع'],
            ['key' => 'website.donation_page.view_edit', 'name' => 'إدارة إعدادات صفحة التبرع بالموقع'],
            ['key' => 'website.donation_settings.view_edit', 'name' => 'إدارة مجالات وفئات التبرع بالموقع'],
            ['key' => 'website.accounts.view', 'name' => 'عرض حسابات دخول المتبرعين بالموقع'],
            ['key' => 'website.accounts.delete', 'name' => 'حذف حساب متبرع بالموقع'],
            ['key' => 'website.donation_accounts.view', 'name' => 'عرض الحسابات البنكية للتبرع بالموقع'],
            ['key' => 'website.donation_accounts.delete', 'name' => 'حذف حساب بنكي للتبرع بالموقع'],

            // إدارة تطبيق الموبايل
            ['key' => 'mobile.view', 'name' => 'فتح لوحة إدارة تطبيق الموبايل'],
            ['key' => 'mobile.create', 'name' => 'إضافة محتوى لتطبيق الموبايل'],
            ['key' => 'mobile.edit', 'name' => 'تعديل محتوى تطبيق الموبايل'],
            ['key' => 'mobile.delete', 'name' => 'حذف محتوى تطبيق الموبايل'],
            ['key' => 'mobile.home_content.view_edit', 'name' => 'إدارة محتوى الشاشة الرئيسية للموبايل'],
            ['key' => 'mobile.news.view', 'name' => 'عرض أخبار تطبيق الموبايل'],
            ['key' => 'mobile.news.create', 'name' => 'إضافة خبر لتطبيق الموبايل'],
            ['key' => 'mobile.news.edit', 'name' => 'تعديل خبر بتطبيق الموبايل'],
            ['key' => 'mobile.news.delete', 'name' => 'حذف خبر من تطبيق الموبايل'],
            ['key' => 'mobile.volunteer_requests.view', 'name' => 'عرض طلبات التطوع (تطبيق الموبايل)'],
            ['key' => 'mobile.volunteer_requests.delete', 'name' => 'حذف طلب تطوع (تطبيق الموبايل)'],
            ['key' => 'mobile.case_applications.view', 'name' => 'عرض طلبات الحالات المستحقة (تطبيق الموبايل)'],
            ['key' => 'mobile.case_applications.delete', 'name' => 'حذف طلب حالة مستحقة (تطبيق الموبايل)'],
            ['key' => 'mobile.bookings.view', 'name' => 'عرض طلبات حجز الغرف والمساحات (تطبيق الموبايل)'],
            ['key' => 'mobile.bookings.delete', 'name' => 'حذف طلب حجز (تطبيق الموبايل)'],
            ['key' => 'mobile.donations.view', 'name' => 'عرض تبرعات تطبيق الموبايل ومراجعتها'],
            ['key' => 'mobile.donors.view', 'name' => 'عرض حسابات المتبرعين المسجلين بالموبايل'],
            ['key' => 'mobile.notifications.view', 'name' => 'عرض سجل إشعارات الموبايل'],
            ['key' => 'mobile.notifications.create', 'name' => 'إرسال إشعار فوري لتطبيق الموبايل'],
        ];

        foreach ($catalog as $perm) {
            Permission::updateOrCreate(
                ['key' => $perm['key']],
                ['name' => $perm['name']]
            );
        }

        // 1. Projects
        $projects = \App\Models\Project::all();
        $validProjectIds = $projects->pluck('id')->toArray();

        foreach ($projects as $project) {
            Permission::updateOrCreate(
                ['key' => "projects.manage.{$project->id}"],
                ['name' => "إدارة مشروع محدد فقط: {$project->name}"]
            );
        }

        Permission::where('key', 'like', 'projects.manage.%')
            ->get()
            ->each(function ($p) use ($validProjectIds) {
                $id = (int) str_replace('projects.manage.', '', $p->key);
                if (!in_array($id, $validProjectIds, true)) {
                    $p->delete();
                }
            });

        // 2. Campaigns
        $campaigns = \App\Models\Campaign::all();
        $validCampaignIds = $campaigns->pluck('id')->toArray();
        foreach ($campaigns as $campaign) {
            Permission::updateOrCreate(
                ['key' => "campaigns.manage.{$campaign->id}"],
                ['name' => "إدارة حملة محددة فقط: {$campaign->name}"]
            );
        }

        Permission::where('key', 'like', 'campaigns.manage.%')
            ->get()
            ->each(function ($p) use ($validCampaignIds) {
                $id = (int) str_replace('campaigns.manage.', '', $p->key);
                if (!in_array($id, $validCampaignIds, true)) {
                    $p->delete();
                }
            });

        // 3. Guest Houses
        $guestHouses = \App\Models\GuestHouse::all();
        $validGuestHouseIds = $guestHouses->pluck('id')->toArray();
        foreach ($guestHouses as $gh) {
            Permission::updateOrCreate(
                ['key' => "guest_houses.manage.{$gh->id}"],
                ['name' => "إدارة دار ضيافة محددة فقط: {$gh->name}"]
            );
        }

        Permission::where('key', 'like', 'guest_houses.manage.%')
            ->get()
            ->each(function ($p) use ($validGuestHouseIds) {
                $id = (int) str_replace('guest_houses.manage.', '', $p->key);
                if (!in_array($id, $validGuestHouseIds, true)) {
                    $p->delete();
                }
            });
    }

    public function getPermissionGroups(): Collection
    {
        $this->syncEntityPermissions();
        $groupLabels = $this->getGroupLabels();

        return Permission::orderBy('key')->get()->groupBy(function (Permission $permission) use ($groupLabels) {
            if (preg_match('/^projects\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['projects_specific'];
            }
            if (preg_match('/^campaigns\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['campaigns_specific'];
            }
            if (preg_match('/^guest_houses\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['guest_houses_specific'];
            }

            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function getGroupedRolePermissions(Role $role): Collection
    {
        $this->syncEntityPermissions();
        $groupLabels = $this->getGroupLabels();

        return $role->permissions->groupBy(function (Permission $permission) use ($groupLabels) {
            if (preg_match('/^projects\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['projects_specific'];
            }
            if (preg_match('/^campaigns\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['campaigns_specific'];
            }
            if (preg_match('/^guest_houses\.manage\.\d+$/', $permission->key)) {
                return $groupLabels['guest_houses_specific'];
            }

            $prefix = explode('.', $permission->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function createRole(array $data): Role
    {
        $permissions = $this->withDashboardPermission($data['permissions'] ?? []);
        $userIds = array_values(array_unique(array_map('intval', $data['users'] ?? [])));
        $exclusiveRole = (bool) ($data['exclusive_users'] ?? true);

        unset($data['permissions'], $data['users'], $data['users_present'], $data['exclusive_users']);

        $role = $this->roleRepository->create($data);
        $role->permissions()->sync($permissions);

        if (!empty($userIds)) {
            if ($exclusiveRole) {
                foreach (User::whereIn('id', $userIds)->get() as $u) {
                    $u->roles()->sync([$role->id]);
                }
            } else {
                $role->users()->sync($userIds);
            }
        }

        return $role;
    }

    public function updateRole(Role $role, array $data): mixed
    {
        if (in_array($role->key, self::PROTECTED_ROLE_KEYS, true) && ($data['key'] ?? $role->key) !== $role->key) {
            throw ValidationException::withMessages([
                'key' => 'لا يمكن تغيير معرّف دور أساسي في النظام.',
            ]);
        }

        $originalData = $data;
        $executor = function () use ($role, $originalData) {
            $permissions = $this->withDashboardPermission($originalData['permissions'] ?? []);
            $hasUsersSync = array_key_exists('users', $originalData) || !empty($originalData['users_present']);
            $userIds = array_values(array_unique(array_map('intval', $originalData['users'] ?? [])));
            $exclusiveRole = (bool) ($originalData['exclusive_users'] ?? false);

            $updateData = $originalData;
            unset($updateData['permissions'], $updateData['users'], $updateData['users_present'], $updateData['exclusive_users']);

            $this->roleRepository->update($role, $updateData);
            $role->permissions()->sync($permissions);

            if ($hasUsersSync) {
                if ($exclusiveRole && !empty($userIds)) {
                    foreach (User::whereIn('id', $userIds)->get() as $u) {
                        $u->roles()->sync([$role->id]);
                    }
                }
                $role->users()->sync($userIds);
            }

            return $role;
        };

        return ChangeRequestService::handleRequest(
            Role::class,
            $role->id,
            'update',
            $data,
            $executor,
            false
        );
    }

    public function attachUserToRole(Role $role, int $userId, bool $replaceExistingRoles = true): void
    {
        $user = User::findOrFail($userId);
        if ($replaceExistingRoles) {
            $user->roles()->sync([$role->id]);
        } else {
            $role->users()->syncWithoutDetaching([$user->id]);
        }
        $user->clearPermissionCache();
    }

    public function detachUserFromRole(Role $role, int $userId): void
    {
        $role->users()->detach($userId);
        $user = User::find($userId);
        $user?->clearPermissionCache();
    }

    public function deleteRole(Role $role): mixed
    {
        if (in_array($role->key, self::PROTECTED_ROLE_KEYS, true)) {
            throw ValidationException::withMessages([
                'role' => 'لا يمكن حذف دور المدير العام الأساسي في النظام.',
            ]);
        }

        $executor = function () use ($role) {
            $role->users()->detach();
            $role->permissions()->detach();
            return $this->roleRepository->delete($role);
        };

        return ChangeRequestService::handleRequest(
            Role::class,
            $role->id,
            'delete',
            [
                'note' => 'حذف دور وصلاحيات',
                'name' => $role->name,
                'key'  => $role->key
            ],
            $executor,
            false
        );
    }

    public function getGroupLabels(): array
    {
        return [
            'projects_specific'         => '1. تخصيص مشروع محدد للموظف (يرى هذا المشروع فقط)',
            'campaigns_specific'        => '2. تخصيص حملة محددة للموظف (يرى هذه الحملة فقط)',
            'guest_houses_specific'     => '3. تخصيص دار ضيافة محددة للموظف (يرى هذه الدار فقط)',
            'dashboard'                 => 'لوحة التحكم والمؤشرات',
            'internal_chat'             => 'المحادثات الداخلية',
            'data_import'               => 'استيراد البيانات (Excel Hub)',
            'donors'                    => 'المتبرعون والكفلاء',
            'donations'                 => 'التبرعات والإيصالات',
            'beneficiaries'             => 'المستفيدون والأسر',
            'psychological_support'     => 'الدعم النفسي (بعثاء الأمل)',
            'projects'                  => 'المشاريع (صلاحيات عامة)',
            'campaigns'                 => 'الحملات (صلاحيات عامة)',
            'ramadan_bags'              => 'حملة رمضان - شنط رمضان',
            'ramadan_iftars'            => 'حملة رمضان - إفطارات رمضان',
            'guest_houses'              => 'دور الضيافة (صلاحيات عامة)',
            'workspaces'                => 'مساحات العمل والحجوزات',
            'treasuries'                => 'الخزائن النقدية والعهد',
            'accounts'                  => 'شجرة ودليل الحسابات',
            'revenues'                  => 'تقارير الإيرادات المالية',
            'journal_entries'           => 'القيود اليومية المحاسبية',
            'expenses'                  => 'المصروفات والمدفوعات',
            'financial_closures'        => 'الإقفال المالي',
            'closures'                  => 'الإقفال المالي',
            'warehouses'                => 'المخازن والمستودعات',
            'inventory'                 => 'المخازن والمستودعات',
            'items'                     => 'الأصناف المخزنية',
            'inventory_transactions'    => 'حركات المخزن (وارد / منصرف)',
            'suppliers'                 => 'الموردون',
            'delegates'                 => 'اللوجيستك - المناديب',
            'travel_routes'             => 'اللوجيستك - خطوط السير',
            'trips'                     => 'اللوجيستك - الرحلات',
            'users'                     => 'شؤون الموظفين والمستخدمين',
            'employee_attendance'       => 'حضور وانصراف الموظفين',
            'employee_tasks'            => 'مهام الموظفين',
            'leaves'                    => 'إجازات الموظفين',
            'payroll'                   => 'الرواتب والأجور',
            'payrolls'                  => 'الرواتب والأجور',
            'hr'                        => 'تقييمات الأداء (HR)',
            'volunteers'                => 'شؤون المتطوعين',
            'volunteer_attendance'      => 'حضور المتطوعين',
            'volunteer_tasks'           => 'مهام المتطوعين',
            'volunteer_hours'           => 'ساعات التطوع',
            'log_volunteer_hours'       => 'ساعات التطوع',
            'tasks'                     => 'المهام العامة',
            'view_own_tasks'            => 'المهام العامة',
            'reception'                 => 'الاستقبال وخدمة المواطنين',
            'visits'                    => 'الزيارات والأبحاث الميدانية',
            'complaints'                => 'الشكاوى والمقترحات',
            'collaborations'            => 'دليل التعاونات والشراكات',
            'school_collaborations'     => 'تعاونات المدارس',
            'memberships'               => 'العضويات',
            'oncology_medicine_reps'    => 'مناديب أدوية الأورام',
            'kafr_el_sheikh_brokers'    => 'سماسرة كفر الشيخ',
            'kafr_el_sheikh_deliveries' => 'توصيلات كفر الشيخ',
            'kafr_el_sheikh_services'   => 'خدمات كفر الشيخ',
            'tanta_workers'             => 'عمال باليومية (طنطا)',
            'website'                   => 'إدارة الموقع الإلكتروني',
            'mobile'                    => 'إدارة تطبيق الموبايل',
            'roles'                     => 'الأدوار والصلاحيات',
            'change_requests'           => 'طلبات المراجعة والاعتماد',
            'manage_change_requests'    => 'طلبات المراجعة والاعتماد',
            'reports'                   => 'التقارير العامة',
            'audits'                    => 'سجلات النظام (Logs)',
            'notifications'             => 'الإشعارات والتنبيهات',
            'attachments'               => 'المرفقات والملفات',
            'logs'                      => 'سجلات النظام (Logs)',
            'manage_employees'          => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_volunteers_hr'      => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_payrolls'           => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_finance'            => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_logistics'          => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_project'            => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_project_volunteers' => 'المشاريع (صلاحيات عامة)',
            'assist_project_management' => 'المشاريع (صلاحيات عامة)',
            'manage_campaign'           => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_ramadan'            => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_guest_house'        => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_specialized_services' => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_website'            => 'صلاحيات قطاعية عامة (إضافية)',
            'manage_mobile'             => 'صلاحيات قطاعية عامة (إضافية)',
        ];
    }

    private function withDashboardPermission(array $permissionIds): array
    {
        $dashboardPermissionId = Permission::where('key', 'dashboard.view')->value('id');

        if ($dashboardPermissionId) {
            $permissionIds[] = (int) $dashboardPermissionId;
        }

        return array_values(array_unique(array_map('intval', $permissionIds)));
    }
}
