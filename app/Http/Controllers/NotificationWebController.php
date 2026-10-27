<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Complaint;
use App\Models\Task;
use App\Models\VolunteerAttendance;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\JournalEntry;
use App\Models\Payroll;
use App\Models\Audit;
use App\Models\Delegate;
use App\Models\GuestHouse;
use App\Models\User;
use App\Models\InventoryTransaction;
use App\Models\ChangeRequest;
use App\Models\WebContactMessage;
use App\Models\MobileCaseApplication;
use App\Models\FieldVisit;
use App\Models\Leave;
use App\Models\EmployeeAttendance;
use App\Models\MobileNotification;
use App\Services\MonthlyDonationReminderService;

final class NotificationWebController extends Controller
{
    public function __construct(
        private readonly MonthlyDonationReminderService $monthlyDonationReminderService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $category = $request->input('category');
        $type = $request->input('type');
        $readStatus = $request->input('read_status');

        $rawItems = [];
        $suggestions = [];
        $isAdmin = $user->hasRole('admin') || $user->hasRole('super_admin');
        $isManager = $user->hasRole('manager');

        // Helper check closure matching role, permissions, or job title
        $canAccess = function (string $permission, array $roles = [], array $jobKeywords = []) use ($user, $isAdmin, $isManager): bool {
            if ($isAdmin || $isManager) return true;
            if ($permission !== '' && $user->hasPermission($permission)) return true;
            foreach ($roles as $role) {
                if ($user->hasRole($role)) return true;
            }
            $userJob = mb_strtolower((string) ($user->job_title . ' ' . $user->department));
            foreach ($jobKeywords as $keyword) {
                if ($keyword !== '' && str_contains($userJob, mb_strtolower($keyword))) return true;
            }
            return false;
        };

        // ==========================================
        // 0. الإشعارات والتنبيهات الموجهة للموظف (حسب الوظيفة / القسم / الاسم)
        // ==========================================
        $targetedNotifications = MobileNotification::forUser($user)
            ->where('is_sent', true)
            ->latest('sent_at')
            ->limit(15)
            ->get();

        foreach ($targetedNotifications as $tNotif) {
            $rawDate = $tNotif->sent_at ?: $tNotif->created_at;
            $ts = $rawDate instanceof \DateTimeInterface 
                ? $rawDate->getTimestamp() 
                : (!empty($rawDate) ? strtotime((string) $rawDate) : time());
            $notifType = match ($tNotif->category) {
                'عاجل' => 'danger',
                'مهام' => 'warning',
                'حملات' => 'success',
                default => 'info',
            };
            $targetDesc = $tNotif->target_type === 'user' ? 'إشعار خاص بك من الإدارة' : $tNotif->target_description;
            $rawItems[] = [
                'id' => 'targeted_' . $tNotif->id,
                'category' => 'targeted_employee',
                'type' => $notifType,
                'title' => 'تنبيه موجه: ' . $tNotif->title,
                'text' => $tNotif->body . ' (' . $targetDesc . ')',
                'link' => route('notifications.index'),
                'timestamp' => $ts,
            ];
        }

        // ==========================================
        // 1. الحالات وطلبات المساعدة (المقبولة والمرفوضة والجديدة)
        // ==========================================
        if ($canAccess('beneficiaries.view', ['social_researcher', 'researcher', 'field_researcher'], ['باحث', 'رعاية', 'اجتماعي', 'حالات']) || $user->hasPermission('cases.view')) {
            // أ) الحالات المقبولة والمعتمدة حديثاً (لكل حالة إشعار بتوقيتها)
            $recentAcceptedCases = MobileCaseApplication::whereIn('status', ['approved', 'completed'])
                ->where('updated_at', '>=', now()->subDays(14))
                ->latest('updated_at')
                ->limit(8)
                ->get();

            foreach ($recentAcceptedCases as $c) {
                $codeStr = $c->case_code ? " (كود: {$c->case_code})" : '';
                $rawItems[] = [
                    'id' => 'case_accepted_' . $c->id,
                    'category' => 'cases',
                    'type' => 'success',
                    'title' => 'تم قبول واعتماد الحالة',
                    'text' => "تم اعتماد وقبول بحث الحالة: {$c->applicant_name}{$codeStr} وجاهزة لربط المساعدات والزيارات",
                    'link' => route('case-applications.index', ['search' => $c->applicant_name]),
                    'timestamp' => $c->updated_at ? $c->updated_at->timestamp : now()->timestamp,
                ];
            }

            if ($recentAcceptedCases->isNotEmpty()) {
                $suggestions[] = [
                    'text' => 'عرض ومتابعة الحالات المقبولة والمعتمدة',
                    'link' => route('case-applications.index', ['status' => 'completed']),
                ];
            }

            // ب) الحالات المرفوضة
            $recentRejectedCases = MobileCaseApplication::where('status', 'rejected')
                ->where('updated_at', '>=', now()->subDays(14))
                ->latest('updated_at')
                ->limit(6)
                ->get();

            foreach ($recentRejectedCases as $c) {
                $reason = $c->rejection_reason ? " — سبب الرفض: {$c->rejection_reason}" : '';
                $rawItems[] = [
                    'id' => 'case_rejected_' . $c->id,
                    'category' => 'cases',
                    'type' => 'danger',
                    'title' => 'حالة مرفوضة بالبحث المبدئي',
                    'text' => "تم رفض طلب الحالة: {$c->applicant_name}{$reason}",
                    'link' => route('case-applications.index', ['search' => $c->applicant_name]),
                    'timestamp' => $c->updated_at ? $c->updated_at->timestamp : now()->timestamp,
                ];
            }

            // ج) طلبات حالات جديدة معلقة تنتظر الفحص وتعيين الباحث
            $pendingCases = MobileCaseApplication::where('status', 'pending')
                ->latest('created_at')
                ->limit(6)
                ->get();

            foreach ($pendingCases as $c) {
                $gov = $c->governorate ? " ({$c->governorate})" : '';
                $rawItems[] = [
                    'id' => 'case_pending_' . $c->id,
                    'category' => 'cases',
                    'type' => 'warning',
                    'title' => 'طلب مساعدة جديد ينتظر الفحص',
                    'text' => "طلب حالة جديد ينتظر الفحص وتعيين باحث: {$c->applicant_name}{$gov}",
                    'link' => route('case-applications.index', ['search' => $c->applicant_name]),
                    'timestamp' => $c->created_at ? $c->created_at->timestamp : now()->timestamp,
                ];
            }

            // د) حالات مسندة للباحث/الموظف الحالي شخصياً
            $myAssignedCases = MobileCaseApplication::where('assigned_researcher_id', $user->id)
                ->whereIn('status', ['pending', 'under_research'])
                ->latest('updated_at')
                ->limit(6)
                ->get();

            foreach ($myAssignedCases as $c) {
                $rawItems[] = [
                    'id' => 'case_assigned_' . $c->id,
                    'category' => 'cases',
                    'type' => 'warning',
                    'title' => 'حالة مسندة إليك شخصياً',
                    'text' => "لديك حالة مسندة لإجراء البحث الميداني: {$c->applicant_name}",
                    'link' => route('case-applications.index', ['search' => $c->applicant_name]),
                    'timestamp' => $c->updated_at ? $c->updated_at->timestamp : now()->timestamp,
                ];
            }
        }

        // ==========================================
        // 2. الزيارات الميدانية للموظفين والباحثين
        // ==========================================
        if ($canAccess('field_visits.view', ['social_researcher', 'researcher', 'field_researcher']) || $user->hasPermission('visits.view') || $isAdmin) {
            $myDueVisits = FieldVisit::with('caseApplication')
                ->where('researcher_id', $user->id)
                ->where('status', 'scheduled')
                ->whereDate('visit_date', '<=', now()->toDateString())
                ->latest('visit_date')
                ->limit(6)
                ->get();

            foreach ($myDueVisits as $v) {
                $isOverdue = $v->visit_date && $v->visit_date < now()->toDateString();
                $cName = $v->caseApplication?->applicant_name ?? 'مستفيد';
                $rawItems[] = [
                    'id' => 'visit_due_' . $v->id,
                    'category' => 'field_visits',
                    'type' => $isOverdue ? 'danger' : 'warning',
                    'title' => $isOverdue ? 'زيارة ميدانية متأخرة' : 'زيارة ميدانية مجدولة',
                    'text' => ($isOverdue ? 'تنبيه عاجل: زيارة ميدانية متأخرة تجاوزت موعدها لـ: ' : 'زيارة ميدانية مجدولة لـ: ') . "{$cName} (تاريخ: {$v->visit_date})",
                    'link' => route('visits.index'),
                    'timestamp' => $v->created_at ? $v->created_at->timestamp : now()->timestamp,
                ];
            }

            if ($myDueVisits->isNotEmpty()) {
                $suggestions[] = [
                    'text' => 'استيفاء تقارير الزيارات الميدانية المجدولة',
                    'link' => route('visits.index'),
                ];
            }
        }

        // ==========================================
        // 3. المهام الخاصة بالموظف وفريق العمل
        // ==========================================
        $myTasks = Task::where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $user->id));
            })
            ->where('status', '!=', 'done')
            ->latest('created_at')
            ->limit(6)
            ->get();

        foreach ($myTasks as $t) {
            $isOverdue = $t->due_date && $t->due_date->toDateString() < now()->toDateString();
            $rawItems[] = [
                'id' => 'task_' . $t->id,
                'category' => 'tasks',
                'type' => $isOverdue ? 'danger' : 'info',
                'title' => $isOverdue ? 'مهمة عمل متأخرة' : 'مهمة عمل مسندة إليك',
                'text' => $t->title . ($t->due_date ? ' (استحقاق: ' . $t->due_date->format('Y-m-d') . ')' : ''),
                'link' => route('tasks.index'),
                'timestamp' => $t->created_at ? $t->created_at->timestamp : now()->timestamp,
            ];
        }

        // ==========================================
        // 4. الموارد البشرية والإجازات وشؤون الموظفين
        // ==========================================
        if ($canAccess('leaves.view', ['hr']) || $isAdmin) {
            $pendingLeaves = Leave::with('user')->where('status', 'pending')->latest('created_at')->limit(6)->get();
            foreach ($pendingLeaves as $l) {
                $uName = $l->user?->name ?? 'موظف';
                $rawItems[] = [
                    'id' => 'leave_pending_' . $l->id,
                    'category' => 'hr',
                    'type' => 'warning',
                    'title' => 'طلب إجازة معلق للمراجعة',
                    'text' => "طلب إجازة جديد ينتظر الاعتماد من: {$uName} ({$l->days} يوم - تبدأ {$l->start_date})",
                    'link' => route('leaves.index'),
                    'timestamp' => $l->created_at ? $l->created_at->timestamp : now()->timestamp,
                ];
            }
            if ($pendingLeaves->isNotEmpty()) {
                $suggestions[] = [
                    'text' => 'اعتماد ومراجعة طلبات إجازات الموظفين',
                    'link' => route('leaves.index'),
                ];
            }
        }

        $myLeaves = Leave::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('updated_at', '>=', now()->subDays(14))
            ->latest('updated_at')
            ->limit(4)
            ->get();

        foreach ($myLeaves as $l) {
            $isApproved = $l->status === 'approved';
            $rawItems[] = [
                'id' => 'my_leave_' . $l->id . '_' . $l->status,
                'category' => 'hr',
                'type' => $isApproved ? 'success' : 'danger',
                'title' => $isApproved ? 'تمت الموافقة على إجازتك' : 'تم رفض طلب الإجازة',
                'text' => ($isApproved ? 'تمت الموافقة على طلب إجازتك بنجاح (' : 'تم رفض طلب إجازتك (') . "{$l->days} يوم)",
                'link' => route('leaves.index'),
                'timestamp' => $l->updated_at ? $l->updated_at->timestamp : now()->timestamp,
            ];
        }

        // ==========================================
        // 5. تذكير الحضور والانصراف اليومي للموظف
        // ==========================================
        if (now()->hour >= 8 && now()->isWeekday()) {
            $myAttendanceToday = EmployeeAttendance::where('user_id', $user->id)
                ->whereDate('date', now()->toDateString())
                ->exists();
            if (! $myAttendanceToday) {
                $rawItems[] = [
                    'id' => 'attendance_' . now()->toDateString(),
                    'category' => 'attendance',
                    'type' => 'warning',
                    'title' => 'تذكير الحضور والانصراف',
                    'text' => 'تذكير: لم تقم بتسجيل حضورك في منظومة العمل اليوم بعد',
                    'link' => route('employee-attendance.index'),
                    'timestamp' => now()->startOfDay()->addHours(8)->timestamp,
                ];
                $suggestions[] = [
                    'text' => 'تسجيل الحضور اليومي في النظام',
                    'link' => route('employee-attendance.index'),
                ];
            }
        }

        // ==========================================
        // 6. Monthly Donation Reminders
        // ==========================================
        if ($canAccess('donors.view') || $user->hasPermission('donations.view')) {
            $monthlyDonationItems = $this->monthlyDonationReminderService->notificationItems();
            foreach ($monthlyDonationItems as $idx => $mItem) {
                $rawItems[] = [
                    'id' => 'monthly_don_' . $idx . '_' . now()->format('Y-m-d'),
                    'category' => 'monthly_donations',
                    'type' => $mItem['type'] ?? 'info',
                    'title' => 'تذكير تبرع شهري دوري',
                    'text' => $mItem['text'] ?? '',
                    'link' => $mItem['link'] ?? route('donors.index', ['classification' => 'recurring']),
                    'timestamp' => now()->timestamp,
                ];
            }
            if (!empty($monthlyDonationItems)) {
                $suggestions[] = [
                    'text' => 'سجّل التبرعات الشهرية المستحقة اليوم والمتأخرة',
                    'link' => route('donors.index', ['classification' => 'recurring']),
                ];
            }
        }

        // ==========================================
        // 7. Change Requests (للمدير وللموظف صاحب الطلب)
        // ==========================================
        // أ) للمدير/الأدمن: طلبات التعديل والموافقة المعلقة
        if ($canAccess('manage_change_requests')) {
            $pendingCRs = ChangeRequest::with('user')->where('status', 'pending')->latest('created_at')->limit(10)->get();
            foreach ($pendingCRs as $cr) {
                $uName = $cr->user?->name ?? 'مستخدم';
                $rawItems[] = [
                    'id' => 'cr_' . $cr->id,
                    'category' => 'change_requests',
                    'type' => 'danger',
                    'title' => 'طلب موافقة وتعديل معلق',
                    'text' => "طلب موافقة وتعديل معلق: " . ($cr->title ?? $cr->type) . " مقدّم من {$uName}",
                    'link' => route('change-requests.index'),
                    'timestamp' => $cr->created_at ? $cr->created_at->timestamp : now()->timestamp,
                ];
            }
            if ($pendingCRs->isNotEmpty()) {
                $suggestions[] = [
                    'text' => 'راجع طلبات الموافقة المعلقة',
                    'link' => route('change-requests.index'),
                ];
            }
        }

        // ب) للموظف: إشعار بحالة الطلبات الخاصة به عند الرفض أو الموافقة
        $myChangeRequests = ChangeRequest::with(['reviewer', 'subject'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('updated_at', '>=', now()->subDays(30))
            ->latest('updated_at')
            ->limit(20)
            ->get();

        foreach ($myChangeRequests as $cr) {
            $isApproved = $cr->status === 'approved';
            $modelBasename = class_basename($cr->model_type);
            $modelAr = match($modelBasename) {
                'Beneficiary' => 'حالة / مستفيد',
                'Donation' => 'تبرع',
                'Expense' => 'مصروف',
                'Delegate' => 'مندوب',
                'DelegateTrip' => 'مشوار مندوب',
                'Campaign' => 'حملة',
                'User' => 'مستخدم',
                'Account' => 'حساب مالي',
                'Task' => 'مهمة',
                'Leave' => 'إجازة',
                'JournalEntry' => 'قيد يومية',
                'Donor' => 'متبرع',
                'Project' => 'مشروع',
                'FinancialClosure' => 'إغلاق مالي',
                default => $modelBasename
            };

            $actionAr = match($cr->action) {
                'create' => 'إضافة',
                'update' => 'تعديل',
                'delete' => 'حذف',
                'cancel' => 'إلغاء',
                default => $cr->action
            };

            $pData = is_array($cr->payload) ? ($cr->payload['data'] ?? $cr->payload) : [];
            $itemName = trim((string)($cr->subject?->full_name ?? ($cr->subject?->name ?? ($pData['full_name'] ?? ($pData['name'] ?? '')))));
            $itemDesc = $itemName !== '' ? " ({$itemName})" : ($cr->model_id ? " (كود: #{$cr->model_id})" : '');

            $reviewerName = $cr->reviewer?->name ? " — بواسطة: {$cr->reviewer->name}" : '';
            $reason = (!$isApproved && !empty($cr->rejection_reason)) ? " — سبب الرفض: {$cr->rejection_reason}" : '';

            $rawItems[] = [
                'id' => 'my_cr_' . $cr->id . '_' . $cr->status . '_' . ($cr->updated_at ? $cr->updated_at->timestamp : '0'),
                'category' => ($modelBasename === 'Beneficiary' ? 'cases' : 'change_requests'),
                'alt_category' => 'change_requests',
                'type' => $isApproved ? 'success' : 'danger',
                'title' => $isApproved ? "تمت الموافقة على طلب {$actionAr} {$modelAr}" : "تم رفض طلب {$actionAr} {$modelAr}",
                'text' => ($isApproved 
                    ? "تمت الموافقة على طلبك لـ {$actionAr} {$modelAr}{$itemDesc} بنجاح{$reviewerName}"
                    : "تم رفض طلب {$actionAr} {$modelAr}{$itemDesc}{$reason}{$reviewerName}"),
                'link' => route('change-requests.index'),
                'timestamp' => $cr->updated_at ? $cr->updated_at->timestamp : ($cr->created_at ? $cr->created_at->timestamp : now()->timestamp),
            ];
        }

        // ==========================================
        // 8. Complaints
        // ==========================================
        if ($canAccess('complaints.view')) {
            $openComplaints = Complaint::where('status', 'open')->latest('created_at')->limit(6)->get();
            foreach ($openComplaints as $comp) {
                $rawItems[] = [
                    'id' => 'complaint_' . $comp->id,
                    'category' => 'complaints',
                    'type' => 'warning',
                    'title' => 'شكوى مفتوحة تتطلب المتابعة',
                    'text' => "شكوى مفتوحة: " . ($comp->subject ?? ('رقم #' . $comp->id)) . ($comp->complainant_name ? " من {$comp->complainant_name}" : ''),
                    'link' => route('complaints.index'),
                    'timestamp' => $comp->created_at ? $comp->created_at->timestamp : now()->timestamp,
                ];
            }
        }

        // ==========================================
        // 9. Website Contact Messages
        // ==========================================
        if ($canAccess('website.contact_messages.view', ['admin', 'super-admin'])) {
            $contactMsgs = WebContactMessage::where('read', false)->latest('created_at')->limit(6)->get();
            foreach ($contactMsgs as $msg) {
                $rawItems[] = [
                    'id' => 'web_msg_' . $msg->id,
                    'category' => 'website',
                    'type' => 'info',
                    'title' => 'رسالة واردة من الموقع الإلكتروني',
                    'text' => "رسالة واردة من: {$msg->name} — " . \Illuminate\Support\Str::limit($msg->subject ?? $msg->message, 50),
                    'link' => route('website.contact-messages.index'),
                    'timestamp' => $msg->created_at ? $msg->created_at->timestamp : now()->timestamp,
                ];
            }
            if ($contactMsgs->isNotEmpty()) {
                $suggestions[] = ['text' => 'مراجعة رسائل واستفسارات الموقع', 'link' => route('website.contact-messages.index')];
            }
        }

        // ==========================================
        // 10. Financial / Payrolls / Inventory system indicators
        // ==========================================
        if ($canAccess('expenses.view', ['finance'])) {
            try {
                $now = now();
                $monthDonationsTotal = (float) Donation::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
                $expenseDateCol = Schema::hasColumn('expenses', 'paid_at') ? 'paid_at' : (Schema::hasColumn('expenses', 'date') ? 'date' : 'created_at');
                $expensesMonth = (float) Expense::whereBetween($expenseDateCol, [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
                $netFlowMonth = $monthDonationsTotal - $expensesMonth;
                if ($netFlowMonth < 0) {
                    $rawItems[] = [
                        'id' => 'finance_neg_flow_' . $now->format('Y-m'),
                        'category' => 'finance',
                        'type' => 'danger',
                        'title' => 'تنبيه التدفق المالي',
                        'text' => 'صافي التدفق المالي لهذا الشهر سالب (المصروفات تجاوزت التبرعات)',
                        'link' => route('expenses.index'),
                        'timestamp' => $now->timestamp,
                    ];
                }
            } catch (\Throwable) {
            }
        }

        if ($canAccess('payrolls.view', ['hr'])) {
            try {
                $unpaidPayrolls = (int) Payroll::where('month', now()->format('Y-m'))->whereNull('paid_at')->count();
                if ($unpaidPayrolls > 0) {
                    $rawItems[] = [
                        'id' => 'payroll_unpaid_' . now()->format('Y-m'),
                        'category' => 'payrolls',
                        'type' => 'warning',
                        'title' => 'رواتب غير مسددة',
                        'text' => 'رواتب غير مُسدّدة هذا الشهر: ' . $unpaidPayrolls,
                        'link' => route('payrolls.index'),
                        'timestamp' => now()->timestamp,
                    ];
                    $suggestions[] = ['text' => 'سدّد رواتب الشهر الحالي', 'link' => route('payrolls.index')];
                }
            } catch (\Throwable) {
            }
        }

        if ($canAccess('inventory_transactions.view') || $user->hasPermission('items.view') || $user->hasPermission('warehouses.view')) {
            try {
                if (Schema::hasTable('inventory_transactions')) {
                    $lowStockCount = (int) DB::table('inventory_transactions')
                        ->select('item_id', DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as net"))
                        ->groupBy('item_id')
                        ->havingRaw('net <= 0')
                        ->count();
                    if ($lowStockCount > 0) {
                        $rawItems[] = [
                            'id' => 'inventory_low_' . now()->format('Y-m-d'),
                            'category' => 'inventory',
                            'type' => 'warning',
                            'title' => 'تنبيه المخزون',
                            'text' => 'أصناف منخفضة أو سالبة المخزون بالمستودعات: ' . $lowStockCount,
                            'link' => route('inventory-transactions.index'),
                            'timestamp' => now()->timestamp,
                        ];
                        $suggestions[] = ['text' => 'راجع الأصناف منخفضة المخزون', 'link' => route('inventory-transactions.index')];
                    }
                }
            } catch (\Throwable) {
            }
        }

        // ==========================================
        // State Synchronization & Formatting
        // ==========================================
        Carbon::setLocale('ar');
        $userStates = collect();
        if (Schema::hasTable('user_notification_states')) {
            try {
                $userStates = DB::table('user_notification_states')
                    ->where('user_id', $user->id)
                    ->get()
                    ->keyBy('notification_key');
            } catch (\Throwable $e) {
                // Table might not be migrated yet on some environments
            }
        }

        $items = [];
        foreach ($rawItems as $item) {
            $key = (string) ($item['id'] ?? md5($item['text'] . ($item['link'] ?? '')));
            $item['id'] = $key;
            $state = $userStates->get($key);

            // If user dismissed/removed this notification, omit it completely!
            if ($state && $state->is_dismissed) {
                continue;
            }

            $item['is_read'] = $state ? (bool) $state->is_read : false;
            $item['read_at'] = $state?->read_at;

            $ts = is_numeric($item['timestamp'] ?? null) ? (int) $item['timestamp'] : now()->timestamp;
            $item['timestamp'] = $ts;

            $dt = Carbon::createFromTimestamp($ts)->locale('ar');
            $item['time_ago'] = $dt->diffForHumans();
            $item['time_formatted'] = $dt->format('Y-m-d h:i A');

            $items[] = $item;
        }

        // 1. Sort strictly by timestamp DESC (Newest first)
        usort($items, function ($a, $b) {
            return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
        });

        // 2. Filters
        if ($category) {
            $items = array_values(array_filter($items, fn($i) => $i['category'] === $category || ($i['alt_category'] ?? null) === $category));
        }
        if ($type) {
            $items = array_values(array_filter($items, fn($i) => $i['type'] === $type));
        }
        if ($readStatus === 'unread') {
            $items = array_values(array_filter($items, fn($i) => empty($i['is_read'])));
        } elseif ($readStatus === 'read') {
            $items = array_values(array_filter($items, fn($i) => !empty($i['is_read'])));
        }

        $unreadCount = count(array_filter($items, fn($i) => empty($i['is_read'])));

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'items' => $items,
                'suggestions' => $suggestions,
                'unread_count' => $unreadCount,
            ]);
        }

        $canCreateNotif = $isAdmin || $isManager || $user->hasPermission('notifications.create') || $user->hasPermission('mobile.notifications.view');
        $employees = $canCreateNotif
            ? User::where(function ($q) {
                $q->where('active', true)->orWhereNull('active');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'job_title', 'department', 'role'])
            : collect();

        return view('notifications.index', [
            'items' => $items,
            'category' => $category,
            'type' => $type,
            'readStatus' => $readStatus,
            'unreadCount' => $unreadCount,
            'suggestions' => $suggestions,
            'canCreateNotif' => $canCreateNotif,
            'employees' => $employees,
        ]);
    }

    public function storeTargeted(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $isAdmin = $user->hasRole('admin') || $user->hasRole('super_admin');
        $isManager = $user->hasRole('manager');
        if (!$isAdmin && !$isManager && !$user->hasPermission('notifications.create') && !$user->hasPermission('mobile.notifications.view')) {
            abort(403, 'غير مصرح لك بإرسال إشعارات خاصة للموظفين.');
        }

        $data = $request->validate([
            'title' => 'required|string|max:191',
            'body' => 'required|string|max:1000',
            'type' => 'nullable|string|in:info,warning,danger,success',
            'target_type' => 'required|string|in:user,all,department,role',
            'target_user_id' => 'nullable|required_if:target_type,user|exists:users,id',
            'target_department' => 'nullable|string|max:191',
            'target_role' => 'nullable|string|max:191',
        ]);

        $importance = $data['type'] ?? 'info';
        $category = match ($importance) {
            'danger' => 'عاجل',
            'warning' => 'مهام',
            'success' => 'حملات',
            default => 'عام',
        };

        $targetUser = null;
        if ($data['target_type'] === 'user' && !empty($data['target_user_id'])) {
            $targetUser = User::find($data['target_user_id']);
        }

        $notifData = [
            'title' => $data['title'],
            'body' => $data['body'],
            'category' => $category,
            'target_type' => $data['target_type'],
            'target_user_id' => $data['target_type'] === 'user' ? $data['target_user_id'] : null,
            'target_department' => $data['target_type'] === 'department' ? $data['target_department'] : null,
            'target_role' => $data['target_type'] === 'role' ? $data['target_role'] : null,
            'target_audience' => $data['target_type'] === 'user' ? ($targetUser ? $targetUser->name : 'user') : 'all',
            'is_sent' => true,
            'sent_at' => now(),
        ];

        MobileNotification::create($notifData);

        $msg = $targetUser 
            ? "تم إرسال الإشعار بنجاح إلى الموظف ({$targetUser->name})." 
            : "تم إرسال وبث التنبيه بنجاح.";

        return back()->with('success', $msg);
    }

    public function markRead(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $key = (string) $request->input('id');
        if ($key === '') {
            return response()->json(['error' => 'Missing notification id'], 422);
        }

        if (Schema::hasTable('user_notification_states')) {
            try {
                DB::table('user_notification_states')->updateOrInsert(
                    ['user_id' => $user->id, 'notification_key' => $key],
                    ['is_read' => true, 'read_at' => now(), 'updated_at' => now()]
                );
            } catch (\Throwable $e) {}
        }

        return response()->json(['success' => true, 'id' => $key]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $keys = $request->input('keys', []);
        if (Schema::hasTable('user_notification_states')) {
            try {
                if (is_array($keys) && !empty($keys)) {
                    foreach ($keys as $k) {
                        if (is_string($k) && $k !== '') {
                            DB::table('user_notification_states')->updateOrInsert(
                                ['user_id' => $user->id, 'notification_key' => $k],
                                ['is_read' => true, 'read_at' => now(), 'updated_at' => now()]
                            );
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        return response()->json(['success' => true]);
    }

    public function dismiss(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $key = (string) $request->input('id');
        if ($key === '') {
            return response()->json(['error' => 'Missing notification id'], 422);
        }

        if (Schema::hasTable('user_notification_states')) {
            try {
                DB::table('user_notification_states')->updateOrInsert(
                    ['user_id' => $user->id, 'notification_key' => $key],
                    ['is_dismissed' => true, 'dismissed_at' => now(), 'is_read' => true, 'read_at' => now(), 'updated_at' => now()]
                );
            } catch (\Throwable $e) {}
        }

        return response()->json(['success' => true, 'id' => $key]);
    }

    public function clearRead(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (Schema::hasTable('user_notification_states')) {
            try {
                DB::table('user_notification_states')
                    ->where('user_id', $user->id)
                    ->where('is_read', true)
                    ->update([
                        'is_dismissed' => true,
                        'dismissed_at' => now(),
                        'updated_at' => now(),
                    ]);
            } catch (\Throwable $e) {}
        }

        return response()->json(['success' => true]);
    }
}
