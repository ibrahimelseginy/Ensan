<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\InventoryTransaction;
use App\Models\Beneficiary;
use App\Models\VolunteerAttendance;
use App\Models\VolunteerHour;
use App\Models\Task;
use App\Models\Payroll;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Expense;
use App\Models\Audit;
use App\Models\EmployeeAttendance;

class DashboardWebController extends Controller
{
    public function index()
    {
        $donorsCount = Donor::count();
        $beneficiariesCount = Beneficiary::count();
        $warehousesCount = Warehouse::count();
        $volunteersCount = User::where('is_volunteer', true)->count();
        $employeesCount = User::where('is_employee', true)->count();
        $cashDonations = Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->sum('amount');
        $inKindDonations = Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->sum('estimated_value');
        $inventoryNet = InventoryTransaction::select(DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as net"))->value('net');
        $openTasksQuery = Task::where('status', '!=', 'done');
        $latestTasksQuery = Task::orderByDesc('id')->limit(5);

        if ($user = auth()->user()) {
            $canViewAll = $user->hasPermission('employee_tasks.view') || $user->hasPermission('tasks.view');
            if (!$canViewAll) {
                $openTasksQuery->where('assigned_to', $user->id);
                $latestTasksQuery->where('assigned_to', $user->id);
            }
        }

        $openTasks = $openTasksQuery->count();
        $latestTasks = $latestTasksQuery->get();

        $today = now()->toDateString();
        $attendanceToday = VolunteerAttendance::where('date', $today)->count();
        $currentlyOnLeave = \App\Models\Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->with('user')
            ->get();

        $latestDonations = Donation::orderByDesc('id')->limit(5)->get();
        $latestAttendance = VolunteerAttendance::orderByDesc('date')->limit(5)->get();

        $openComplaints = Complaint::where('status', '!=', 'resolved')->count();
        $vhoursMonth = (float) VolunteerHour::whereBetween('date', [now()->startOfMonth(), now()])->sum('hours');
        $payrollMonth = (float) Payroll::where('month', now()->format('Y-m'))->sum('amount');

        $months = [];
        $cashSeries = [];
        $inKindSeries = [];
        $expenseSeries = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $label = $start->format('Y-m');
            $months[] = $label;
            $cashSeries[] = (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->whereBetween('created_at', [$start, $end])->sum('amount');
            $inKindSeries[] = (float) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->whereBetween('created_at', [$start, $end])->sum('estimated_value');

            // Calculate expenses for this month (General + Project Activities)
            $generalExp = Expense::where('status', '!=', 'cancelled')->whereBetween('created_at', [$start, $end])->sum('amount');
            $activityExp = \App\Models\ProjectActivity::whereBetween('activity_date', [$start, $end])->sum('expenses');
            $expenseSeries[] = $generalExp + $activityExp;
        }

        $beneficiaryStatus = Beneficiary::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get()->map(function ($r) {
                $labelMap = ['new' => 'جديد', 'under_review' => 'قيد المراجعة', 'accepted' => 'مقبول'];
                return ['status' => $labelMap[$r->status] ?? $r->status, 'count' => (int) $r->count];
            });

        $evaluations = [];
        $volunteers = User::where('is_volunteer', true)->orderBy('name')->limit(12)->get();
        foreach ($volunteers as $v) {
            $hours = (float) VolunteerHour::where('user_id', $v->id)->whereBetween('date', [$monthStart, $today])->sum('hours');
            $attendanceDays = (int) VolunteerAttendance::where('user_id', $v->id)->whereBetween('date', [$monthStart, $today])->distinct()->count('date');
            $tasksDone = (int) Task::where('assigned_to', $v->id)->where('status', 'done')->whereBetween('created_at', [now()->startOfMonth(), now()])->count();
            $score = min($hours / 40, 1) * 40 + min($attendanceDays / 20, 1) * 30 + min($tasksDone / 10, 1) * 30;
            $evaluations[] = [
                'name' => $v->name,
                'hours' => $hours,
                'attendance' => $attendanceDays,
                'tasks' => $tasksDone,
                'score' => round($score)
            ];
        }

        // Get "مشروع كسوة" ID
        $kiswaProject = \App\Models\Project::where('name', 'مشروع كسوة')->first();

        // Calculate revenues for this month
        $kiswaActivitiesRevenue = 0;
        if ($kiswaProject) {
            $kiswaActivitiesRevenue = \App\Models\ProjectActivity::where('project_id', $kiswaProject->id)
                ->whereBetween('activity_date', [now()->startOfMonth(), now()])
                ->sum('revenue');
        }

        // Workspace rentals revenue for this month (confirmed + completed only)
        $workspaceRentalsRevenue = \App\Models\WorkspaceRental::whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('start_time', [now()->startOfMonth(), now()])
            ->sum('total_price');

        // Total activities revenue = Kiswa project activities + Workspace rentals
        $projectActivitiesRevenue = $kiswaActivitiesRevenue + $workspaceRentalsRevenue;

        $projectActivitiesExpenses = \App\Models\ProjectActivity::whereBetween('activity_date', [now()->startOfMonth(), now()])->sum('expenses');

        $cashMonth = (float) Donation::where('type', 'cash')->where('status', '!=', 'cancelled')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount');
        $inKindMonth = (float) Donation::where('type', 'in_kind')->where('status', '!=', 'cancelled')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('estimated_value');
        $monthDonationsTotal = $cashMonth + $inKindMonth + $projectActivitiesRevenue;
        $cashMonthPct = $monthDonationsTotal > 0 ? round(($cashMonth / $monthDonationsTotal) * 100) : 0;
        $inKindMonthPct = $monthDonationsTotal > 0 ? 100 - $cashMonthPct : 0;

        $expensesMonth = (float) Expense::where('status', '!=', 'cancelled')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount') + $projectActivitiesExpenses;
        $netFlowMonth = $monthDonationsTotal - $expensesMonth;

        $latestExpenses = Expense::with('creator')->orderByDesc('id')->limit(5)->get();
        $expenseByCat = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [now()->startOfMonth(), now()])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Active Campaigns Financials
        $activeCampaigns = \App\Models\Campaign::where('status', 'active')
            ->withCount('donations')
            ->limit(5)
            ->get();

        foreach ($activeCampaigns as $ac) {
            $ac->total_donations = $ac->donations()->where('type', 'cash')->where('status', '!=', 'cancelled')->sum('amount') +
                $ac->donations()->where('type', 'in_kind')->where('status', '!=', 'cancelled')->sum('estimated_value');
            $ac->total_expenses = \App\Models\Expense::where('campaign_id', $ac->id)->where('status', '!=', 'cancelled')->sum('amount');
            $ac->net_balance = $ac->total_donations - $ac->total_expenses;
        }

        // Active Projects Financials
        $activeProjects = \App\Models\Project::where('status', 'active')
            ->limit(5)
            ->get();

        foreach ($activeProjects as $ap) {
            $cashSum = $ap->donations()->where('type', 'cash')->where('status', '!=', 'cancelled')->sum('amount');
            $inKindSum = $ap->donations()->where('type', 'in_kind')->where('status', '!=', 'cancelled')->sum('estimated_value');

            $activitiesRevenue = $ap->activities()->sum('revenue');
            $activitiesExpenses = $ap->activities()->sum('expenses');

            $ap->total_donations = $cashSum + $inKindSum + $activitiesRevenue;
            $ap->total_expenses = \App\Models\Expense::where('project_id', $ap->id)->where('status', '!=', 'cancelled')->sum('amount') + $activitiesExpenses;
            $ap->net_balance = $ap->total_donations - $ap->total_expenses;
        }

        $inventoryLevels = InventoryTransaction::select(
            'item_id',
            DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as current_stock")
        )
            ->groupBy('item_id')
            ->having('current_stock', '>', 0)
            ->with('item')
            ->orderByDesc('current_stock')
            ->limit(5)
            ->get();

        $donorTotals = Donation::select('donor_id', DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->startOfMonth(), now()])
            ->groupBy('donor_id')->get();
        $donorIds = $donorTotals->pluck('donor_id')->filter()->unique();
        $donorsMap = Donor::whereIn('id', $donorIds)->get()->keyBy('id');
        $topDonors = $donorTotals->map(function ($r) use ($donorsMap) {
            return ['id' => $r->donor_id, 'name' => optional($donorsMap->get($r->donor_id))->name ?? '—', 'total' => (float) $r->total];
        })->sortByDesc('total')->values()->take(5)->all();


        $user = request()->user();
        $isAdmin = $user && ($user->roles()->where('key', 'admin')->exists() || $user->hasPermission('roles.delete'));
        $isFinance = $user && ($user->roles()->where('key', 'finance')->exists() || $user->hasPermission('journal_entries.view') || $user->hasPermission('expenses.view'));
        $financeStats = [];
        if ($isFinance) {
            $lastPayroll = Payroll::where('user_id', $user->id)->orderByDesc('month')->first();
            $takenDays = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('type', '!=', 'unpaid')
                ->whereYear('start_date', now()->year)
                ->get()->sum('days');

            $permissionsCount = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('type', 'permission')
                ->whereYear('start_date', now()->year)
                ->count();

            $financeStats = [
                'attendance' => EmployeeAttendance::where('user_id', $user->id)->whereMonth('date', now()->month)->count(),
                'salary' => $lastPayroll ? $lastPayroll->amount : ($user->salary ?? 0),
                'salary_month' => $lastPayroll ? $lastPayroll->month : now()->format('Y-m'),
                'tasks_pending' => Task::where('assigned_to', $user->id)->where('status', '!=', 'done')->count(),
                'annual_balance' => $user->annual_leave_quota ?? 21,
                'vacations_taken' => $takenDays,
                'remaining_balance' => $user->leave_balance ?? (21 - $takenDays),
                'vacations_balance' => $user->leave_balance ?? (21 - $takenDays),
                'absence_days' => 0, // Placeholder
                'permissions_count' => $permissionsCount
            ];
        }
        $isHr = $user && ($user->roles()->where('key', 'hr')->exists() || $user->hasPermission('users.create') || $user->hasPermission('payroll.create'));
        $hrStats = [];
        $hrDashboardData = [];
        $pendingLeaves = 0;
        $currentlyOnLeave = collect();
        if ($isHr) {
            $lastPayroll = Payroll::where('user_id', $user->id)->orderByDesc('month')->first();
            $takenDays = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('type', '!=', 'unpaid')
                ->whereYear('start_date', now()->year)
                ->get()->sum('days');

            $permissionsCount = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('type', 'permission')
                ->whereYear('start_date', now()->year)
                ->count();

            $hrStats = [
                'attendance' => EmployeeAttendance::where('user_id', $user->id)->whereMonth('date', now()->month)->count(),
                'salary' => $lastPayroll ? $lastPayroll->amount : ($user->salary ?? 0),
                'salary_month' => $lastPayroll ? $lastPayroll->month : now()->format('Y-m'),
                'tasks_pending' => Task::where('assigned_to', $user->id)->where('status', '!=', 'done')->count(),
                'annual_balance' => $user->annual_leave_quota ?? 21,
                'vacations_taken' => $takenDays,
                'remaining_balance' => $user->leave_balance ?? (21 - $takenDays),
                'vacations_balance' => $user->leave_balance ?? (21 - $takenDays),
                'absence_days' => 0,
                'permissions_count' => $permissionsCount
            ];

            // Advanced HR Dashboard Data
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');
            $currentMonthString = $now->format('Y-m');

            $totalEmployees = User::where('is_employee', true)->count();
            $totalVolunteers = User::where('is_volunteer', true)->count();
            
            $tasksTotal = Task::count();
            $tasksCompleted = Task::where('status', 'done')->count();
            $tasksPending = Task::where('status', 'pending')->count();
            
            $avgRating = Task::whereNotNull('rating')->avg('rating') ?? 0;
            $ratedTasksCount = Task::whereNotNull('rating')->count();

            // Top Performers
            $topPerformers = User::whereHas('assignedTasks', function($q) {
                    $q->where('status', 'done')->where('rating', '>=', 4.5);
                })
                ->withCount(['assignedTasks as avg_rating' => function($q) {
                    $q->where('status', 'done')->select(DB::raw('avg(rating)'));
                }])
                ->orderByDesc('avg_rating')
                ->take(5)
                ->get();

            // Attendance
            $employeeAttendanceCount = EmployeeAttendance::whereMonth('date', $currentMonth)
                                                         ->whereYear('date', $currentYear)
                                                         ->count();
            
            $volunteerAttendanceCount = VolunteerAttendance::whereMonth('date', $currentMonth)
                                                           ->whereYear('date', $currentYear)
                                                           ->count();

            $workingDaysPassed = max(1, $now->day); 
            $expectedAttendance = $totalEmployees * $workingDaysPassed; 
            $attendanceRate = $expectedAttendance > 0 ? min(100, ($employeeAttendanceCount / $expectedAttendance) * 100) : 0; 

            // Salaries
            $totalSalaries = Payroll::where('month', $currentMonthString)->sum('amount');
            $employeesPaidCount = Payroll::where('month', $currentMonthString)->distinct('user_id')->count('user_id');

            // 6 Month Trend
            $salaryTrendLabels = [];
            $salaryTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $mStr = $date->format('Y-m');
                $salaryTrendLabels[] = $date->translatedFormat('F');
                $salaryTrendData[] = Payroll::where('month', $mStr)->sum('amount');
            }

            // Leaves
            $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count(); 
            
            // AI Insights
            $insights = [];
            if ($avgRating > 4.5) {
                $insights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'أداء الفريق ممتاز هذا الشهر بمتوسط تقييم ' . number_format($avgRating, 1)];
            } elseif ($avgRating < 3 && $ratedTasksCount > 5) {
                $insights[] = ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'انخفاض في مستوى الأداء العام، يرجى مراجعة التقييمات.'];
            }

            if ($attendanceRate < 70 && $totalEmployees > 5) {
                $insights[] = ['type' => 'danger', 'icon' => 'person-x', 'message' => 'معدل الحضور منخفض (' . number_format($attendanceRate, 0) . '%)، يرجى التحقق من سجلات الغياب.'];
            }

            if (count($salaryTrendData) >= 2 && end($salaryTrendData) > prev($salaryTrendData) * 1.2 && prev($salaryTrendData) > 0) {
                 $insights[] = ['type' => 'info', 'icon' => 'cash-stack', 'message' => 'ارتفاع ملحوظ في كتلة الرواتب مقارنة بالشهر السابق.'];
            }

            if ($tasksPending > $tasksCompleted * 2 && $tasksCompleted > 0) {
                $insights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'تراكم المهام المعلقة يتطلب تدخلاً إدارياً لتوزيع الأحمال.'];
            }

            $hrDashboardData = [
                'totalEmployees' => $totalEmployees,
                'totalVolunteers' => $totalVolunteers,
                'tasksTotal' => $tasksTotal,
                'tasksCompleted' => $tasksCompleted,
                'tasksPending' => $tasksPending,
                'avgRating' => $avgRating,
                'ratedTasksCount' => $ratedTasksCount,
                'topPerformers' => $topPerformers,
                'employeeAttendanceCount' => $employeeAttendanceCount,
                'volunteerAttendanceCount' => $volunteerAttendanceCount,
                'attendanceRate' => $attendanceRate,
                'totalSalaries' => $totalSalaries,
                'employeesPaidCount' => $employeesPaidCount,
                'salaryTrendLabels' => $salaryTrendLabels,
                'salaryTrendData' => $salaryTrendData,
                'pendingLeaves' => $pendingLeaves,
                'insights' => $insights
            ];
        }

        // Project Manager / Deputy Manager Detection
        $isProjectManager = false;
        $isDeputyManager = false;
        $managedProject = null;

        // Check if user is a project manager
        if ($user) {
            $managedProject = \App\Models\Project::where('manager_user_id', $user->id)->first();
            if ($managedProject) {
                $isProjectManager = true;
            } else {
                // Check if user is a deputy manager
                $managedProject = \App\Models\Project::where('deputy_user_id', $user->id)->first();
                if ($managedProject) {
                    $isDeputyManager = true;
                }
            }
        }

        // Guest House Manager Detection
        $isGuestHouseManager = false;
        $managedGuestHouse = null;
        $guestHouseManagerDashboardData = [];

        if ($user) {
            $managedGuestHouse = \App\Models\GuestHouse::where('manager_user_id', $user->id)->first();
            if ($managedGuestHouse) {
                $isGuestHouseManager = true;

                $now = \Carbon\Carbon::now();
                $currentMonth = $now->format('m');
                $currentYear = $now->format('Y');
                $gh = $managedGuestHouse;

                // Financial Stats
                $totalDonations = $gh->donations()->sum('amount') + $gh->donations()->sum('estimated_value');
                $donationsThisMonth = $gh->donations()
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $totalExpenses = $gh->expenses()->sum('amount');
                $expensesThisMonth = $gh->expenses()
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $netBalance = $totalDonations - $totalExpenses;

                // Capacity & Occupancy
                $capacity = $gh->capacity ?? 0;
                $currentGuests = $gh->beneficiaries()->where('status', 'active')->count();
                $occupancyRate = $capacity > 0 ? round(($currentGuests / $capacity) * 100) : 0;

                // Beneficiaries
                $beneficiariesTotal = $gh->beneficiaries()->count();
                $beneficiariesThisMonth = $gh->beneficiaries()
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();

                // Volunteer Stats
                $volunteersCount = $gh->volunteers()->count();
                $volunteersActive = $gh->volunteers()->whereNotNull('started_at')->count();

                // Tasks
                $tasksPending = Task::where('guest_house_id', $gh->id)->where('status', '!=', 'done')->count();
                $tasksCompleted = Task::where('guest_house_id', $gh->id)->where('status', 'done')
                    ->whereMonth('updated_at', $currentMonth)
                    ->count();
                $tasksTotal = Task::where('guest_house_id', $gh->id)->count();

                // Monthly Trend (Last 6 Months)
                $trendLabels = [];
                $guestsTrendData = [];
                $expensesTrendData = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = \Carbon\Carbon::now()->subMonths($i);
                    $trendLabels[] = $date->translatedFormat('F');
                    $guestsTrendData[] = $gh->beneficiaries()
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                    $expensesTrendData[] = $gh->expenses()
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('amount');
                }

                // Latest Items
                $latestTasks = Task::where('guest_house_id', $gh->id)->orderByDesc('id')->limit(5)->get();
                $latestBeneficiaries = $gh->beneficiaries()->orderByDesc('created_at')->limit(5)->get();
                $latestExpenses = $gh->expenses()->orderByDesc('created_at')->limit(5)->get();

                // AI Insights
                $ghInsights = [];
                if ($occupancyRate >= 90) {
                    $ghInsights[] = ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'الدار شبه ممتلئة! نسبة الإشغال ' . $occupancyRate . '%'];
                } elseif ($occupancyRate < 50 && $capacity > 0) {
                    $ghInsights[] = ['type' => 'info', 'icon' => 'house', 'message' => 'يوجد سعة متاحة! نسبة الإشغال ' . $occupancyRate . '% فقط'];
                }
                if ($netBalance > 0) {
                    $ghInsights[] = ['type' => 'success', 'icon' => 'graph-up-arrow', 'message' => 'الدار تحقق فائضاً مالياً بقيمة ' . number_format($netBalance) . ' ج.م'];
                } elseif ($netBalance < 0) {
                    $ghInsights[] = ['type' => 'danger', 'icon' => 'exclamation-triangle', 'message' => 'تنبيه! عجز مالي بقيمة ' . number_format(abs($netBalance)) . ' ج.م'];
                }
                if ($beneficiariesThisMonth > 5) {
                    $ghInsights[] = ['type' => 'success', 'icon' => 'people', 'message' => 'تم استقبال ' . $beneficiariesThisMonth . ' ضيف جديد هذا الشهر'];
                }
                if ($tasksPending > 5) {
                    $ghInsights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'يوجد ' . $tasksPending . ' مهمة معلقة تحتاج متابعة'];
                }
                if ($volunteersCount > 0) {
                    $ghInsights[] = ['type' => 'info', 'icon' => 'heart', 'message' => 'لديك ' . $volunteersCount . ' متطوع مسجل بالدار'];
                }

                $guestHouseManagerDashboardData = [
                    'guestHouse' => $gh,
                    'guestHouseId' => $gh->id,
                    'guestHouseName' => $gh->name,
                    'guestHouseLocation' => $gh->location,
                    'guestHousePhone' => $gh->phone,
                    'guestHouseStatus' => $gh->status,
                    'guestHouseCapacity' => $capacity,
                    'guestHouseManager' => $gh->manager,
                    // Occupancy
                    'currentGuests' => $currentGuests,
                    'occupancyRate' => $occupancyRate,
                    // Financial
                    'totalDonations' => $totalDonations,
                    'donationsThisMonth' => $donationsThisMonth,
                    'totalExpenses' => $totalExpenses,
                    'expensesThisMonth' => $expensesThisMonth,
                    'netBalance' => $netBalance,
                    // Beneficiaries
                    'beneficiariesTotal' => $beneficiariesTotal,
                    'beneficiariesThisMonth' => $beneficiariesThisMonth,
                    // Volunteers
                    'volunteersCount' => $volunteersCount,
                    'volunteersActive' => $volunteersActive,
                    // Tasks
                    'tasksPending' => $tasksPending,
                    'tasksCompleted' => $tasksCompleted,
                    'tasksTotal' => $tasksTotal,
                    // Trends
                    'trendLabels' => $trendLabels,
                    'guestsTrendData' => $guestsTrendData,
                    'expensesTrendData' => $expensesTrendData,
                    // Latest
                    'latestTasks' => $latestTasks,
                    'latestBeneficiaries' => $latestBeneficiaries,
                    'latestExpenses' => $latestExpenses,
                    // AI
                    'insights' => $ghInsights
                ];
            }
        }

        // Campaign Manager / Deputy Campaign Manager Detection
        $isCampaignManager = false;
        $isDeputyCampaignManager = false;
        $managedCampaign = null;
        $campaignManagerDashboardData = [];

        if ($user) {
            $managedCampaign = \App\Models\Campaign::where('manager_user_id', $user->id)->first();
            if ($managedCampaign) {
                $isCampaignManager = true;
            } else {
                // Try to find deputy campaign - column may not exist yet
                try {
                    if (\Schema::hasColumn('campaigns', 'deputy_user_id')) {
                        $managedCampaign = \App\Models\Campaign::where('deputy_user_id', $user->id)->first();
                        if ($managedCampaign) {
                            $isDeputyCampaignManager = true;
                        }
                    }
                } catch (\Exception $e) {
                    // Column doesn't exist yet, skip deputy check
                }
            }

            if ($isCampaignManager || $isDeputyCampaignManager) {
                $now = \Carbon\Carbon::now();
                $currentMonth = $now->format('m');
                $currentYear = $now->format('Y');
                $campaign = $managedCampaign;

                // Financial Stats
                $totalDonations = $campaign->donations()->sum('amount') + $campaign->donations()->sum('estimated_value');
                $donationsThisMonth = $campaign->donations()
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $donationsCount = $campaign->donations()->count();
                $avgDonation = $donationsCount > 0 ? round($totalDonations / $donationsCount) : 0;

                // Campaign Duration
                $startDate = $campaign->start_date;
                $endDate = $campaign->end_date;
                $totalDays = $startDate && $endDate ? $startDate->diffInDays($endDate) : 0;
                $daysRemaining = $endDate ? max(0, now()->diffInDays($endDate, false)) : 0;
                $daysElapsed = $startDate ? max(0, now()->diffInDays($startDate)) : 0;
                $progressRate = $totalDays > 0 ? min(100, round(($daysElapsed / $totalDays) * 100)) : 0;

                // Beneficiaries
                $beneficiariesTotal = $campaign->beneficiaries()->count();
                $beneficiariesThisMonth = $campaign->beneficiaries()
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();

                // Volunteer Stats
                $volunteersCount = $campaign->volunteers()->count();
                $volunteersActive = $campaign->volunteers()->whereNotNull('started_at')->count();
                $volunteerHours = $campaign->volunteers()->sum('hours');

                // Daily Menu Stats (for food campaigns)
                $dailyMenusCount = $campaign->dailyMenus()->count();
                $todayMenu = $campaign->dailyMenus()->whereDate('day_date', now()->format('Y-m-d'))->first();

                // Monthly Trend (Last 6 Months or Campaign Duration)
                $trendLabels = [];
                $donationTrendData = [];
                $beneficiaryTrendData = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = \Carbon\Carbon::now()->subMonths($i);
                    $trendLabels[] = $date->translatedFormat('F');
                    $donationTrendData[] = $campaign->donations()
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('amount');
                    $beneficiaryTrendData[] = $campaign->beneficiaries()
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                }

                // Latest Items
                $latestDonations = $campaign->donations()->with('donor')->orderByDesc('created_at')->limit(5)->get();
                $latestBeneficiaries = $campaign->beneficiaries()->orderByDesc('created_at')->limit(5)->get();

                // AI Insights
                $campaignInsights = [];
                if ($daysRemaining <= 7 && $daysRemaining > 0) {
                    $campaignInsights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'تنبيه! باقي ' . $daysRemaining . ' أيام فقط على انتهاء الحملة'];
                } elseif ($daysRemaining == 0 && $endDate && now()->lte($endDate)) {
                    $campaignInsights[] = ['type' => 'danger', 'icon' => 'clock', 'message' => 'اليوم هو آخر يوم في الحملة!'];
                }
                if ($totalDonations > 50000) {
                    $campaignInsights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'ممتاز! تجاوزت الحملة 50,000 ج.م في التبرعات'];
                }
                if ($donationsThisMonth > 0) {
                    $campaignInsights[] = ['type' => 'info', 'icon' => 'cash-stack', 'message' => 'تم استلام ' . number_format($donationsThisMonth) . ' ج.م تبرعات هذا الشهر'];
                }
                if ($beneficiariesThisMonth > 10) {
                    $campaignInsights[] = ['type' => 'success', 'icon' => 'people', 'message' => 'تم خدمة ' . $beneficiariesThisMonth . ' مستفيد هذا الشهر'];
                }
                if ($volunteersCount > 0) {
                    $campaignInsights[] = ['type' => 'info', 'icon' => 'heart', 'message' => 'لديك ' . $volunteersCount . ' متطوع مسجل بالحملة'];
                }
                if ($progressRate >= 80 && $progressRate < 100) {
                    $campaignInsights[] = ['type' => 'warning', 'icon' => 'graph-up', 'message' => 'الحملة وصلت ' . $progressRate . '% من مدتها'];
                }

                $campaignManagerDashboardData = [
                    'isCampaignManager' => $isCampaignManager,
                    'isDeputyCampaignManager' => $isDeputyCampaignManager,
                    'campaign' => $campaign,
                    'campaignId' => $campaign->id,
                    'campaignName' => $campaign->name,
                    'campaignStatus' => $campaign->status,
                    'campaignProject' => $campaign->project,
                    'campaignManager' => $campaign->manager,
                    'campaignDeputy' => $campaign->deputy,
                    'seasonYear' => $campaign->season_year,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    // Progress
                    'totalDays' => $totalDays,
                    'daysRemaining' => $daysRemaining,
                    'daysElapsed' => $daysElapsed,
                    'progressRate' => $progressRate,
                    // Financial
                    'totalDonations' => $totalDonations,
                    'donationsThisMonth' => $donationsThisMonth,
                    'donationsCount' => $donationsCount,
                    'avgDonation' => $avgDonation,
                    // Beneficiaries
                    'beneficiariesTotal' => $beneficiariesTotal,
                    'beneficiariesThisMonth' => $beneficiariesThisMonth,
                    // Volunteers
                    'volunteersCount' => $volunteersCount,
                    'volunteersActive' => $volunteersActive,
                    'volunteerHours' => $volunteerHours,
                    // Daily Menus
                    'dailyMenusCount' => $dailyMenusCount,
                    'todayMenu' => $todayMenu,
                    // Trends
                    'trendLabels' => $trendLabels,
                    'donationTrendData' => $donationTrendData,
                    'beneficiaryTrendData' => $beneficiaryTrendData,
                    // Latest
                    'latestDonations' => $latestDonations,
                    'latestBeneficiaries' => $latestBeneficiaries,
                    // AI
                    'insights' => $campaignInsights
                ];
            }
        }

        // Warehouse Manager Detection (أمين المخزن)
        $isWarehouseManager = false;
        $warehouseManagerDashboardData = [];

        if ($user && ($user->roles()->where('key', 'warehouse')->exists() || $user->hasPermission('inventory.manage'))) {
            $isWarehouseManager = true;
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');

            // Get all items with current stock levels
            $items = \App\Models\Item::all();
            $totalItems = $items->count();
            
            // Calculate stock levels for each item
            $lowStockItems = [];
            $outOfStockItems = [];
            $totalStockValue = 0;
            
            foreach ($items as $item) {
                $stockIn = \App\Models\InventoryTransaction::where('item_id', $item->id)
                    ->where('type', 'in')
                    ->sum('quantity');
                $stockOut = \App\Models\InventoryTransaction::where('item_id', $item->id)
                    ->where('type', 'out')
                    ->sum('quantity');
                $currentStock = $stockIn - $stockOut;
                
                $item->current_stock = $currentStock;
                $totalStockValue += $currentStock * ($item->estimated_value ?? 0);
                
                if ($currentStock <= 0) {
                    $outOfStockItems[] = $item;
                } elseif ($currentStock < 10) {
                    $lowStockItems[] = $item;
                }
            }

            // Transaction Stats
            $transactionsThisMonth = \App\Models\InventoryTransaction::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();
            $transactionsTotal = \App\Models\InventoryTransaction::count();
            
            $incomingThisMonth = \App\Models\InventoryTransaction::where('type', 'in')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('quantity');
            $outgoingThisMonth = \App\Models\InventoryTransaction::where('type', 'out')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('quantity');

            // Monthly Trend (Last 6 Months)
            $trendLabels = [];
            $incomingTrendData = [];
            $outgoingTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $trendLabels[] = $date->translatedFormat('F');
                $incomingTrendData[] = \App\Models\InventoryTransaction::where('type', 'in')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('quantity');
                $outgoingTrendData[] = \App\Models\InventoryTransaction::where('type', 'out')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('quantity');
            }

            // Latest Transactions
            $latestTransactions = \App\Models\InventoryTransaction::with(['item', 'warehouse'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            // Warehouses
            $warehouses = \App\Models\Warehouse::withCount('transactions')->get();
            $warehousesCount = $warehouses->count();

            // AI Insights
            $warehouseInsights = [];
            if (count($outOfStockItems) > 0) {
                $warehouseInsights[] = ['type' => 'danger', 'icon' => 'exclamation-triangle', 'message' => 'تنبيه! ' . count($outOfStockItems) . ' صنف نفذ من المخزون'];
            }
            if (count($lowStockItems) > 0) {
                $warehouseInsights[] = ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'تحذير! ' . count($lowStockItems) . ' صنف على وشك النفاذ'];
            }
            if ($incomingThisMonth > $outgoingThisMonth) {
                $warehouseInsights[] = ['type' => 'success', 'icon' => 'arrow-down-circle', 'message' => 'الواردات أكثر من الصادرات هذا الشهر'];
            } elseif ($outgoingThisMonth > $incomingThisMonth * 1.5) {
                $warehouseInsights[] = ['type' => 'warning', 'icon' => 'arrow-up-circle', 'message' => 'الصادرات مرتفعة! تحتاج لتجديد المخزون'];
            }
            if ($transactionsThisMonth > 50) {
                $warehouseInsights[] = ['type' => 'info', 'icon' => 'graph-up', 'message' => 'نشاط مرتفع! ' . $transactionsThisMonth . ' عملية هذا الشهر'];
            }
            if ($totalStockValue > 100000) {
                $warehouseInsights[] = ['type' => 'success', 'icon' => 'cash-stack', 'message' => 'قيمة المخزون: ' . number_format($totalStockValue) . ' ج.م'];
            }

            $warehouseManagerDashboardData = [
                'totalItems' => $totalItems,
                'lowStockItems' => $lowStockItems,
                'lowStockCount' => count($lowStockItems),
                'outOfStockItems' => $outOfStockItems,
                'outOfStockCount' => count($outOfStockItems),
                'totalStockValue' => $totalStockValue,
                'transactionsThisMonth' => $transactionsThisMonth,
                'transactionsTotal' => $transactionsTotal,
                'incomingThisMonth' => $incomingThisMonth,
                'outgoingThisMonth' => $outgoingThisMonth,
                'trendLabels' => $trendLabels,
                'incomingTrendData' => $incomingTrendData,
                'outgoingTrendData' => $outgoingTrendData,
                'latestTransactions' => $latestTransactions,
                'warehouses' => $warehouses,
                'warehousesCount' => $warehousesCount,
                'insights' => $warehouseInsights
            ];
        }

        // Logistics Manager Detection (مدير اللوجستيات)
        $isLogisticsManager = false;
        $logisticsManagerDashboardData = [];

        if ($user && ($user->roles()->where('key', 'logistics')->exists() || $user->hasPermission('logistics.manage'))) {
            $isLogisticsManager = true;
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');

            // Delegates Stats
            $totalDelegates = \App\Models\Delegate::count();
            $activeDelegates = \App\Models\Delegate::where('active', true)->count();
            $inactiveDelegates = $totalDelegates - $activeDelegates;

            // Trips Stats
            $tripsThisMonth = \App\Models\DelegateTrip::whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->count();
            $tripsTotal = \App\Models\DelegateTrip::count();
            $tripsCompleted = \App\Models\DelegateTrip::where('status', 'completed')->count();
            $tripsPending = \App\Models\DelegateTrip::where('status', 'pending')->count();
            $tripsInProgress = \App\Models\DelegateTrip::where('status', 'in_progress')->count();

            // Cost Stats
            $totalTripsCost = \App\Models\DelegateTrip::sum('cost');
            $tripsCostThisMonth = \App\Models\DelegateTrip::whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('cost');

            // Routes Stats
            $totalRoutes = \App\Models\TravelRoute::count();
            $routesWithDelegates = \App\Models\TravelRoute::has('delegates')->count();

            // Donations via Delegates
            $delegateDonations = \App\Models\Donation::whereNotNull('delegate_id')->count();
            $delegateDonationsAmount = \App\Models\Donation::whereNotNull('delegate_id')->sum('amount');

            // Monthly Trend (Last 6 Months)
            $trendLabels = [];
            $tripsTrendData = [];
            $costTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $trendLabels[] = $date->translatedFormat('F');
                $tripsTrendData[] = \App\Models\DelegateTrip::whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->count();
                $costTrendData[] = \App\Models\DelegateTrip::whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('cost');
            }

            // Latest Items
            $latestTrips = \App\Models\DelegateTrip::with('delegate')->orderByDesc('date')->limit(10)->get();
            $topDelegates = \App\Models\Delegate::withCount('trips')->orderByDesc('trips_count')->limit(5)->get();
            $routes = \App\Models\TravelRoute::withCount('delegates')->get();

            // AI Insights
            $logisticsInsights = [];
            if ($tripsPending > 10) {
                $logisticsInsights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'يوجد ' . $tripsPending . ' رحلة معلقة تحتاج متابعة'];
            }
            if ($tripsInProgress > 0) {
                $logisticsInsights[] = ['type' => 'info', 'icon' => 'truck', 'message' => $tripsInProgress . ' رحلة جارية حالياً'];
            }
            if ($activeDelegates < $totalDelegates * 0.5) {
                $logisticsInsights[] = ['type' => 'warning', 'icon' => 'person-x', 'message' => 'نسبة المندوبين النشطين منخفضة'];
            }
            if ($tripsThisMonth > 50) {
                $logisticsInsights[] = ['type' => 'success', 'icon' => 'graph-up', 'message' => 'نشاط مرتفع! ' . $tripsThisMonth . ' رحلة هذا الشهر'];
            }
            if ($tripsCostThisMonth > 0) {
                $logisticsInsights[] = ['type' => 'info', 'icon' => 'cash-stack', 'message' => 'تكلفة الرحلات: ' . number_format($tripsCostThisMonth) . ' ج.م هذا الشهر'];
            }
            if ($delegateDonations > 0) {
                $logisticsInsights[] = ['type' => 'success', 'icon' => 'gift', 'message' => $delegateDonations . ' تبرع تم جمعه عبر المندوبين'];
            }

            $logisticsManagerDashboardData = [
                'totalDelegates' => $totalDelegates,
                'activeDelegates' => $activeDelegates,
                'inactiveDelegates' => $inactiveDelegates,
                'tripsThisMonth' => $tripsThisMonth,
                'tripsTotal' => $tripsTotal,
                'tripsCompleted' => $tripsCompleted,
                'tripsPending' => $tripsPending,
                'tripsInProgress' => $tripsInProgress,
                'totalTripsCost' => $totalTripsCost,
                'tripsCostThisMonth' => $tripsCostThisMonth,
                'totalRoutes' => $totalRoutes,
                'routesWithDelegates' => $routesWithDelegates,
                'delegateDonations' => $delegateDonations,
                'delegateDonationsAmount' => $delegateDonationsAmount,
                'trendLabels' => $trendLabels,
                'tripsTrendData' => $tripsTrendData,
                'costTrendData' => $costTrendData,
                'latestTrips' => $latestTrips,
                'topDelegates' => $topDelegates,
                'routes' => $routes,
                'insights' => $logisticsInsights
            ];
        }

        $projectManagerDashboardData = [];
        $isManager = $user && ($user->roles()->where('key', 'manager')->exists() || $user->hasPermission('projects.edit') || $user->hasPermission('guest_houses.set_manager') || $isProjectManager || $isDeputyManager);
        $managerStats = [];

        if ($isProjectManager || $isDeputyManager) {
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');
            $project = $managedProject;

            // Financial Stats
            $projectDonations = $project->donations()->sum('amount') + $project->donations()->sum('estimated_value');
            $projectDonationsThisMonth = $project->donations()
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('amount');
            $projectExpenses = \App\Models\Expense::where('project_id', $project->id)->sum('amount') + $project->activities()->sum('expenses');
            $projectExpensesThisMonth = \App\Models\Expense::where('project_id', $project->id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('amount');
            $projectRevenue = $project->activities()->sum('revenue');
            $netBalance = $projectDonations + $projectRevenue - $projectExpenses;

            // Team Stats
            $employeesCount = $project->volunteers()->where('is_employee', true)->count();
            $volunteersCount = $project->volunteers()->where('is_volunteer', true)->count();
            $totalTeam = $employeesCount + $volunteersCount;

            // Tasks Stats
            $tasksPending = Task::where('project_id', $project->id)->where('status', '!=', 'done')->count();
            $tasksCompleted = Task::where('project_id', $project->id)->where('status', 'done')
                ->whereMonth('updated_at', $currentMonth)
                ->count();
            $tasksTotal = Task::where('project_id', $project->id)->count();
            $tasksCompletionRate = $tasksTotal > 0 ? round(($tasksCompleted / max(1, $tasksPending + $tasksCompleted)) * 100) : 0;

            // Beneficiaries Stats
            $beneficiariesTotal = $project->beneficiaries()->count();
            $beneficiariesThisMonth = $project->beneficiaries()
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            // Monthly Trend (Last 6 Months)
            $donationTrendLabels = [];
            $donationTrendData = [];
            $expenseTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $donationTrendLabels[] = $date->translatedFormat('F');
                $donationTrendData[] = $project->donations()
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount');
                $expenseTrendData[] = \App\Models\Expense::where('project_id', $project->id)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount');
            }

            // Campaign Stats
            $activeCampaigns = $project->campaigns()->where('status', 'active')->count();
            $totalCampaigns = $project->campaigns()->count();

            // Latest Items
            $latestTasks = Task::where('project_id', $project->id)->orderByDesc('id')->limit(5)->get();
            $latestDonations = $project->donations()->orderByDesc('created_at')->limit(5)->get();
            $latestActivities = $project->activities()->orderByDesc('activity_date')->limit(5)->get();
            $latestExpenses = \App\Models\Expense::where('project_id', $project->id)->orderByDesc('created_at')->limit(5)->get();

            // AI Insights
            $pmInsights = [];
            if ($netBalance > 0) {
                $pmInsights[] = ['type' => 'success', 'icon' => 'graph-up-arrow', 'message' => 'المشروع يحقق فائضاً مالياً بقيمة ' . number_format($netBalance) . ' ج.م'];
            } elseif ($netBalance < 0) {
                $pmInsights[] = ['type' => 'danger', 'icon' => 'exclamation-triangle', 'message' => 'تنبيه! المشروع يعاني من عجز مالي بقيمة ' . number_format(abs($netBalance)) . ' ج.م'];
            }
            if ($tasksCompletionRate >= 80) {
                $pmInsights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'معدل إنجاز ممتاز! ' . $tasksCompletionRate . '% من المهام مكتملة'];
            } elseif ($tasksPending > 10) {
                $pmInsights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'يوجد ' . $tasksPending . ' مهمة معلقة تحتاج متابعة'];
            }
            if ($projectDonationsThisMonth > 0) {
                $pmInsights[] = ['type' => 'info', 'icon' => 'cash-stack', 'message' => 'تم استلام ' . number_format($projectDonationsThisMonth) . ' ج.م تبرعات هذا الشهر'];
            }
            if ($beneficiariesThisMonth > 5) {
                $pmInsights[] = ['type' => 'info', 'icon' => 'people', 'message' => 'تم تسجيل ' . $beneficiariesThisMonth . ' مستفيد جديد هذا الشهر'];
            }
            if ($activeCampaigns > 0) {
                $pmInsights[] = ['type' => 'success', 'icon' => 'megaphone', 'message' => 'لديك ' . $activeCampaigns . ' حملة نشطة حالياً'];
            }

            // Manager & Deputy Info
            $projectManager = $project->manager;
            $projectDeputy = $project->deputy;

            $managerStats = [
                'type' => 'project',
                'name' => $project->name,
                'id' => $project->id,
                'total_donations' => $projectDonations,
                'total_expenses' => $projectExpenses,
                'net_balance' => $netBalance,
                'tasks_count' => $tasksPending,
                'employees_count' => $employeesCount,
                'volunteers_count' => $volunteersCount,
                'latest_activities' => $latestActivities,
                'latest_expenses' => $latestExpenses
            ];

            $projectManagerDashboardData = [
                'isProjectManager' => $isProjectManager,
                'isDeputyManager' => $isDeputyManager,
                'project' => $project,
                'projectId' => $project->id,
                'projectName' => $project->name,
                'projectDescription' => $project->description,
                'projectStatus' => $project->status,
                'projectManager' => $projectManager,
                'projectDeputy' => $projectDeputy,
                // Financial
                'totalDonations' => $projectDonations,
                'donationsThisMonth' => $projectDonationsThisMonth,
                'totalExpenses' => $projectExpenses,
                'expensesThisMonth' => $projectExpensesThisMonth,
                'totalRevenue' => $projectRevenue,
                'netBalance' => $netBalance,
                // Team
                'employeesCount' => $employeesCount,
                'volunteersCount' => $volunteersCount,
                'totalTeam' => $totalTeam,
                // Tasks
                'tasksPending' => $tasksPending,
                'tasksCompleted' => $tasksCompleted,
                'tasksTotal' => $tasksTotal,
                'tasksCompletionRate' => $tasksCompletionRate,
                // Beneficiaries
                'beneficiariesTotal' => $beneficiariesTotal,
                'beneficiariesThisMonth' => $beneficiariesThisMonth,
                // Campaigns
                'activeCampaigns' => $activeCampaigns,
                'totalCampaigns' => $totalCampaigns,
                // Trends
                'donationTrendLabels' => $donationTrendLabels,
                'donationTrendData' => $donationTrendData,
                'expenseTrendData' => $expenseTrendData,
                // Latest
                'latestTasks' => $latestTasks,
                'latestDonations' => $latestDonations,
                'latestActivities' => $latestActivities,
                'latestExpenses' => $latestExpenses,
                // AI
                'insights' => $pmInsights
            ];
        } elseif ($isManager) {
            if ($user->project) {
                $project = $user->project;
                $projectDonations = $project->donations()->sum('amount') + $project->donations()->sum('estimated_value');
                $projectExpenses = \App\Models\Expense::where('project_id', $project->id)->sum('amount') + $project->activities()->sum('expenses');
                $projectRevenue = $project->activities()->sum('revenue');
                $netBalance = $projectDonations + $projectRevenue - $projectExpenses;

                $managerStats = [
                    'type' => 'project',
                    'name' => $project->name,
                    'id' => $project->id,
                    'total_donations' => $projectDonations,
                    'total_expenses' => $projectExpenses,
                    'net_balance' => $netBalance,
                    'tasks_count' => Task::where('project_id', $project->id)->where('status', '!=', 'done')->count(),
                    'employees_count' => $project->volunteers()->where('is_employee', true)->count(),
                    'volunteers_count' => $project->volunteers()->where('is_volunteer', true)->count(),
                    'latest_activities' => $project->activities()->orderByDesc('activity_date')->limit(5)->get(),
                    'latest_expenses' => \App\Models\Expense::where('project_id', $project->id)->orderByDesc('created_at')->limit(5)->get()
                ];
            } elseif ($user->guestHouse) {
                $gh = $user->guestHouse;
                $managerStats = [
                    'type' => 'guest_house',
                    'name' => $gh->name,
                    'id' => $gh->id,
                    'total_donations' => 0,
                    'total_expenses' => \App\Models\Expense::where('guest_house_id', $gh->id)->sum('amount'),
                    'net_balance' => 0,
                    'tasks_count' => Task::where('guest_house_id', $gh->id)->where('status', '!=', 'done')->count(),
                    'employees_count' => 0,
                    'volunteers_count' => \App\Models\User::where('guest_house_id', $gh->id)->where('is_volunteer', true)->count(),
                    'latest_activities' => [],
                    'latest_expenses' => \App\Models\Expense::where('guest_house_id', $gh->id)->orderByDesc('created_at')->limit(5)->get()
                ];
            }
        }

        $isRepresent = $user && $user->roles()->where('key', 'represent')->exists();
        $representStats = [];
        if ($isRepresent && $user->project) {
            $project = $user->project;
            // Representative might see similar stats to Manager or subset
            $projectDonations = $project->donations()->sum('amount') + $project->donations()->sum('estimated_value');
            $projectExpenses = \App\Models\Expense::where('project_id', $project->id)->sum('amount'); // Maybe just expenses, no activities?

            $representStats = [
                'type' => 'project',
                'name' => $project->name,
                'id' => $project->id,
                'total_donations' => $projectDonations,
                'total_expenses' => $projectExpenses,
                'tasks_count' => Task::where('project_id', $project->id)->where('assigned_to', $user->id)->where('status', '!=', 'done')->count(), // My tasks in project
                'latest_activities' => $project->activities()->orderByDesc('activity_date')->limit(5)->get(),
            ];
        }

        // Field Researcher
        $isFieldResearcher = $user && ($user->roles()->where('key', 'field_researcher')->exists() || $user->roles()->where('name', 'باحث ميداني')->exists() || $user->hasPermission('visits.view'));
        $fieldResStats = [];
        $fieldResDashboardData = [];
        if ($isFieldResearcher) {
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');

            // Basic Stats
            $tasksPending = Task::where('assigned_to', $user->id)->where('status', '!=', 'done')->count();
            $tasksCompleted = Task::where('assigned_to', $user->id)->where('status', 'done')->whereMonth('updated_at', $currentMonth)->count();
            $tasksTotal = Task::where('assigned_to', $user->id)->count();
            $tasksInProgress = Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count();

            // Beneficiaries Stats
            $beneficiariesTotal = \App\Models\Beneficiary::count();
            $beneficiariesThisMonth = \App\Models\Beneficiary::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
            $beneficiariesPending = \App\Models\Beneficiary::where('status', 'under_review')->count();
            $beneficiariesAccepted = \App\Models\Beneficiary::where('status', 'accepted')->count();

            // Field Visits Stats (if FieldVisit model exists)
            $visitsThisMonth = 0;
            $visitsTotal = 0;
            $visitsPending = 0;
            try {
                $visitsThisMonth = \App\Models\FieldVisit::where('researcher_id', $user->id)
                    ->whereMonth('visit_date', $currentMonth)
                    ->whereYear('visit_date', $currentYear)
                    ->count();
                $visitsTotal = \App\Models\FieldVisit::where('researcher_id', $user->id)->count();
                $visitsPending = \App\Models\FieldVisit::where('researcher_id', $user->id)->where('status', 'pending')->count();
            } catch (\Exception $e) {
                // FieldVisit model might not exist
            }

            // Task Completion Rate
            $completionRate = $tasksTotal > 0 ? round(($tasksCompleted / max(1, $tasksPending + $tasksCompleted)) * 100) : 0;

            // Average Task Rating
            $avgRating = Task::where('assigned_to', $user->id)->whereNotNull('rating')->avg('rating') ?? 0;
            $ratedTasksCount = Task::where('assigned_to', $user->id)->whereNotNull('rating')->count();

            // Monthly Performance Trend (last 6 months)
            $performanceTrendLabels = [];
            $performanceTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $performanceTrendLabels[] = $date->translatedFormat('F');
                $performanceTrendData[] = Task::where('assigned_to', $user->id)
                    ->where('status', 'done')
                    ->whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->count();
            }

            // Latest Tasks
            $latestTasks = Task::where('assigned_to', $user->id)->orderByDesc('id')->limit(5)->get();

            // Latest Beneficiaries (for quick access)
            $latestBeneficiaries = \App\Models\Beneficiary::orderByDesc('id')->limit(5)->get();

            // AI Insights
            $frInsights = [];

            // Performance Insight
            if ($avgRating >= 4.5 && $ratedTasksCount >= 3) {
                $frInsights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'أداء متميز! متوسط تقييمك ' . number_format($avgRating, 1) . ' نجوم'];
            } elseif ($avgRating < 3 && $ratedTasksCount >= 3) {
                $frInsights[] = ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'يحتاج التقييم للتحسين. راجع ملاحظات المهام المكتملة.'];
            }

            // Task Load Insight
            if ($tasksPending > 10) {
                $frInsights[] = ['type' => 'danger', 'icon' => 'hourglass-split', 'message' => 'لديك ' . $tasksPending . ' مهمة معلقة. يُنصح بترتيب الأولويات.'];
            } elseif ($tasksPending == 0 && $tasksCompleted > 0) {
                $frInsights[] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'ممتاز! لا توجد مهام معلقة حالياً.'];
            }

            // Beneficiary Processing Insight
            if ($beneficiariesPending > 20) {
                $frInsights[] = ['type' => 'info', 'icon' => 'people', 'message' => 'يوجد ' . $beneficiariesPending . ' مستفيد قيد المراجعة. قد تحتاج زيارات ميدانية.'];
            }

            // Productivity Insight
            if ($tasksCompleted >= 5 && $completionRate >= 70) {
                $frInsights[] = ['type' => 'success', 'icon' => 'graph-up-arrow', 'message' => 'معدل إنجاز ممتاز هذا الشهر (' . $completionRate . '%)'];
            } elseif ($completionRate < 50 && $tasksPending > 5) {
                $frInsights[] = ['type' => 'warning', 'icon' => 'speedometer', 'message' => 'معدل الإنجاز منخفض (' . $completionRate . '%). حاول زيادة الإنتاجية.'];
            }

            // Visit Insight
            if ($visitsThisMonth > 0) {
                $frInsights[] = ['type' => 'info', 'icon' => 'geo-alt', 'message' => 'قمت بـ ' . $visitsThisMonth . ' زيارة ميدانية هذا الشهر.'];
            }

            $fieldResStats = [
                'tasks_pending' => $tasksPending,
                'tasks_completed' => $tasksCompleted,
                'beneficiaries_total' => $beneficiariesTotal,
                'latest_tasks' => $latestTasks,
            ];

            $fieldResDashboardData = [
                'tasksPending' => $tasksPending,
                'tasksCompleted' => $tasksCompleted,
                'tasksTotal' => $tasksTotal,
                'tasksInProgress' => $tasksInProgress,
                'beneficiariesTotal' => $beneficiariesTotal,
                'beneficiariesThisMonth' => $beneficiariesThisMonth,
                'beneficiariesPending' => $beneficiariesPending,
                'beneficiariesAccepted' => $beneficiariesAccepted,
                'visitsThisMonth' => $visitsThisMonth,
                'visitsTotal' => $visitsTotal,
                'visitsPending' => $visitsPending,
                'completionRate' => $completionRate,
                'avgRating' => $avgRating,
                'ratedTasksCount' => $ratedTasksCount,
                'performanceTrendLabels' => $performanceTrendLabels,
                'performanceTrendData' => $performanceTrendData,
                'latestTasks' => $latestTasks,
                'latestBeneficiaries' => $latestBeneficiaries,
                'insights' => $frInsights
            ];
        }

        // Receptionist
        $isReceptionist = $user && ($user->roles()->where('key', 'receptionist')->exists() || $user->roles()->where('name', 'الاستقبال')->exists() || $user->roles()->where('name', 'موظف استقبال')->exists() || $user->hasPermission('reception.view'));
        $receptionStats = [];
        $receptionDashboardData = [];
        if ($isReceptionist) {
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');

            // Beneficiary Stats
            $beneficiariesToday = \App\Models\Beneficiary::whereDate('created_at', now())->count();
            $beneficiariesThisWeek = \App\Models\Beneficiary::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            $beneficiariesThisMonth = \App\Models\Beneficiary::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            // Complaints Stats
            $complaintsOpen = \App\Models\Complaint::where('status', '!=', 'resolved')->count();
            $complaintsToday = \App\Models\Complaint::whereDate('created_at', now())->count();
            $complaintsResolved = \App\Models\Complaint::where('status', 'resolved')->whereMonth('updated_at', $currentMonth)->count();

            // Reception Logs
            $receptionLogsToday = 0;
            $receptionLogsTotal = 0;
            try {
                $receptionLogsToday = \App\Models\ReceptionLog::whereDate('created_at', now())->count();
                $receptionLogsTotal = \App\Models\ReceptionLog::whereMonth('created_at', $currentMonth)->count();
            } catch (\Exception $e) {}

            // Tasks
            $tasksPending = Task::where('assigned_to', $user->id)->where('status', '!=', 'done')->count();
            $tasksCompleted = Task::where('assigned_to', $user->id)->where('status', 'done')->whereMonth('updated_at', $currentMonth)->count();
            $latestTasks = Task::where('assigned_to', $user->id)->orderByDesc('id')->limit(5)->get();

            // Weekly Trend
            $weeklyTrendLabels = [];
            $weeklyTrendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $weeklyTrendLabels[] = $date->translatedFormat('l');
                $weeklyTrendData[] = \App\Models\Beneficiary::whereDate('created_at', $date)->count();
            }

            // AI Insights
            $recInsights = [];
            if ($complaintsOpen > 10) {
                $recInsights[] = ['type' => 'danger', 'icon' => 'exclamation-triangle', 'message' => 'يوجد ' . $complaintsOpen . ' شكوى مفتوحة تحتاج متابعة عاجلة'];
            }
            if ($beneficiariesToday > 5) {
                $recInsights[] = ['type' => 'info', 'icon' => 'people', 'message' => 'يوم نشط! استقبلت ' . $beneficiariesToday . ' مستفيد جديد اليوم'];
            }
            if ($tasksPending == 0 && $tasksCompleted > 0) {
                $recInsights[] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'ممتاز! أنهيت جميع مهامك المعلقة'];
            }
            if ($complaintsResolved > 5) {
                $recInsights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'أداء متميز! تم حل ' . $complaintsResolved . ' شكوى هذا الشهر'];
            }

            $receptionStats = [
                'beneficiaries_today' => $beneficiariesToday,
                'complaints_open' => $complaintsOpen,
                'tasks_pending' => $tasksPending,
                'latest_tasks' => $latestTasks,
            ];

            $receptionDashboardData = [
                'beneficiariesToday' => $beneficiariesToday,
                'beneficiariesThisWeek' => $beneficiariesThisWeek,
                'beneficiariesThisMonth' => $beneficiariesThisMonth,
                'complaintsOpen' => $complaintsOpen,
                'complaintsToday' => $complaintsToday,
                'complaintsResolved' => $complaintsResolved,
                'receptionLogsToday' => $receptionLogsToday,
                'receptionLogsTotal' => $receptionLogsTotal,
                'tasksPending' => $tasksPending,
                'tasksCompleted' => $tasksCompleted,
                'latestTasks' => $latestTasks,
                'weeklyTrendLabels' => $weeklyTrendLabels,
                'weeklyTrendData' => $weeklyTrendData,
                'insights' => $recInsights
            ];
        }

        $isVolunteer = $user && ($user->roles()->where('key', 'volunteer')->exists() || $user->is_volunteer); // check both role and flag
        $volunteerTasks = [];
        $volunteerStats = [];
        $volunteerDashboardData = [];
        if ($isVolunteer) {
            $now = \Carbon\Carbon::now();
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');

            $volunteerTasks = Task::where('assigned_to', $user->id)
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            // Determine assignment
            $assignment = 'غير محدد';
            $assignmentDescription = null;
            $assignmentEntity = null;
            $assignmentType = null;

            if ($user->project) {
                $assignment = 'مشروع: ' . $user->project->name;
                $assignmentDescription = $user->project->description;
                $assignmentEntity = $user->project;
                $assignmentType = 'project';
            } elseif ($user->campaign) {
                $assignment = 'حملة: ' . $user->campaign->name;
                $assignmentDescription = $user->campaign->description;
                $assignmentEntity = $user->campaign;
                $assignmentType = 'campaign';
            } elseif ($user->guestHouse) {
                $assignment = 'دار ضيافة: ' . $user->guestHouse->name;
                $assignmentDescription = $user->guestHouse->description;
                $assignmentEntity = $user->guestHouse;
                $assignmentType = 'guest_house';
            }

            // Hours Stats
            $totalHours = \App\Models\VolunteerHour::where('user_id', $user->id)->sum('hours');
            $hoursThisMonth = \App\Models\VolunteerHour::where('user_id', $user->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('hours');

            // Attendance Stats
            $attendanceThisMonth = VolunteerAttendance::where('user_id', $user->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->count();

            // Tasks Stats
            $tasksPending = Task::where('assigned_to', $user->id)->where('status', '!=', 'done')->count();
            $tasksCompleted = Task::where('assigned_to', $user->id)->where('status', 'done')->whereMonth('updated_at', $currentMonth)->count();
            $tasksTotal = Task::where('assigned_to', $user->id)->count();
            $avgRating = Task::where('assigned_to', $user->id)->whereNotNull('rating')->avg('rating') ?? 0;

            // Monthly Hours Trend
            $hoursTrendLabels = [];
            $hoursTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $hoursTrendLabels[] = $date->translatedFormat('F');
                $hoursTrendData[] = \App\Models\VolunteerHour::where('user_id', $user->id)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('hours');
            }

            // Activities
            $recentActivities = \App\Models\VolunteerHour::where('user_id', $user->id)->orderByDesc('date')->limit(5)->get();

            // AI Insights
            $volInsights = [];
            if ($totalHours >= 100) {
                $volInsights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'تهانينا! وصلت إلى ' . number_format($totalHours) . ' ساعة تطوعية'];
            }
            if ($hoursThisMonth >= 20) {
                $volInsights[] = ['type' => 'success', 'icon' => 'star', 'message' => 'أداء متميز! ' . $hoursThisMonth . ' ساعة هذا الشهر'];
            }
            if ($avgRating >= 4.5 && $tasksCompleted >= 3) {
                $volInsights[] = ['type' => 'success', 'icon' => 'award', 'message' => 'تقييم ممتاز بمتوسط ' . number_format($avgRating, 1) . ' نجوم'];
            }
            if ($tasksPending > 5) {
                $volInsights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'لديك ' . $tasksPending . ' مهمة معلقة'];
            }
            if ($attendanceThisMonth >= 20) {
                $volInsights[] = ['type' => 'info', 'icon' => 'calendar-check', 'message' => 'التزام رائع بالحضور! ' . $attendanceThisMonth . ' يوم'];
            }

            $volunteerStats = [
                'total_hours' => $totalHours,
                'join_date' => $user->join_date ?? $user->created_at->format('Y-m-d'),
                'current_assignment' => $assignment,
                'assignment_description' => $assignmentDescription,
                'activities' => $recentActivities
            ];

            $volunteerDashboardData = [
                'totalHours' => $totalHours,
                'hoursThisMonth' => $hoursThisMonth,
                'attendanceThisMonth' => $attendanceThisMonth,
                'tasksPending' => $tasksPending,
                'tasksCompleted' => $tasksCompleted,
                'tasksTotal' => $tasksTotal,
                'avgRating' => $avgRating,
                'assignment' => $assignment,
                'assignmentType' => $assignmentType,
                'assignmentDescription' => $assignmentDescription,
                'joinDate' => $user->join_date ?? $user->created_at->format('Y-m-d'),
                'hoursTrendLabels' => $hoursTrendLabels,
                'hoursTrendData' => $hoursTrendData,
                'recentActivities' => $recentActivities,
                'latestTasks' => $volunteerTasks,
                'insights' => $volInsights
            ];
        }
        $audits = collect();
        $audUserMap = collect();
        if ($isAdmin) {
            $audits = Audit::orderByDesc('id')->limit(10)->get();
            $audUserIds = $audits->pluck('user_id')->filter()->unique();
            $audUserMap = User::whereIn('id', $audUserIds)->get()->keyBy('id');
        }

        $notifications = [];
        if ($openComplaints > 0 && ($isAdmin || $isFinance)) {
            $notifications[] = ['text' => 'شكاوى مفتوحة: ' . $openComplaints, 'type' => 'warning'];
        }
        if ($openTasks > 0) {
            $notifications[] = ['text' => 'مهام مفتوحة: ' . $openTasks, 'type' => 'info'];
        }

        // Only show financial and general attendance alerts to Admin/Finance/HR
        if ($isAdmin || $isFinance || $isHr) {
            if ($attendanceToday == 0) {
                $notifications[] = ['text' => 'لا يوجد حضور مسجل اليوم (إجمالي)', 'type' => 'secondary'];
            }
            if ($attendanceToday > 0) {
                $notifications[] = ['text' => 'تم تسجيل حضور اليوم (' . $attendanceToday . ')', 'type' => 'success'];
            }
        }

        // Only show Net Flow to Admin/Finance
        if ($isAdmin || $isFinance) {
            if ($netFlowMonth < 0) {
                $notifications[] = ['text' => 'صافي التدفق هذا الشهر سالب', 'type' => 'danger'];
            }
            if ($netFlowMonth >= 0 && $monthDonationsTotal > 0) {
                $notifications[] = ['text' => 'صافي التدفق لهذا الشهر موجب', 'type' => 'success'];
            }
        }

        // Contract Expiry Notifications
        if ($isAdmin || $isHr) {
            $expiringContracts = User::whereNotNull('contract_end_date')
                ->whereBetween('contract_end_date', [now(), now()->addDays(60)])
                ->get();
            foreach ($expiringContracts as $expUser) {
                $daysLeft = (int) now()->diffInDays($expUser->contract_end_date, false);
                $notifications[] = [
                    'text' => "تنبيه: عقد الموظف {$expUser->name} ينتهي قريباً ({$expUser->contract_end_date->format('Y-m-d')}) - باقي {$daysLeft} يوم",
                    'type' => 'warning'
                ];
            }
        } else {
            // Check for current user only
            if ($user->contract_end_date && now()->diffInDays($user->contract_end_date, false) <= 60 && now()->diffInDays($user->contract_end_date, false) >= 0) {
                $daysLeft = (int) now()->diffInDays($user->contract_end_date, false);
                $notifications[] = [
                    'text' => "تنبيه: عقدك ينتهي قريباً ({$user->contract_end_date->format('Y-m-d')}) - باقي {$daysLeft} يوم",
                    'type' => 'warning'
                ];
            }
        }

        // Check for today's attendance record for the current user
        $todayRecord = null;
        if ($user) {
            $todayRecord = EmployeeAttendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->first();
        }

        return view('dashboard.index', compact(
            'donorsCount',
            'beneficiariesCount',
            'warehousesCount',
            'volunteersCount',
            'employeesCount',
            'cashDonations',
            'inKindDonations',
            'inventoryNet',
            'openTasks',
            'attendanceToday',
            'vhoursMonth',
            'payrollMonth',
            'openComplaints',
            'latestDonations',
            'latestTasks',
            'latestAttendance',
            'months',
            'cashSeries',
            'inKindSeries',
            'beneficiaryStatus',
            'evaluations'
            ,
            'cashMonth',
            'inKindMonth',
            'monthDonationsTotal',
            'cashMonthPct',
            'inKindMonthPct',
            'expensesMonth',
            'netFlowMonth',
            'topDonors',
            'pendingLeaves',
            'currentlyOnLeave'
            ,
            'audits',
            'audUserMap',
            'isAdmin',
            'notifications',
            'latestExpenses',
            'expenseByCat'
            ,
            'activeCampaigns',
            'activeProjects',
            'inventoryLevels',
            'projectActivitiesRevenue',
            'expenseSeries',
            'isFinance',
            'financeStats',
            'isHr',
            'hrStats',
            'hrDashboardData',
            'isVolunteer',
            'volunteerTasks',
            'volunteerStats',
            'volunteerDashboardData',
            'isManager',
            'managerStats',
            'isProjectManager',
            'isDeputyManager',
            'projectManagerDashboardData',
            'isGuestHouseManager',
            'guestHouseManagerDashboardData',
            'isCampaignManager',
            'isDeputyCampaignManager',
            'campaignManagerDashboardData',
            'isWarehouseManager',
            'warehouseManagerDashboardData',
            'isLogisticsManager',
            'logisticsManagerDashboardData',
            'isRepresent',
            'representStats',
            'isFieldResearcher',
            'fieldResStats',
            'fieldResDashboardData',
            'isReceptionist',
            'receptionStats',
            'receptionDashboardData',
            'user',
            'todayRecord'
        ));
    }
}
