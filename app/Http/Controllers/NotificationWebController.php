<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
use App\Services\MonthlyDonationReminderService;

final class NotificationWebController extends Controller
{
    public function __construct(
        private readonly MonthlyDonationReminderService $monthlyDonationReminderService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->hasPermission('notifications.view')) { abort(403); }

        $category = $request->input('category');
        $type = $request->input('type');

        $items = [];
        $suggestions = [];
        $isAdmin = $user->hasRole('admin');

        // Helper check closure
        $canAccess = function (string $permission, array $roles = []) use ($user, $isAdmin): bool {
            if ($isAdmin) return true;
            if ($permission !== '' && $user->hasPermission($permission)) return true;
            foreach ($roles as $role) {
                if ($user->hasRole($role)) return true;
            }
            return false;
        };

        // ==========================================
        // 1. الحالات وطلبات المساعدة (المقبولة والمرفوضة والجديدة)
        // ==========================================
        if ($canAccess('beneficiaries.view', ['social_researcher', 'researcher', 'field_researcher']) || $isAdmin || $user->hasPermission('cases.view')) {
            // أ) الحالات المقبولة والمعتمدة حديثاً (خلال آخر 14 يوم)
            $recentAcceptedCases = MobileCaseApplication::whereIn('status', ['approved', 'completed'])
                ->where('updated_at', '>=', now()->subDays(14))
                ->count();
            if ($recentAcceptedCases > 0) {
                $items[] = [
                    'category' => 'cases',
                    'type' => 'success',
                    'text' => 'حالات مقبولة ومعتمدة حديثاً: ' . $recentAcceptedCases . ' حالة (تم قبول البحث المبدئي وجاهزة لربط المساعدات والزيارات)',
                    'link' => route('case-applications.index', ['status' => 'completed']),
                ];
                $suggestions[] = [
                    'text' => 'عرض ومتابعة الحالات المقبولة والمعتمدة',
                    'link' => route('case-applications.index', ['status' => 'completed']),
                ];
            }

            // ب) الحالات المرفوضة (مع سبب الرفض)
            $recentRejectedCases = MobileCaseApplication::where('status', 'rejected')
                ->where('updated_at', '>=', now()->subDays(14))
                ->count();
            if ($recentRejectedCases > 0) {
                $items[] = [
                    'category' => 'cases',
                    'type' => 'danger',
                    'text' => 'حالات مرفوضة بالبحث المبدئي: ' . $recentRejectedCases . ' حالة (تتطلب مراجعة أسباب عدم القبول أو الأرشفة)',
                    'link' => route('case-applications.index', ['status' => 'rejected']),
                ];
                $suggestions[] = [
                    'text' => 'مراجعة أسباب الحالات المرفوضة',
                    'link' => route('case-applications.index', ['status' => 'rejected']),
                ];
            }

            // ج) طلبات حالات جديدة معلقة تنتظر الفحص وتعيين الباحث
            $pendingCases = MobileCaseApplication::where('status', 'pending')->count();
            if ($pendingCases > 0) {
                $items[] = [
                    'category' => 'cases',
                    'type' => 'warning',
                    'text' => 'طلبات حالات جديدة تنتظر الفحص المبدئي: ' . $pendingCases,
                    'link' => route('case-applications.index', ['status' => 'pending']),
                ];
                $suggestions[] = [
                    'text' => 'فحص طلبات الحالات الجديدة وتعيين الباحثين',
                    'link' => route('case-applications.index', ['status' => 'pending']),
                ];
            }

            // د) حالات جارية قيد البحث الميداني
            $underResearchCases = MobileCaseApplication::where('status', 'under_research')->count();
            if ($underResearchCases > 0) {
                $items[] = [
                    'category' => 'cases',
                    'type' => 'info',
                    'text' => 'حالات جارية قيد البحث الميداني والدراسة: ' . $underResearchCases,
                    'link' => route('case-applications.index', ['status' => 'under_research']),
                ];
            }

            // هـ) حالات مسندة للباحث/الموظف الحالي شخصياً
            $myAssignedCases = MobileCaseApplication::where('assigned_researcher_id', $user->id)
                ->whereIn('status', ['pending', 'under_research'])
                ->count();
            if ($myAssignedCases > 0) {
                $items[] = [
                    'category' => 'cases',
                    'type' => 'warning',
                    'text' => 'لديك ' . $myAssignedCases . ' حالة مسندة إليك شخصياً لإجراء البحث والتنسيق',
                    'link' => route('case-applications.index'),
                ];
            }
        }

        // ==========================================
        // 2. الزيارات الميدانية للموظفين والباحثين
        // ==========================================
        if ($canAccess('field_visits.view', ['social_researcher', 'researcher', 'field_researcher']) || $user->hasPermission('visits.view') || $isAdmin) {
            // زيارات الموظف المجدولة لليوم أو المتأخرة
            $myDueVisits = FieldVisit::where('researcher_id', $user->id)
                ->where('status', 'scheduled')
                ->whereDate('visit_date', '<=', now()->toDateString())
                ->count();
            if ($myDueVisits > 0) {
                $items[] = [
                    'category' => 'field_visits',
                    'type' => 'danger',
                    'text' => 'لديك ' . $myDueVisits . ' زيارة ميدانية مجدولة لليوم أو متأخرة تتطلب استيفاء التقرير والبحث',
                    'link' => route('visits.index'),
                ];
                $suggestions[] = [
                    'text' => 'استيفاء تقارير الزيارات الميدانية المجدولة',
                    'link' => route('visits.index'),
                ];
            }

            // إجمالي الزيارات الميدانية المتأخرة (للإدارة)
            $overdueAllVisits = FieldVisit::where('status', 'scheduled')
                ->whereDate('visit_date', '<', now()->toDateString())
                ->count();
            if ($overdueAllVisits > 0 && ($isAdmin || $user->hasRole('manager'))) {
                $items[] = [
                    'category' => 'field_visits',
                    'type' => 'warning',
                    'text' => 'زيارات ميدانية متأخرة تجاوزت موعدها المحدد: ' . $overdueAllVisits,
                    'link' => route('visits.index'),
                ];
            }
        }

        // ==========================================
        // 3. المهام الخاصة بالموظف وفريق العمل
        // ==========================================
        // أ) مهام مسندة للموظف الحالي مباشرة
        $myOpenTasks = Task::where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $user->id));
            })
            ->where('status', '!=', 'done')
            ->count();
        if ($myOpenTasks > 0) {
            $items[] = [
                'category' => 'tasks',
                'type' => 'info',
                'text' => 'لديك ' . $myOpenTasks . ' مهمة عمل خاصة بك قيد التنفيذ',
                'link' => route('tasks.index'),
            ];
        }

        // ب) مهام متأخرة للموظف الحالي (تجاوزت تاريخ الاستحقاق)
        $myOverdueTasks = Task::where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $user->id));
            })
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();
        if ($myOverdueTasks > 0) {
            $items[] = [
                'category' => 'tasks',
                'type' => 'danger',
                'text' => 'تنبيه عاجل: لديك ' . $myOverdueTasks . ' مهمة متأخرة تجاوزت موعد الاستحقاق',
                'link' => route('tasks.index'),
            ];
            $suggestions[] = [
                'text' => 'إنجاز المهام المتأخرة المستعجلة',
                'link' => route('tasks.index'),
            ];
        }

        // ج) إجمالي المهام المفتوحة بالمنظومة (للإدارة)
        if ($canAccess('tasks.view')) {
            $openTasks = (int) Task::where('status', '!=', 'done')->count();
            if ($openTasks > 0 && $isAdmin) {
                $items[] = [
                    'category' => 'tasks',
                    'type' => 'secondary',
                    'text' => 'إجمالي مهام فريق العمل المفتوحة بالمنظومة: ' . $openTasks,
                    'link' => route('tasks.index'),
                ];
            }
        }

        // ==========================================
        // 4. الموارد البشرية والإجازات وشؤون الموظفين
        // ==========================================
        // أ) طلبات إجازات معلقة تنتظر موافقة الموارد البشرية/الإدارة
        if ($canAccess('leaves.view', ['hr']) || $isAdmin) {
            $pendingLeaves = Leave::where('status', 'pending')->count();
            if ($pendingLeaves > 0) {
                $items[] = [
                    'category' => 'hr',
                    'type' => 'warning',
                    'text' => 'طلبات إجازات معلقة للموظفين تنتظر الاعتماد: ' . $pendingLeaves,
                    'link' => route('leaves.index'),
                ];
                $suggestions[] = [
                    'text' => 'اعتماد ومراجعة طلبات إجازات الموظفين',
                    'link' => route('leaves.index'),
                ];
            }
        }

        // ب) إشعار الموظف الحالي بالموافقة على إجازته
        $myApprovedLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        if ($myApprovedLeaves > 0) {
            $items[] = [
                'category' => 'hr',
                'type' => 'success',
                'text' => 'تمت الموافقة على طلب إجازتك الأخير بنجاح من قبل إدارة الموارد البشرية',
                'link' => route('leaves.index'),
            ];
        }

        // ج) إشعار الموظف الحالي برفض طلب إجازته
        $myRejectedLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        if ($myRejectedLeaves > 0) {
            $items[] = [
                'category' => 'hr',
                'type' => 'danger',
                'text' => 'تم رفض طلب إجازتك الأخير من قبل إدارة الموارد البشرية',
                'link' => route('leaves.index'),
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
                $items[] = [
                    'category' => 'attendance',
                    'type' => 'warning',
                    'text' => 'تذكير: لم تقم بتسجيل حضورك في منظومة العمل اليوم بعد',
                    'link' => route('employee-attendance.index'),
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
            array_push($items, ...$monthlyDonationItems);
            if ($monthlyDonationItems !== []) {
                $suggestions[] = [
                    'text' => 'سجّل التبرعات الشهرية المستحقة اليوم والمتأخرة',
                    'link' => route('donors.index', ['classification' => 'recurring']),
                ];
            }
        }

        // ==========================================
        // 7. Pending Change Requests (CRITICAL)
        // ==========================================
        if ($canAccess('manage_change_requests')) {
            $pendingCRs = (int) ChangeRequest::where('status', 'pending')->count();
            if ($pendingCRs > 0) {
                $items[] = [
                    'category' => 'change_requests',
                    'type' => 'danger',
                    'text' => 'طلبات موافقة وتعديل معلقة: ' . $pendingCRs,
                    'link' => route('change-requests.index'),
                ];
                $suggestions[] = [
                    'text' => 'راجع طلبات الموافقة المعلقة',
                    'link' => route('change-requests.index'),
                ];
            }
        }

        // ==========================================
        // 8. Open Complaints
        // ==========================================
        if ($canAccess('complaints.view')) {
            $openComplaints = (int) Complaint::where('status', 'open')->count();
            if ($openComplaints > 0) {
                $items[] = [
                    'category' => 'complaints',
                    'type' => 'warning',
                    'text' => 'شكاوى مفتوحة تتطلب المتابعة: ' . $openComplaints,
                    'link' => route('complaints.index'),
                ];
            }
        }

        // ==========================================
        // 9. Volunteer Attendance
        // ==========================================
        if ($canAccess('volunteers.view', ['hr']) || $user->hasPermission('volunteer_attendance.view')) {
            $attendanceToday = (int) VolunteerAttendance::whereDate('date', now()->toDateString())->count();
            if ($attendanceToday == 0) { $items[] = ['category'=>'attendance','type'=>'secondary','text'=>'لا يوجد حضور متطوعين مسجل اليوم','link'=>route('volunteers.index')]; }
            if ($attendanceToday > 0) { $items[] = ['category'=>'attendance','type'=>'success','text'=>'تم تسجيل حضور المتطوعين اليوم (' . $attendanceToday . ' متطوع)','link'=>route('volunteers.index')]; }
        }

        // 6. Financial Net Flow
        if ($canAccess('expenses.view', ['finance']) || $user->hasPermission('accounts.view')) {
            $cashMonth = (float) Donation::where('type','cash')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount');
            $inKindMonth = (float) Donation::where('type','in_kind')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('estimated_value');
            $monthDonationsTotal = $cashMonth + $inKindMonth;
            $expensesMonth = (float) Expense::whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount');
            $netFlowMonth = $monthDonationsTotal - $expensesMonth;
            if ($netFlowMonth < 0) { $items[] = ['category'=>'finance','type'=>'danger','text'=>'صافي التدفق هذا الشهر سالب','link'=>route('expenses.index')]; }
            if ($netFlowMonth >= 0 && $monthDonationsTotal > 0) { $items[] = ['category'=>'finance','type'=>'success','text'=>'صافي التدفق لهذا الشهر موجب','link'=>route('reports.index')]; }
        }

        // 7. Unlocked Journal Entries
        if ($canAccess('financial_closures.view', ['finance']) || $user->hasPermission('journal_entries.view')) {
            $unlockedPrev = (int) JournalEntry::where('locked', false)->whereDate('date', '<', now()->startOfMonth())->count();
            if ($unlockedPrev > 0) { $items[] = ['category'=>'finance','type'=>'warning','text'=>'هناك قيود قديمة غير مُقفلة','link'=>route('closures.index')]; $suggestions[] = ['text' => 'أغلق الشهر المالي السابق', 'link' => route('closures.create')]; }
        }

        // 8. Unpaid Payrolls
        if ($canAccess('payrolls.view', ['hr'])) {
            $unpaidPayrolls = (int) Payroll::where('month', now()->format('Y-m'))->whereNull('paid_at')->count();
            if ($unpaidPayrolls > 0) { $items[] = ['category'=>'payrolls','type'=>'warning','text'=>'رواتب غير مُسدّدة هذا الشهر: '.$unpaidPayrolls,'link'=>route('payrolls.index')]; $suggestions[] = ['text' => 'سدّد رواتب الشهر الحالي', 'link' => route('payrolls.index')]; }
        }

        // 9. Failed Audits
        if ($canAccess('audits.view')) {
            $failedAudits = 0;
            if (Schema::hasColumn('audits','status_code')) { $failedAudits = (int) Audit::where('status_code','>=',400)->whereBetween('created_at',[now()->subDays(7), now()])->count(); }
            if ($failedAudits > 0) { $items[] = ['category'=>'audits','type'=>'danger','text'=>'طلبات فاشلة في السجلات: '.$failedAudits,'link'=>route('audits.index')]; $suggestions[] = ['text' => 'تحقّق من السجلات ذات الأخطاء', 'link' => route('audits.index')]; }
        }

        // 10. Delegates Without Routes
        if ($canAccess('delegates.view') || $user->hasPermission('manage_logistics')) {
            $delegatesNoRoute = (int) Delegate::whereNull('route_id')->count();
            if ($delegatesNoRoute > 0) { $items[] = ['category'=>'delegates','type'=>'info','text'=>'مندوبون بلا خط سير: '.$delegatesNoRoute,'link'=>route('delegates.index')]; $suggestions[] = ['text' => 'عيّن خط السير للمندوبين', 'link' => route('delegates.index')]; }
        }

        // 11. Unassigned Donations
        if ($canAccess('donations.view')) {
            $donationsUnassigned = (int) Donation::where(function($q){ $q->whereNull('delegate_id')->orWhereNull('route_id'); })->count();
            if ($donationsUnassigned > 0) { $items[] = ['category'=>'donations','type'=>'secondary','text'=>'تبرعات غير مُعيّنة لمندوب/خط سير: '.$donationsUnassigned,'link'=>route('donations.index')]; $suggestions[] = ['text' => 'أكمل تعيين المندوب وخط السير للتبرعات', 'link' => route('donations.index')]; }
        }

        // 12. Guest Houses Missing Capacity
        if ($canAccess('guest_houses.view') || $user->hasPermission('manage_guest_house')) {
            $ghMissingCapacity = (int) GuestHouse::whereNull('capacity')->count();
            if ($ghMissingCapacity > 0) { $items[] = ['category'=>'guest_houses','type'=>'info','text'=>'دور ضيافة بلا سعة محددة: '.$ghMissingCapacity,'link'=>route('guest-houses.index')]; $suggestions[] = ['text' => 'أكمل بيانات سعة دور الضيافة', 'link' => route('guest-houses.index')]; }
        }

        // 13. Users Without Roles
        if ($canAccess('users.view') || $user->hasPermission('roles.view')) {
            $usersNoRoles = (int) User::doesntHave('roles')->count();
            if ($usersNoRoles > 0) { $items[] = ['category'=>'users','type'=>'warning','text'=>'مستخدمون بلا أدوار: '.$usersNoRoles,'link'=>route('users.index')]; $suggestions[] = ['text' => 'عيّن الأدوار للمستخدمين', 'link' => route('users.index')]; }
        }

        // 14. Low Inventory Stock
        if ($canAccess('inventory_transactions.view') || $user->hasPermission('items.view') || $user->hasPermission('warehouses.view')) {
            $lowStockCount = (int) DB::table('inventory_transactions')
                ->select('item_id', DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as net"))
                ->groupBy('item_id')
                ->havingRaw('net <= 0')
                ->count();
            if ($lowStockCount > 0) { $items[] = ['category'=>'inventory','type'=>'warning','text'=>'أصناف منخفضة أو سالبة المخزون: '.$lowStockCount,'link'=>route('inventory-transactions.index')]; $suggestions[] = ['text' => 'راجع الأصناف منخفضة المخزون', 'link' => route('inventory-transactions.index')]; }
        }

        // 15. New Beneficiaries
        if ($canAccess('beneficiaries.view')) {
            $newBeneficiaries = (int) Beneficiary::where('status','new')->whereBetween('created_at',[now()->startOfMonth(), now()])->count();
            if ($newBeneficiaries > 0) { $items[] = ['category'=>'beneficiaries','type'=>'info','text'=>'مستفيدون جدد هذا الشهر: '.$newBeneficiaries,'link'=>route('beneficiaries.index')]; }
        }

        // 16. Unread Website Contact Messages
        if ($canAccess('website.contact_messages.view', ['admin', 'super-admin'])) {
            $unreadMessages = (int) WebContactMessage::where('read', false)->count();
            if ($unreadMessages > 0) {
                $items[] = [
                    'category' => 'website',
                    'type' => 'info',
                    'text' => 'رسائل واردة جديدة من الموقع الإلكتروني: ' . $unreadMessages,
                    'link' => route('website.contact-messages.index')
                ];
                $suggestions[] = ['text' => 'مراجعة رسائل واستفسارات الموقع', 'link' => route('website.contact-messages.index')];
            }
        }

        if ($category) { $items = array_values(array_filter($items, fn($i)=>$i['category']===$category)); }
        if ($type) { $items = array_values(array_filter($items, fn($i)=>$i['type']===$type)); }

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(['items' => $items, 'suggestions' => $suggestions]);
        }

        return view('notifications.index', [
            'items' => $items,
            'category' => $category,
            'type' => $type,
            'suggestions' => $suggestions,
        ]);
    }
}
