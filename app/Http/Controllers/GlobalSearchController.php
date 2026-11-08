<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ArabicSearchHelper;
use App\Models\Account;
use App\Models\Beneficiary;
use App\Models\BeneficiaryReassessment;
use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\Complaint;
use App\Models\Delegate;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Expense;
use App\Models\FieldVisit;
use App\Models\GuestHouse;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\KafrElSheikhBroker;
use App\Models\KafrElSheikhDelivery;
use App\Models\KafrElSheikhService;
use App\Models\Membership;
use App\Models\MobileCaseApplication;
use App\Models\MobileVolunteerRequest;
use App\Models\OncologyMedicineRep;
use App\Models\Project;
use App\Models\PsychologicalSupportReport;
use App\Models\RamadanBag;
use App\Models\RamadanIftar;
use App\Models\ReceptionLog;
use App\Models\SchoolCollaboration;
use App\Models\Supplier;
use App\Models\TantaWorker;
use App\Models\Task;
use App\Models\TravelRoute;
use App\Models\Treasury;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WebDonor;
use App\Models\WebVolunteerRequest;
use App\Models\Workspace;
use App\Models\WorkspaceRental;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([
                'query' => $q,
                'total' => 0,
                'categories' => [],
            ]);
        }

        $parsed = ArabicSearchHelper::parseQuery($q);
        $cleanQ = $parsed['raw'];
        $variants = $parsed['variants'];
        $words = $parsed['words'];
        $numbers = $parsed['numbers'];
        $qNorm = ArabicSearchHelper::normalize($cleanQ);

        $results = [
            'system_pages' => [],
            'beneficiaries' => [],
            'case_applications' => [],
            'donors' => [],
            'donations' => [],
            'projects' => [],
            'employees' => [],
            'volunteers' => [],
            'inventory' => [],
            'logistics' => [],
            'collaborations' => [],
            'complaints' => [],
        ];

        $safeRun = static function (callable $callback): void {
            try {
                $callback();
            } catch (\Throwable $e) {
                // Prevent any single table/column mismatch from breaking global search
            }
        };

        // 0. System Pages & Quick Navigation (أقسام وصفحات النظام السريعة)
        $safeRun(function () use ($qNorm, $cleanQ, &$results) {
            $user = auth()->user();
            $pages = [
                ['title' => 'المستفيدون والحالات', 'sub' => 'سجل الحالات والأسر والأيتام والمرضى', 'route' => 'beneficiaries.index', 'perm' => 'beneficiaries.view', 'icon' => 'bi-person-heart', 'badge' => 'قسم بالنظام', 'keywords' => 'مستفيدين حالات اسر ايتام مرضى كفالات beneficiaries'],
                ['title' => 'إضافة حالة / مستفيد جديد', 'sub' => 'تسجيل ملف مستفيد أو أسرة جديدة', 'route' => 'beneficiaries.create', 'perm' => 'beneficiaries.create', 'icon' => 'bi-person-plus-fill', 'badge' => 'إجراء سريع', 'keywords' => 'اضافة حالة مستفيد جديد تسجيل اسرة'],
                ['title' => 'المتبرعون والكفلاء', 'sub' => 'قاعدة بيانات المتبرعين والكفالات الشهرية', 'route' => 'donors.index', 'perm' => 'donors.view', 'icon' => 'bi-people-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'متبرعين كفلاء كافل donors'],
                ['title' => 'التبرعات والإيصالات', 'sub' => 'سجل التبرعات النقدية والعينية والإيصالات', 'route' => 'donations.index', 'perm' => 'donations.view', 'icon' => 'bi-gift-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'تبرعات ايصالات نقدي عيني كاش donations'],
                ['title' => 'تسجيل تبرع جديد', 'sub' => 'إضافة إيصال تبرع نقدي أو عيني وتوزيعه', 'route' => 'donations.create', 'perm' => 'donations.create', 'icon' => 'bi-plus-circle-fill', 'badge' => 'إجراء سريع', 'keywords' => 'تسجيل اضافة تبرع ايصال جديد'],
                ['title' => 'طلبات التقديم والبحث المبدئي', 'sub' => 'استمارات زاد ومدرار وبعثاء الأمل والمساعدات العامة', 'route' => 'case-applications.index', 'perm' => 'mobile.case_applications.view', 'icon' => 'bi-file-earmark-text-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'تقديمات طلبات بحث مبدئي استمارات زاد مدرار اورام'],
                ['title' => 'الزيارات والأبحاث الميدانية', 'sub' => 'تقارير الباحثين والفحص الميداني للحالات', 'route' => 'visits.index', 'perm' => 'visits.view', 'icon' => 'bi-geo-alt-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'زيارات ميدانية ابحاث باحث ميداني visits'],
                ['title' => 'الدعم النفسي (بعثاء الأمل)', 'sub' => 'متابعة مرضى الأورام والأطفال والتقارير الدورية', 'route' => 'psychological-support.index', 'perm' => 'psychological_support.view', 'icon' => 'bi-heart-pulse-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'دعم نفسي بعثاء الامل اورام جلسات'],
                ['title' => 'إدارة المشاريع', 'sub' => 'مشروع زاد، مدرار، كسوة، بعثاء الأمل، والمساعدات', 'route' => 'projects.index', 'perm' => 'projects.view', 'icon' => 'bi-kanban-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'مشاريع زاد مدرار كسوة بعثاء الامل مساعدات projects'],
                ['title' => 'الحملات الموسمية', 'sub' => 'حملات التبرع وشنط وإفطارات رمضان', 'route' => 'campaigns.index', 'perm' => 'campaigns.view', 'icon' => 'bi-megaphone-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'حملات موسمية رمضان شتاء اضاحي campaigns'],
                ['title' => 'دار الضيافة', 'sub' => 'إدارة الغرف والأجنحة والأسرة والإقامات والعهد', 'route' => 'guest-houses.index', 'perm' => 'guest_houses.view', 'icon' => 'bi-house-door-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'دار ضيافة غرف سراير اقامة نزلاء طنطا كفر الشيخ'],
                ['title' => 'الخزائن النقدية والبنكية', 'sub' => 'أرصدة الخزائن وحركة الوارد والصادر المالي', 'route' => 'treasuries.index', 'perm' => 'treasuries.view', 'icon' => 'bi-safe2-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'خزائن خزينة خزنة عهدة نقدية بنك صادر وارد treasuries'],
                ['title' => 'المصروفات وسندات الصرف', 'sub' => 'تسجيل ومتابعة المصروفات التشغيلية والمساعدات', 'route' => 'expenses.index', 'perm' => 'expenses.view', 'icon' => 'bi-arrow-up-right-circle-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'مصروفات مصاريف سند صرف نفقات expenses'],
                ['title' => 'الإيرادات والتحليل المالي', 'sub' => 'حركة الإيرادات وسندات القبض', 'route' => 'revenues.index', 'perm' => 'revenues.view', 'icon' => 'bi-arrow-down-left-circle-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'ايرادات مقبوضات مالية revenues'],
                ['title' => 'دليل الحسابات والقيود اليومية', 'sub' => 'شجرة الحسابات والقيود المحاسبية', 'route' => 'accounts.index', 'perm' => 'accounts.view', 'icon' => 'bi-diagram-3-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'حسابات شجرة قيود يومية محاسبة accounts'],
                ['title' => 'المستودعات والمخازن', 'sub' => 'إدارة المخازن والأصناف وحركات المخزون', 'route' => 'warehouses.index', 'perm' => 'warehouses.view', 'icon' => 'bi-boxes', 'badge' => 'قسم بالنظام', 'keywords' => 'مخازن مخزن مستودعات جرد اصناف warehouses inventory'],
                ['title' => 'الأصناف والمواد المخزنية', 'sub' => 'بطاقات الأصناف والأرصدة المخزنية', 'route' => 'items.index', 'perm' => 'items.view', 'icon' => 'bi-box-seam-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'اصناف مواد بضاعة مخزون items'],
                ['title' => 'الموردون والمشتريات', 'sub' => 'سجل الموردين وفواتير الشراء', 'route' => 'suppliers.index', 'perm' => 'suppliers.view', 'icon' => 'bi-shop', 'badge' => 'قسم بالنظام', 'keywords' => 'موردين مورد تجار مشتريات suppliers'],
                ['title' => 'الموظفون والموارد البشرية', 'sub' => 'دليل العاملين وبيانات الموظفين والعقود', 'route' => 'users.index', 'perm' => 'users.view', 'icon' => 'bi-people-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'موظفين عاملين موارد بشرية اتش ار hr users employees'],
                ['title' => 'حضور وانصراف الموظفين', 'sub' => 'سجل الدوام اليومي والتأخيرات', 'route' => 'employee-attendance.index', 'perm' => 'employee_attendance.view', 'icon' => 'bi-clock-history', 'badge' => 'قسم بالنظام', 'keywords' => 'حضور انصراف دوام بصمة موظفين'],
                ['title' => 'الإجازات والأذونات', 'sub' => 'طلبات الإجازات وأرصدة الموظفين', 'route' => 'leaves.index', 'perm' => 'leaves.view', 'icon' => 'bi-calendar-check-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'اجازات اذونات رصيد اجازة leaves'],
                ['title' => 'مسير الرواتب', 'sub' => 'رواتب واستحقاقات الموظفين الشهرية', 'route' => 'payrolls.index', 'perm' => 'payrolls.view', 'icon' => 'bi-cash-stack', 'badge' => 'قسم بالنظام', 'keywords' => 'رواتب مرتبات قبض payrolls'],
                ['title' => 'المتطوعون وساعات التطوع', 'sub' => 'قاعدة بيانات المتطوعين والفرق التطوعية', 'route' => 'volunteers.index', 'perm' => 'volunteers.view', 'icon' => 'bi-person-hearts', 'badge' => 'قسم بالنظام', 'keywords' => 'متطوعين تطوع فرق volunteers'],
                ['title' => 'طلبات التطوع (الموقع)', 'sub' => 'استمارات التطوع الواردة من الموقع الإلكتروني', 'route' => 'website.volunteer-requests.index', 'perm' => 'website.volunteer_requests.view', 'icon' => 'bi-person-plus', 'badge' => 'قسم بالنظام', 'keywords' => 'طلبات تطوع موقع استمارات متطوعين جدد'],
                ['title' => 'المناديب وخطوط السير والرحلات', 'sub' => 'إدارة اللوجيستك ومناديب التحصيل والتوصيل', 'route' => 'delegates.index', 'perm' => 'delegates.view', 'icon' => 'bi-truck', 'badge' => 'قسم بالنظام', 'keywords' => 'لوجيستك مناديب مندوب خطوط سير رحلات توصيل delegates'],
                ['title' => 'دليل التعاونات والشراكات', 'sub' => 'المستشفيات والمراكز الطبية والصيدليات والمعامل المتعاقدة', 'route' => 'collaborations.index', 'perm' => 'collaborations.view', 'icon' => 'bi-hospital-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'تعاونات شراكات مستشفيات معامل صيدليات اطباء خصومات'],
                ['title' => 'الاستقبال وخدمة العملاء', 'sub' => 'تسجيل الزوار والمكالمات والتحويل للأقسام', 'route' => 'reception.index', 'perm' => 'reception.view', 'icon' => 'bi-telephone-inbound-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'استقبال زوار مكالمات خدمة عملاء reception'],
                ['title' => 'مساحات العمل (Workspace)', 'sub' => 'حجوزات القاعات والمكاتب المشتركة', 'route' => 'workspaces.index', 'perm' => 'workspaces.view', 'icon' => 'bi-easel2-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'مساحات عمل قاعات حجز وورك سبيس workspaces'],
                ['title' => 'الشكاوى والمقترحات', 'sub' => 'متابعة الشكاوى والرد عليها', 'route' => 'complaints.index', 'perm' => 'complaints.view', 'icon' => 'bi-chat-dots-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'شكاوى مقترحات شكوى complaints'],
                ['title' => 'الأدوار والصلاحيات', 'sub' => 'إدارة صلاحيات المستخدمين والأدوار الوظيفية', 'route' => 'roles.index', 'perm' => 'roles.view', 'icon' => 'bi-shield-lock-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'ادوار صلاحيات امان roles permissions'],
                ['title' => 'مركز استيراد الإكسيل (Excel Hub)', 'sub' => 'رفع واستيراد شيتات البيانات الذكي', 'route' => 'data-import.index', 'perm' => 'data_import.view', 'icon' => 'bi-file-earmark-spreadsheet-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'اكسيل استيراد شيت excel import'],
                ['title' => 'التقارير والإحصاءات الشاملة', 'sub' => 'تقارير التبرعات والمستفيدين والمخازن والمالية', 'route' => 'reports.index', 'perm' => 'reports.view', 'icon' => 'bi-bar-chart-fill', 'badge' => 'قسم بالنظام', 'keywords' => 'تقارير احصائيات طباعة تصدير reports'],
            ];

            foreach ($pages as $p) {
                if ($user && !empty($p['perm']) && !$user->hasPermission($p['perm'])) {
                    continue;
                }
                $haystack = ArabicSearchHelper::normalize($p['title'] . ' ' . $p['sub'] . ' ' . $p['keywords']);
                if ($qNorm !== '' && (mb_stripos($haystack, $qNorm) !== false || mb_stripos($haystack, mb_strtolower($cleanQ)) !== false)) {
                    $results['system_pages'][] = [
                        'id' => 'page-' . $p['route'],
                        'title' => $p['title'],
                        'code' => 'قسم',
                        'subtitle' => $p['sub'],
                        'url' => route($p['route']),
                        'badge' => $p['badge'],
                        'badge_class' => 'bg-primary-subtle text-primary',
                        'icon' => $p['icon'],
                    ];
                }
                if (count($results['system_pages']) >= 4) {
                    break;
                }
            }
        });

        // 1. Beneficiaries / الحالات والمستفيدون (وأفراد الأسرة وملفات المرضى وإعادة البحث)
        $safeRun(function () use ($cleanQ, $q, $qNorm, &$results) {
            $beneficiariesQuery = Beneficiary::query()->with([
                'familyMembers' => fn ($fm) => $fm->where(fn ($sq) => $sq->whereNull('active')->orWhere('active', true)->orWhere('active', 1)),
                'patientProfile',
            ]);

            if (preg_match('/^\d{2}$/', $cleanQ)) {
                $beneficiariesQuery->where(function ($w) use ($cleanQ, $q) {
                    $w->where('code', 'like', "{$cleanQ}%")
                      ->orWhere('code', 'like', "G-{$cleanQ}%")
                      ->orWhere('code', 'like', "B{$cleanQ}%")
                      ->orWhere('patient_code', 'like', "{$cleanQ}%")
                      ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', "{$cleanQ}%")->orWhere('code', 'like', "B{$cleanQ}%"))
                      ->orWhere('full_name', 'like', "%{$q}%");
                });
            } elseif (preg_match('/^(?:19|20)(\d{2})$/', $cleanQ, $matches)) {
                $yr = $matches[1];
                $beneficiariesQuery->where(function ($w) use ($yr, $cleanQ, $q) {
                    $w->where('code', 'like', "{$yr}%")
                      ->orWhere('code', 'like', "G-{$yr}%")
                      ->orWhere('code', 'like', "B{$yr}%")
                      ->orWhere('code', 'like', "%{$cleanQ}%")
                      ->orWhere('patient_code', 'like', "{$yr}%")
                      ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', "{$yr}%")->orWhere('code', 'like', "B{$yr}%"))
                      ->orWhere('full_name', 'like', "%{$q}%");
                });
            } else {
                $beneficiariesQuery->where(function ($w) use ($cleanQ) {
                    ArabicSearchHelper::applySmartSearch(
                        $w,
                        $cleanQ,
                        ['full_name', 'patient_name', 'guardian_name', 'mother_name', 'deceased_husband_name', 'address', 'assistance_type', 'notes'],
                        ['code', 'patient_code', 'national_id', 'phone', 'backup_phone', 'visa_card_number', 'reference_number']
                    );

                    // Also search inside familyMembers (children, patients, spouses)
                    $w->orWhereHas('familyMembers', function ($fm) use ($cleanQ) {
                        ArabicSearchHelper::applySmartSearch(
                            $fm,
                            $cleanQ,
                            ['full_name', 'school_type', 'health_status', 'notes'],
                            ['code', 'national_id', 'phone']
                        );
                    });

                    // Also search inside GuestHousePatientProfile (companion, diagnosis, doctor)
                    $w->orWhereHas('patientProfile', function ($pp) use ($cleanQ) {
                        ArabicSearchHelper::applySmartSearch(
                            $pp,
                            $cleanQ,
                            ['companion_name', 'diagnosis', 'doctor_name', 'hospital_department'],
                            ['companion_phone', 'companion_national_id']
                        );
                    });
                });
            }

            $beneficiaries = $beneficiariesQuery
                ->select(['id', 'code', 'full_name', 'patient_name', 'guardian_name', 'phone', 'national_id', 'assistance_type', 'status', 'address'])
                ->limit(8)
                ->get();

            foreach ($beneficiaries as $b) {
                $codeStr = $b->code ?: ('BEN-' . $b->id);
                $subParts = [];
                if ($b->phone) $subParts[] = 'هاتف: ' . $b->phone;
                if ($b->national_id) $subParts[] = 'رقم قومي: ' . $b->national_id;
                if ($b->assistance_type) $subParts[] = $b->assistance_type;
                elseif ($b->address) $subParts[] = $b->address;

                $matchedMemberTitle = null;
                if ($b->relationLoaded('familyMembers') && $b->familyMembers->isNotEmpty()) {
                    foreach ($b->familyMembers as $m) {
                        $mNorm = ArabicSearchHelper::normalize((string) $m->full_name);
                        if (
                            ($qNorm !== '' && mb_stripos($mNorm, $qNorm) !== false)
                            || ($m->national_id && stripos((string) $m->national_id, $cleanQ) !== false)
                            || ($m->code && stripos((string) $m->code, $cleanQ) !== false)
                        ) {
                            $matchedMemberTitle = $m->full_name . ' (أسرة: ' . $b->full_name . ')';
                            if ($m->national_id && !$b->national_id) {
                                $subParts[] = 'قومي الفرد: ' . $m->national_id;
                            }
                            if ($m->code) {
                                $codeStr = $m->code;
                            }
                            break;
                        }
                    }
                }

                $displayTitle = $matchedMemberTitle ?: ($b->patient_name && $b->patient_name !== $b->full_name ? "{$b->full_name} ({$b->patient_name})" : $b->full_name);

                $results['beneficiaries'][] = [
                    'id' => $b->id,
                    'title' => $displayTitle,
                    'code' => $codeStr,
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('beneficiaries.show', $b),
                    'badge' => 'حالة / مستفيد',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-heart-pulse-fill',
                ];
            }
        });

        // 1.b Reassessments / استمارات إعادة البحث الدورية
        $safeRun(function () use ($cleanQ, &$results) {
            if (count($results['beneficiaries']) >= 10) {
                return;
            }
            $reassessQuery = BeneficiaryReassessment::query()->with('beneficiary:id,code,full_name');
            ArabicSearchHelper::applySmartSearch(
                $reassessQuery,
                $cleanQ,
                ['head_name', 'researcher_name', 'governorate', 'center', 'village', 'researcher_recommendation'],
                ['national_id', 'phone']
            );
            foreach ($reassessQuery->latest('id')->limit(3)->get() as $ra) {
                $targetBen = $ra->beneficiary;
                if (!$targetBen) continue;
                $results['beneficiaries'][] = [
                    'id' => 'ra-' . $ra->id,
                    'title' => 'إعادة بحث: ' . ($ra->head_name ?: $targetBen->full_name),
                    'code' => $targetBen->code ?: ('RA-' . $ra->id),
                    'subtitle' => implode(' • ', array_filter([
                        $ra->researcher_name ? 'الباحث: ' . $ra->researcher_name : null,
                        $ra->phone ? 'هاتف: ' . $ra->phone : null,
                        $ra->assessment_date ? $ra->assessment_date->format('Y-m-d') : null,
                    ])),
                    'url' => route('beneficiaries.show', $targetBen),
                    'badge' => 'إعادة بحث 6 أشهر',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-arrow-repeat',
                ];
            }
        });

        // 2. Initial Research & Case Applications + Field Visits + Psychological Support
        $safeRun(function () use ($cleanQ, &$results) {
            $caseAppsQuery = MobileCaseApplication::query();
            ArabicSearchHelper::applySmartSearch(
                $caseAppsQuery,
                $cleanQ,
                ['applicant_name', 'submitter_name', 'mother_name', 'deceased_husband_name', 'guide_name', 'address', 'city'],
                ['applicant_id_number', 'applicant_phone', 'backup_phone', 'submitter_phone', 'guide_phone', 'reference_number']
            );

            $caseApps = $caseAppsQuery->latest('created_at')->limit(6)->get();

            foreach ($caseApps as $app) {
                $subParts = [];
                if ($app->applicant_phone) $subParts[] = 'هاتف: ' . $app->applicant_phone;
                if ($app->applicant_id_number) $subParts[] = 'رقم قومي: ' . $app->applicant_id_number;
                $subParts[] = $app->status_label;

                $searchParam = $app->applicant_id_number ?: ($app->applicant_phone ?: $app->applicant_name);
                $results['case_applications'][] = [
                    'id' => $app->id,
                    'title' => $app->applicant_name,
                    'code' => $app->reference_number ?: ('APP-' . $app->id),
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('case-applications.index', ['q' => $searchParam]),
                    'badge' => 'طلب بحث (' . $app->status_label . ')',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-clipboard2-pulse-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $visitsQuery = FieldVisit::query();
            ArabicSearchHelper::applySmartSearch(
                $visitsQuery,
                $cleanQ,
                ['patient_name', 'guardian_name', 'address', 'case_guide', 'hospital_name', 'tumor_location', 'report', 'recommendation'],
                ['national_id', 'phone', 'researcher_phone']
            );
            $visits = $visitsQuery->latest('id')->limit(4)->get();

            foreach ($visits as $visit) {
                $vName = $visit->patient_name ?: ($visit->guardian_name ?: ('بحث ميداني #' . $visit->id));
                $subParts = [];
                if ($visit->phone) $subParts[] = 'هاتف: ' . $visit->phone;
                if ($visit->national_id) $subParts[] = 'رقم قومي: ' . $visit->national_id;
                if ($visit->address) $subParts[] = $visit->address;

                $results['case_applications'][] = [
                    'id' => 'fv-' . $visit->id,
                    'title' => $vName,
                    'code' => 'VST-' . $visit->id,
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('visits.show', $visit),
                    'badge' => 'بحث ميداني',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-clipboard-check-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $psychQuery = PsychologicalSupportReport::query()->with(['beneficiary:id,code,full_name', 'familyMember:id,beneficiary_id,full_name,code']);
            ArabicSearchHelper::applySmartSearch(
                $psychQuery,
                $cleanQ,
                ['supporter_name', 'notes', 'key_challenges', 'positive_developments', 'recommendations'],
                ['month_year']
            );
            foreach ($psychQuery->latest('id')->limit(3)->get() as $ps) {
                $targetName = $ps->familyMember?->full_name ?: ($ps->beneficiary?->full_name ?: ('دعم نفسي #' . $ps->id));
                $results['case_applications'][] = [
                    'id' => 'ps-' . $ps->id,
                    'title' => 'تقرير دعم نفسي: ' . $targetName,
                    'code' => $ps->month_year ?: ('PSY-' . $ps->id),
                    'subtitle' => implode(' • ', array_filter([
                        $ps->supporter_name ? 'المتطوع/الأخصائي: ' . $ps->supporter_name : null,
                        $ps->contact_type_label ?? null,
                    ])),
                    'url' => $ps->beneficiary ? route('beneficiaries.show', $ps->beneficiary) : route('psychological-support.index'),
                    'badge' => 'دعم نفسي',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-chat-heart-fill',
                ];
            }
        });

        // 3. Donors / المتبرعون (System Donors + Website Donors)
        $safeRun(function () use ($cleanQ, &$results) {
            $donorsQuery = Donor::query();
            ArabicSearchHelper::applySmartSearch(
                $donorsQuery,
                $cleanQ,
                ['name', 'address', 'notes'],
                ['code', 'phone', 'national_id']
            );
            $donors = $donorsQuery
                ->select(['id', 'code', 'name', 'phone', 'national_id', 'classification', 'sponsorship_monthly_amount'])
                ->limit(7)
                ->get();

            foreach ($donors as $d) {
                $codeStr = $d->code ?: ('D-' . str_pad((string) $d->id, 5, '0', STR_PAD_LEFT));
                $subParts = [];
                if ($d->phone) $subParts[] = 'هاتف: ' . $d->phone;
                if ($d->national_id) $subParts[] = 'قومي: ' . $d->national_id;
                if ($d->classification === 'recurring') {
                    $subParts[] = 'متبرع شهري' . ($d->sponsorship_monthly_amount > 0 ? ' (' . number_format((float) $d->sponsorship_monthly_amount) . ' ج.م)' : '');
                } else {
                    $subParts[] = 'متبرع عام';
                }

                $results['donors'][] = [
                    'id' => $d->id,
                    'title' => $d->name,
                    'code' => $codeStr,
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('donors.show', $d),
                    'action_url' => route('donations.create', ['donor_id' => $d->id]),
                    'action_label' => 'تسجيل تبرع',
                    'badge' => 'متبرع',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-hand-thumbs-up-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            if (count($results['donors']) >= 7) return;
            $webDonorsQuery = WebDonor::query();
            ArabicSearchHelper::applySmartSearch($webDonorsQuery, $cleanQ, ['name'], ['phone', 'email']);
            foreach ($webDonorsQuery->limit(3)->get() as $wd) {
                $results['donors'][] = [
                    'id' => 'wd-' . $wd->id,
                    'title' => $wd->name,
                    'code' => 'WEB-' . $wd->id,
                    'subtitle' => implode(' • ', array_filter([$wd->phone ? 'هاتف: ' . $wd->phone : null, $wd->email])),
                    'url' => route('website.donation-accounts.show', $wd->id),
                    'badge' => 'متبرع أونلاين',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-globe2',
                ];
            }
        });

        // 4. Donations, Treasuries, Expenses & Accounts (التبرعات والخزائن والمصروفات والحسابات)
        $safeRun(function () use ($cleanQ, $numbers, &$results) {
            $donations = Donation::query()
                ->with(['donor:id,name,phone,code', 'project:id,name', 'campaign:id,name', 'guestHouse:id,name'])
                ->where(function ($w) use ($cleanQ, $numbers) {
                    ArabicSearchHelper::applySmartSearch(
                        $w,
                        $cleanQ,
                        ['purpose', 'category', 'notes', 'description'],
                        ['receipt_number', 'operation_number']
                    );
                    foreach ($numbers as $num) {
                        if (strlen($num) >= 2) {
                            $w->orWhere('receipt_number', 'like', "%{$num}%")
                              ->orWhere('operation_number', 'like', "%{$num}%")
                              ->orWhere('amount', $num);
                        }
                    }
                    $w->orWhereHas('donor', function ($dq) use ($cleanQ) {
                        ArabicSearchHelper::applySmartSearch($dq, $cleanQ, ['name'], ['code', 'phone']);
                    });
                })
                ->select(['id', 'receipt_number', 'operation_number', 'donor_id', 'project_id', 'campaign_id', 'guest_house_id', 'amount', 'estimated_value', 'type', 'purpose', 'created_at', 'status'])
                ->latest('id')
                ->limit(5)
                ->get();

            foreach ($donations as $dn) {
                $recCode = $dn->receipt_number ?: ($dn->operation_number ?: $dn->id);
                $val = $dn->type === 'cash' ? (float) $dn->amount : (float) $dn->estimated_value;
                $dest = $dn->project?->name ?: ($dn->campaign?->name ?: ($dn->guestHouse?->name ?: ($dn->purpose ?: 'عام')));
                $results['donations'][] = [
                    'id' => $dn->id,
                    'title' => 'إيصال #' . $recCode . ' — ' . ($dn->donor?->name ?? 'فاعل خير'),
                    'code' => 'REC-' . $recCode,
                    'subtitle' => 'المبلغ: ' . number_format($val, 2) . ' ج.م • الوجهة: ' . $dest,
                    'url' => route('donations.show', $dn),
                    'badge' => $dn->type === 'cash' ? 'تبرع نقدي' : 'تبرع عيني',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-receipt',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $treasuriesQuery = Treasury::query();
            ArabicSearchHelper::applySmartSearch($treasuriesQuery, $cleanQ, ['name', 'location', 'description'], ['code']);
            foreach ($treasuriesQuery->limit(4)->get() as $tr) {
                $results['donations'][] = [
                    'id' => 'tr-' . $tr->id,
                    'title' => $tr->name,
                    'code' => $tr->code ?: ('TR-' . $tr->id),
                    'subtitle' => 'الرصيد الحالي: ' . number_format((float) $tr->current_balance, 2) . ' ' . ($tr->currency ?: 'EGP'),
                    'url' => route('treasuries.show', $tr),
                    'badge' => 'خزينة / عهدة',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-safe2-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $expensesQuery = Expense::query()->with(['project:id,name', 'guestHouse:id,name', 'treasury:id,name']);
            ArabicSearchHelper::applySmartSearch($expensesQuery, $cleanQ, ['description', 'category', 'type'], []);
            foreach ($expensesQuery->latest('id')->limit(4)->get() as $exp) {
                $results['donations'][] = [
                    'id' => 'exp-' . $exp->id,
                    'title' => $exp->description ?: ('سند صرف: ' . ($exp->category ?: '#' . $exp->id)),
                    'code' => 'EXP-' . $exp->id,
                    'subtitle' => 'المبلغ: ' . number_format((float) $exp->amount, 2) . ' ج.م'
                        . ($exp->treasury ? ' • الخزينة: ' . $exp->treasury->name : '')
                        . ($exp->category ? ' • ' . $exp->category : ''),
                    'url' => route('expenses.show', $exp),
                    'badge' => 'سند مصروفات',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-arrow-up-right-circle-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $accountsQuery = Account::query();
            ArabicSearchHelper::applySmartSearch($accountsQuery, $cleanQ, ['name', 'description'], ['code']);
            foreach ($accountsQuery->limit(3)->get() as $acc) {
                $results['donations'][] = [
                    'id' => 'acc-' . $acc->id,
                    'title' => $acc->name,
                    'code' => $acc->code ?: ('ACC-' . $acc->id),
                    'subtitle' => 'حساب مالي' . ($acc->description ? ' • ' . $acc->description : ''),
                    'url' => route('accounts.show', $acc),
                    'badge' => 'دليل الحسابات',
                    'badge_class' => 'bg-secondary-subtle text-secondary',
                    'icon' => 'bi-journal-bookmark-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $jeQuery = JournalEntry::query();
            ArabicSearchHelper::applySmartSearch($jeQuery, $cleanQ, ['description'], ['reference_number']);
            foreach ($jeQuery->latest('id')->limit(2)->get() as $je) {
                $results['donations'][] = [
                    'id' => 'je-' . $je->id,
                    'title' => $je->description ?: ('قيد يومية #' . ($je->reference_number ?: $je->id)),
                    'code' => $je->reference_number ?: ('JE-' . $je->id),
                    'subtitle' => 'قيد محاسبي يومي',
                    'url' => route('journal-entries.show', $je),
                    'badge' => 'قيد يومية',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-journal-text',
                ];
            }
        });

        // 5. Projects, Campaigns, Guest Houses, Workspaces & Ramadan / المشاريع والحملات ودور الضيافة ومساحات العمل
        $safeRun(function () use ($cleanQ, &$results) {
            $projectsQuery = Project::query();
            ArabicSearchHelper::applySmartSearch($projectsQuery, $cleanQ, ['name', 'description'], []);
            foreach ($projectsQuery->limit(5)->get() as $proj) {
                $results['projects'][] = [
                    'id' => $proj->id,
                    'title' => $proj->name,
                    'code' => 'PRJ-' . $proj->id,
                    'subtitle' => $proj->description ?: 'مشروع خيري بالمنظومة',
                    'url' => route('projects.show', $proj),
                    'badge' => 'مشروع',
                    'badge_class' => 'bg-primary-subtle text-primary',
                    'icon' => 'bi-kanban-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $campaignsQuery = Campaign::query();
            ArabicSearchHelper::applySmartSearch($campaignsQuery, $cleanQ, ['name', 'description'], []);
            foreach ($campaignsQuery->limit(4)->get() as $camp) {
                $results['projects'][] = [
                    'id' => 'cmp-' . $camp->id,
                    'title' => $camp->name,
                    'code' => 'CMP-' . $camp->id,
                    'subtitle' => 'حملة خيرية موسمية',
                    'url' => route('campaigns.show', $camp),
                    'badge' => 'حملة',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-megaphone-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $guestHousesQuery = GuestHouse::query();
            ArabicSearchHelper::applySmartSearch($guestHousesQuery, $cleanQ, ['name', 'governorate', 'location', 'description'], ['phone']);
            foreach ($guestHousesQuery->limit(3)->get() as $gh) {
                $results['projects'][] = [
                    'id' => 'gh-' . $gh->id,
                    'title' => $gh->name,
                    'code' => 'GH-' . $gh->id,
                    'subtitle' => implode(' • ', array_filter([$gh->governorate, $gh->location, $gh->phone])),
                    'url' => route('guest-houses.show', $gh),
                    'badge' => 'دار ضيافة',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-house-heart-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $wsQuery = Workspace::query();
            ArabicSearchHelper::applySmartSearch($wsQuery, $cleanQ, ['name', 'location', 'description'], []);
            foreach ($wsQuery->limit(3)->get() as $ws) {
                $results['projects'][] = [
                    'id' => 'ws-' . $ws->id,
                    'title' => $ws->name,
                    'code' => 'WS-' . $ws->id,
                    'subtitle' => $ws->location ?: 'مساحة عمل / قاعة',
                    'url' => route('workspaces.show', $ws),
                    'badge' => 'مساحة عمل',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-easel2-fill',
                ];
            }

            $rentalQuery = WorkspaceRental::query()->with('workspace:id,name');
            ArabicSearchHelper::applySmartSearch($rentalQuery, $cleanQ, ['renter_name', 'notes'], ['renter_phone']);
            foreach ($rentalQuery->latest('id')->limit(3)->get() as $rt) {
                $results['projects'][] = [
                    'id' => 'wsr-' . $rt->id,
                    'title' => 'حجز مساحة عمل: ' . $rt->renter_name,
                    'code' => 'RNT-' . $rt->id,
                    'subtitle' => implode(' • ', array_filter([
                        $rt->workspace?->name,
                        $rt->renter_phone ? 'هاتف: ' . $rt->renter_phone : null,
                    ])),
                    'url' => $rt->workspace ? route('workspaces.show', $rt->workspace) : route('workspaces.index'),
                    'badge' => 'حجز قاعة',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-calendar2-check-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $rBagsQuery = RamadanBag::query();
            ArabicSearchHelper::applySmartSearch($rBagsQuery, $cleanQ, ['beneficiary_name', 'spouse_name', 'address', 'region', 'notes'], ['national_id', 'phone', 'phone_2']);
            foreach ($rBagsQuery->limit(3)->get() as $rb) {
                $results['projects'][] = [
                    'id' => 'rbag-' . $rb->id,
                    'title' => $rb->beneficiary_name,
                    'code' => 'BAG-' . $rb->id,
                    'subtitle' => implode(' • ', array_filter([$rb->region, $rb->phone ? 'هاتف: ' . $rb->phone : null, $rb->national_id ? 'قومي: ' . $rb->national_id : null])),
                    'url' => route('ramadan-bags.index', ['q' => $rb->beneficiary_name]),
                    'badge' => 'شنط رمضان',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-bag-heart-fill',
                ];
            }

            $rIftarsQuery = RamadanIftar::query();
            ArabicSearchHelper::applySmartSearch($rIftarsQuery, $cleanQ, ['beneficiary_name', 'nickname', 'guide_name', 'region', 'address', 'notes'], ['national_id', 'guide_phone', 'guide_phone_2']);
            foreach ($rIftarsQuery->limit(3)->get() as $ri) {
                $results['projects'][] = [
                    'id' => 'rift-' . $ri->id,
                    'title' => $ri->beneficiary_name,
                    'code' => 'IFT-' . $ri->id,
                    'subtitle' => implode(' • ', array_filter([$ri->region, $ri->guide_name ? 'الدليل: ' . $ri->guide_name : null, $ri->guide_phone ? 'هاتف: ' . $ri->guide_phone : null])),
                    'url' => route('ramadan-iftars.index', ['q' => $ri->beneficiary_name]),
                    'badge' => 'إفطار رمضان',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-cup-hot-fill',
                ];
            }
        });

        // 6. Staff / Employees & Tasks (الموظفون وفريق العمل والمهام)
        $safeRun(function () use ($cleanQ, &$results) {
            $employeesQuery = User::query()
                ->where(function ($w) {
                    $w->where('is_employee', true)
                      ->orWhere(function ($sq) {
                          $sq->where('is_volunteer', false)->orWhereNull('is_volunteer');
                      });
                });
            ArabicSearchHelper::applySmartSearch(
                $employeesQuery,
                $cleanQ,
                ['name', 'job_title', 'department', 'governorate', 'city'],
                ['phone', 'national_id', 'email', 'code']
            );
            $employees = $employeesQuery
                ->select(['id', 'code', 'name', 'phone', 'national_id', 'job_title', 'department', 'role'])
                ->limit(6)
                ->get();

            foreach ($employees as $emp) {
                $subParts = [];
                if ($emp->job_title) $subParts[] = $emp->job_title;
                if ($emp->department) $subParts[] = $emp->department;
                if ($emp->phone) $subParts[] = 'هاتف: ' . $emp->phone;
                if ($emp->national_id) $subParts[] = 'قومي: ' . $emp->national_id;

                $results['employees'][] = [
                    'id' => $emp->id,
                    'title' => $emp->name,
                    'code' => $emp->code ?: ('EMP-' . $emp->id),
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('users.show', $emp),
                    'badge' => 'موظف / فريق العمل',
                    'badge_class' => 'bg-primary-subtle text-primary',
                    'icon' => 'bi-person-badge-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $tasksQuery = Task::query()->with('assignee:id,name');
            ArabicSearchHelper::applySmartSearch($tasksQuery, $cleanQ, ['title', 'volunteer_activity_name', 'description'], []);
            foreach ($tasksQuery->latest('id')->limit(3)->get() as $tsk) {
                $results['employees'][] = [
                    'id' => 'tsk-' . $tsk->id,
                    'title' => $tsk->title,
                    'code' => 'TSK-' . $tsk->id,
                    'subtitle' => implode(' • ', array_filter([
                        $tsk->assignee ? 'المكلف: ' . $tsk->assignee->name : null,
                        $tsk->due_date ? 'الاستحقاق: ' . $tsk->due_date->format('Y-m-d') : null,
                    ])),
                    'url' => route('tasks.index', ['q' => $tsk->title]),
                    'badge' => 'مهمة عمل',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-check2-square',
                ];
            }
        });

        // 7. Volunteers & Volunteer Requests (المتطوعون وطلبات التطوع)
        $safeRun(function () use ($cleanQ, &$results) {
            $volunteersQuery = User::query()->where('is_volunteer', true);
            ArabicSearchHelper::applySmartSearch(
                $volunteersQuery,
                $cleanQ,
                ['name', 'city', 'governorate', 'college', 'project_role'],
                ['phone', 'national_id', 'code', 'email']
            );
            $volunteers = $volunteersQuery
                ->select(['id', 'code', 'name', 'phone', 'national_id', 'city', 'volunteer_hours'])
                ->limit(6)
                ->get();

            foreach ($volunteers as $v) {
                $subParts = [];
                if ($v->phone) $subParts[] = 'هاتف: ' . $v->phone;
                if ($v->national_id) $subParts[] = 'قومي: ' . $v->national_id;
                if ($v->city) $subParts[] = $v->city;
                if ($v->volunteer_hours) $subParts[] = $v->volunteer_hours . ' ساعة تطوع';

                $results['volunteers'][] = [
                    'id' => $v->id,
                    'title' => $v->name,
                    'code' => $v->code ?: ('V-' . $v->id),
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('volunteers.show', $v),
                    'badge' => 'متطوع',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-people-fill',
                ];
            }
        });

        $safeRun(function () use ($cleanQ, &$results) {
            $webVolReqQuery = WebVolunteerRequest::query();
            ArabicSearchHelper::applySmartSearch(
                $webVolReqQuery,
                $cleanQ,
                ['name', 'area_of_interest', 'faculty', 'university', 'address'],
                ['phone', 'national_id', 'email', 'tracking_code']
            );
            foreach ($webVolReqQuery->latest('id')->limit(3)->get() as $wvr) {
                $results['volunteers'][] = [
                    'id' => 'wvr-' . $wvr->id,
                    'title' => $wvr->name,
                    'code' => $wvr->tracking_code ?: ('VOL-' . $wvr->id),
                    'subtitle' => implode(' • ', array_filter([$wvr->phone ? 'هاتف: ' . $wvr->phone : null, $wvr->area_of_interest, $wvr->status_label ?? null])),
                    'url' => route('website.volunteer-requests.index', ['q' => $wvr->name]),
                    'badge' => 'طلب تطوع (الموقع)',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-person-plus-fill',
                ];
            }

            $mobVolReqQuery = MobileVolunteerRequest::query();
            ArabicSearchHelper::applySmartSearch(
                $mobVolReqQuery,
                $cleanQ,
                ['name', 'area_of_interest', 'faculty', 'university', 'address'],
                ['phone', 'national_id', 'email']
            );
            foreach ($mobVolReqQuery->latest('id')->limit(3)->get() as $mvr) {
                $results['volunteers'][] = [
                    'id' => 'mvr-' . $mvr->id,
                    'title' => $mvr->name,
                    'code' => 'MVOL-' . $mvr->id,
                    'subtitle' => implode(' • ', array_filter([$mvr->phone ? 'هاتف: ' . $mvr->phone : null, $mvr->area_of_interest])),
                    'url' => route('mobile.volunteer-requests.index', ['q' => $mvr->name]),
                    'badge' => 'طلب تطوع (التطبيق)',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-phone-fill',
                ];
            }
        });

        // 8. Inventory, Warehouses, Items & Suppliers (المخازن والأصناف والموردون)
        $safeRun(function () use ($cleanQ, &$results) {
            $whQuery = Warehouse::query();
            ArabicSearchHelper::applySmartSearch($whQuery, $cleanQ, ['name', 'location', 'department', 'address'], ['phone']);
            foreach ($whQuery->limit(4)->get() as $wh) {
                $results['inventory'][] = [
                    'id' => 'wh-' . $wh->id,
                    'title' => $wh->name,
                    'code' => 'WH-' . $wh->id,
                    'subtitle' => implode(' • ', array_filter([$wh->location, $wh->address, $wh->phone ? 'هاتف: ' . $wh->phone : null])),
                    'url' => route('warehouses.show', $wh),
                    'badge' => 'مخزن / مستودع',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-building',
                ];
            }

            $itemQuery = Item::query();
            ArabicSearchHelper::applySmartSearch($itemQuery, $cleanQ, ['name', 'category', 'unit'], ['sku', 'barcode']);
            foreach ($itemQuery->limit(5)->get() as $it) {
                $results['inventory'][] = [
                    'id' => 'itm-' . $it->id,
                    'title' => $it->name,
                    'code' => $it->sku ?: ('ITM-' . $it->id),
                    'subtitle' => implode(' • ', array_filter([$it->category, $it->unit ? 'الوحدة: ' . $it->unit : null])),
                    'url' => route('items.show', $it),
                    'badge' => 'صنف مخزني',
                    'badge_class' => 'bg-primary-subtle text-primary',
                    'icon' => 'bi-box-seam-fill',
                ];
            }

            $supQuery = Supplier::query();
            ArabicSearchHelper::applySmartSearch($supQuery, $cleanQ, ['name', 'merchant_name', 'source_name', 'address', 'notes'], ['phone']);
            foreach ($supQuery->limit(4)->get() as $sup) {
                $results['inventory'][] = [
                    'id' => 'sup-' . $sup->id,
                    'title' => $sup->name,
                    'code' => 'SUP-' . $sup->id,
                    'subtitle' => implode(' • ', array_filter([$sup->merchant_name, $sup->phone ? 'هاتف: ' . $sup->phone : null, $sup->address])),
                    'url' => route('suppliers.show', $sup),
                    'badge' => 'مورد',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-shop',
                ];
            }
        });

        // 9. Logistics, Delegates, Routes & Regional Services (اللوجيستك والمناديب وخطوط السير)
        $safeRun(function () use ($cleanQ, &$results) {
            $delQuery = Delegate::query();
            ArabicSearchHelper::applySmartSearch(
                $delQuery,
                $cleanQ,
                ['name', 'vehicle_type', 'vehicle_plate', 'address', 'notes'],
                ['phone', 'emergency_phone', 'national_id', 'license_number']
            );
            foreach ($delQuery->limit(4)->get() as $del) {
                $results['logistics'][] = [
                    'id' => 'del-' . $del->id,
                    'title' => $del->name,
                    'code' => 'DEL-' . $del->id,
                    'subtitle' => implode(' • ', array_filter([$del->phone ? 'هاتف: ' . $del->phone : null, $del->vehicle_type, $del->address])),
                    'url' => route('delegates.show', $del),
                    'badge' => 'مندوب تحصيل/توصيل',
                    'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                    'icon' => 'bi-person-badge',
                ];
            }

            $routeQuery = TravelRoute::query();
            ArabicSearchHelper::applySmartSearch($routeQuery, $cleanQ, ['name', 'description'], []);
            foreach ($routeQuery->limit(3)->get() as $tr) {
                $results['logistics'][] = [
                    'id' => 'rt-' . $tr->id,
                    'title' => $tr->name,
                    'code' => 'RTE-' . $tr->id,
                    'subtitle' => $tr->description ?: 'خط سير لوجيستك',
                    'url' => route('travel-routes.show', $tr),
                    'badge' => 'خط سير',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-signpost-2-fill',
                ];
            }

            $kfsServices = KafrElSheikhService::query();
            ArabicSearchHelper::applySmartSearch($kfsServices, $cleanQ, ['name', 'service_type', 'notes'], ['phone']);
            foreach ($kfsServices->limit(2)->get() as $ks) {
                $results['logistics'][] = [
                    'id' => 'kfs-s-' . $ks->id,
                    'title' => $ks->name,
                    'code' => 'KFS-' . $ks->id,
                    'subtitle' => implode(' • ', array_filter([$ks->service_type, $ks->phone ? 'هاتف: ' . $ks->phone : null])),
                    'url' => route('kafr-el-sheikh-services.index'),
                    'badge' => 'خدمات كفر الشيخ',
                    'badge_class' => 'bg-primary-subtle text-primary',
                    'icon' => 'bi-truck',
                ];
            }

            $kfsBrokers = KafrElSheikhBroker::query();
            ArabicSearchHelper::applySmartSearch($kfsBrokers, $cleanQ, ['name', 'area', 'notes'], ['phone']);
            foreach ($kfsBrokers->limit(2)->get() as $kb) {
                $results['logistics'][] = [
                    'id' => 'kfs-b-' . $kb->id,
                    'title' => $kb->name,
                    'code' => 'BRK-' . $kb->id,
                    'subtitle' => implode(' • ', array_filter([$kb->area, $kb->phone ? 'هاتف: ' . $kb->phone : null])),
                    'url' => route('kafr-el-sheikh-brokers.index'),
                    'badge' => 'سمسار كفر الشيخ',
                    'badge_class' => 'bg-secondary-subtle text-secondary',
                    'icon' => 'bi-people',
                ];
            }

            $kfsDeliveries = KafrElSheikhDelivery::query();
            ArabicSearchHelper::applySmartSearch($kfsDeliveries, $cleanQ, ['name', 'vehicle_type', 'notes'], ['phone']);
            foreach ($kfsDeliveries->limit(2)->get() as $kd) {
                $results['logistics'][] = [
                    'id' => 'kfs-d-' . $kd->id,
                    'title' => $kd->name,
                    'code' => 'DLV-' . $kd->id,
                    'subtitle' => implode(' • ', array_filter([$kd->vehicle_type, $kd->phone ? 'هاتف: ' . $kd->phone : null])),
                    'url' => route('kafr-el-sheikh-deliveries.index'),
                    'badge' => 'توصيل كفر الشيخ',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-bicycle',
                ];
            }

            $tantaWorkers = TantaWorker::query();
            ArabicSearchHelper::applySmartSearch($tantaWorkers, $cleanQ, ['name', 'profession', 'notes'], ['phone']);
            foreach ($tantaWorkers->limit(2)->get() as $tw) {
                $results['logistics'][] = [
                    'id' => 'tw-' . $tw->id,
                    'title' => $tw->name,
                    'code' => 'TNT-' . $tw->id,
                    'subtitle' => implode(' • ', array_filter([$tw->profession, $tw->phone ? 'هاتف: ' . $tw->phone : null])),
                    'url' => route('tanta-workers.index'),
                    'badge' => 'عامل طنطا',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-person-workspace',
                ];
            }
        });

        // 10. Collaborations, Partnerships, Memberships & Medicine Reps (التعاونات والشراكات والعضويات)
        $safeRun(function () use ($cleanQ, &$results) {
            $collabsQuery = Collaboration::query();
            ArabicSearchHelper::applySmartSearch(
                $collabsQuery,
                $cleanQ,
                ['name', 'specialty', 'service_provided', 'contact_person', 'category', 'city', 'address', 'notes'],
                ['phone', 'phone_alt', 'contact_person_phone']
            );
            foreach ($collabsQuery->limit(5)->get() as $col) {
                $subParts = [];
                if ($col->category) $subParts[] = $col->category;
                if ($col->specialty) $subParts[] = $col->specialty;
                if ($col->phone) $subParts[] = 'هاتف: ' . $col->phone;
                elseif ($col->contact_person_phone) $subParts[] = 'هاتف: ' . $col->contact_person_phone;

                $results['collaborations'][] = [
                    'id' => $col->id,
                    'title' => $col->name,
                    'code' => 'COL-' . $col->id,
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('collaborations.index', ['search' => $col->name]),
                    'badge' => 'شراكة وتعاون',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => $col->category_icon ?: 'bi-diagram-3-fill',
                ];
            }

            $schoolCollabsQuery = SchoolCollaboration::query();
            ArabicSearchHelper::applySmartSearch($schoolCollabsQuery, $cleanQ, ['shop_name', 'address', 'campaign', 'notes'], ['phone']);
            foreach ($schoolCollabsQuery->limit(3)->get() as $sc) {
                $subParts = [];
                if ($sc->phone) $subParts[] = 'هاتف: ' . $sc->phone;
                if ($sc->address) $subParts[] = $sc->address;
                if ($sc->discount) $subParts[] = 'خصم: ' . $sc->discount;

                $results['collaborations'][] = [
                    'id' => 'sc-' . $sc->id,
                    'title' => $sc->shop_name,
                    'code' => 'SCH-' . $sc->id,
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('school-collaborations.index', ['q' => $sc->shop_name]),
                    'badge' => 'تعاون مدارس',
                    'badge_class' => 'bg-success-subtle text-success',
                    'icon' => 'bi-shop',
                ];
            }

            $memQuery = Membership::query();
            ArabicSearchHelper::applySmartSearch(
                $memQuery,
                $cleanQ,
                ['entity_name', 'entity_type', 'service_provided', 'entity_address', 'entity_contact_name', 'notes'],
                ['contact_number', 'contact_person_number', 'email']
            );
            foreach ($memQuery->limit(3)->get() as $mem) {
                $results['collaborations'][] = [
                    'id' => 'mem-' . $mem->id,
                    'title' => $mem->entity_name,
                    'code' => 'MEM-' . $mem->id,
                    'subtitle' => implode(' • ', array_filter([$mem->entity_type, $mem->service_provided, $mem->contact_number ? 'هاتف: ' . $mem->contact_number : null])),
                    'url' => route('memberships.index', ['q' => $mem->entity_name]),
                    'badge' => 'عضوية / بروتوكول',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-card-checklist',
                ];
            }

            $medRepQuery = OncologyMedicineRep::query();
            ArabicSearchHelper::applySmartSearch($medRepQuery, $cleanQ, ['name', 'company', 'notes'], ['phone']);
            foreach ($medRepQuery->limit(3)->get() as $mr) {
                $results['collaborations'][] = [
                    'id' => 'omr-' . $mr->id,
                    'title' => $mr->name,
                    'code' => 'MED-' . $mr->id,
                    'subtitle' => implode(' • ', array_filter([$mr->company ? 'الشركة: ' . $mr->company : null, $mr->phone ? 'هاتف: ' . $mr->phone : null])),
                    'url' => route('oncology-medicine-reps.index', ['q' => $mr->name]),
                    'badge' => 'مندوب أدوية أورام',
                    'badge_class' => 'bg-danger-subtle text-danger',
                    'icon' => 'bi-capsule',
                ];
            }
        });

        // 11. Reception & Complaints / الاستقبال والشكاوى والمقترحات
        $safeRun(function () use ($cleanQ, &$results) {
            $complaintsQuery = Complaint::query();
            ArabicSearchHelper::applySmartSearch(
                $complaintsQuery,
                $cleanQ,
                ['complainant_name', 'subject', 'message'],
                ['tracking_code', 'complainant_phone']
            );
            foreach ($complaintsQuery->latest('id')->limit(3)->get() as $comp) {
                $subParts = [];
                if ($comp->complainant_phone) $subParts[] = 'هاتف: ' . $comp->complainant_phone;
                if ($comp->subject) $subParts[] = $comp->subject;

                $results['complaints'][] = [
                    'id' => $comp->id,
                    'title' => $comp->complainant_name ?: ('شكوى #' . $comp->tracking_code),
                    'code' => $comp->tracking_code ?: ('CMP-' . $comp->id),
                    'subtitle' => implode(' • ', $subParts),
                    'url' => route('complaints.index', ['q' => $comp->tracking_code ?: $comp->complainant_name]),
                    'badge' => 'شكوى / مقترح',
                    'badge_class' => 'bg-secondary-subtle text-secondary',
                    'icon' => 'bi-chat-dots-fill',
                ];
            }

            $recQuery = ReceptionLog::query();
            ArabicSearchHelper::applySmartSearch($recQuery, $cleanQ, ['visitor_name', 'purpose', 'directed_to', 'notes'], ['phone']);
            foreach ($recQuery->latest('id')->limit(3)->get() as $rec) {
                $results['complaints'][] = [
                    'id' => 'rec-' . $rec->id,
                    'title' => $rec->visitor_name,
                    'code' => 'RCP-' . $rec->id,
                    'subtitle' => implode(' • ', array_filter([$rec->purpose_label ?? $rec->purpose, $rec->phone ? 'هاتف: ' . $rec->phone : null, $rec->directed_to])),
                    'url' => route('reception.index', ['q' => $rec->visitor_name]),
                    'badge' => 'سجل الاستقبال',
                    'badge_class' => 'bg-info-subtle text-info',
                    'icon' => 'bi-telephone-inbound-fill',
                ];
            }
        });

        $totalCount = 0;
        foreach ($results as $items) {
            $totalCount += count($items);
        }

        return response()->json([
            'query' => $q,
            'total' => $totalCount,
            'categories' => [
                [
                    'key' => 'system_pages',
                    'title' => 'أقسام وصفحات النظام السريعة',
                    'icon' => 'bi-grid-fill',
                    'color' => 'text-primary',
                    'count' => count($results['system_pages']),
                    'items' => $results['system_pages'],
                ],
                [
                    'key' => 'beneficiaries',
                    'title' => 'الحالات والمستفيدون وأفراد الأسرة',
                    'icon' => 'bi-heart-pulse-fill',
                    'color' => 'text-danger',
                    'count' => count($results['beneficiaries']),
                    'items' => $results['beneficiaries'],
                ],
                [
                    'key' => 'case_applications',
                    'title' => 'طلبات التقديم والأبحاث الميدانية والدعم النفسي',
                    'icon' => 'bi-clipboard2-pulse-fill',
                    'color' => 'text-warning',
                    'count' => count($results['case_applications']),
                    'items' => $results['case_applications'],
                ],
                [
                    'key' => 'donors',
                    'title' => 'المتبرعون والكفلاء',
                    'icon' => 'bi-hand-thumbs-up-fill',
                    'color' => 'text-success',
                    'count' => count($results['donors']),
                    'items' => $results['donors'],
                ],
                [
                    'key' => 'donations',
                    'title' => 'الإيصالات والخزائن والمصروفات والحسابات',
                    'icon' => 'bi-receipt',
                    'color' => 'text-info',
                    'count' => count($results['donations']),
                    'items' => $results['donations'],
                ],
                [
                    'key' => 'projects',
                    'title' => 'المشاريع والحملات ودور الضيافة ومساحات العمل',
                    'icon' => 'bi-kanban-fill',
                    'color' => 'text-primary',
                    'count' => count($results['projects']),
                    'items' => $results['projects'],
                ],
                [
                    'key' => 'employees',
                    'title' => 'الموظفون وفريق العمل والمهام',
                    'icon' => 'bi-person-badge-fill',
                    'color' => 'text-primary',
                    'count' => count($results['employees']),
                    'items' => $results['employees'],
                ],
                [
                    'key' => 'volunteers',
                    'title' => 'المتطوعون وطلبات التطوع',
                    'icon' => 'bi-people-fill',
                    'color' => 'text-warning',
                    'count' => count($results['volunteers']),
                    'items' => $results['volunteers'],
                ],
                [
                    'key' => 'inventory',
                    'title' => 'المخازن والأصناف والموردون',
                    'icon' => 'bi-boxes',
                    'color' => 'text-warning',
                    'count' => count($results['inventory']),
                    'items' => $results['inventory'],
                ],
                [
                    'key' => 'logistics',
                    'title' => 'اللوجيستك والمناديب وخطوط السير والخدمات',
                    'icon' => 'bi-truck',
                    'color' => 'text-danger',
                    'count' => count($results['logistics']),
                    'items' => $results['logistics'],
                ],
                [
                    'key' => 'collaborations',
                    'title' => 'التعاونات والشراكات والعضويات ومندوبي الأدوية',
                    'icon' => 'bi-diagram-3-fill',
                    'color' => 'text-success',
                    'count' => count($results['collaborations']),
                    'items' => $results['collaborations'],
                ],
                [
                    'key' => 'complaints',
                    'title' => 'الاستقبال والشكاوى والمقترحات',
                    'icon' => 'bi-chat-dots-fill',
                    'color' => 'text-secondary',
                    'count' => count($results['complaints']),
                    'items' => $results['complaints'],
                ],
            ],
            'reports_url' => route('reports.index', ['q' => $q]),
        ]);
    }
}
