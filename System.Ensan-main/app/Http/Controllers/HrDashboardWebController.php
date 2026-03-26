<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Payroll;
use App\Models\VolunteerAttendance;
use App\Models\EmployeeAttendance;
use App\Models\User;
use Carbon\Carbon;

class HrDashboardWebController extends Controller
{
    public function index()
    {
        // 1. Employee & Volunteer Stats
        $totalEmployees = User::where('is_employee', true)->count();
        $totalVolunteers = User::where('is_volunteer', true)->count();
        
        // 2. Tasks Stats
        $tasksTotal = Task::count();
        $tasksCompleted = Task::where('status', 'done')->count();
        $tasksPending = Task::where('status', 'pending')->count();
        $tasksInProgress = Task::where('status', 'in_progress')->count();

        // 3. Evaluation Stats
        $avgRating = Task::whereNotNull('rating')->avg('rating') ?? 0;
        $ratedTasksCount = Task::whereNotNull('rating')->count();
        
        // Top Performers (Users with high average rating on completed tasks)
        $topPerformers = User::whereHas('assignedTasks', function($q) {
                $q->where('status', 'done')->where('rating', '>=', 4.5);
            })
            ->withCount(['assignedTasks as avg_rating' => function($q) {
                $q->where('status', 'done')->select(\Illuminate\Support\Facades\DB::raw('avg(rating)'));
            }])
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        // 4. Attendance Stats (Current Month)
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');

        $employeeAttendanceCount = EmployeeAttendance::whereMonth('date', $currentMonth)
                                                     ->whereYear('date', $currentYear)
                                                     ->count();
        
        $volunteerAttendanceCount = VolunteerAttendance::whereMonth('date', $currentMonth)
                                                       ->whereYear('date', $currentYear)
                                                       ->count();

        // Attendance Rate Calculation
        $workingDaysPassed = max(1, $now->day); 
        $expectedAttendance = $totalEmployees * $workingDaysPassed; 
        $attendanceRate = $expectedAttendance > 0 ? min(100, ($employeeAttendanceCount / $expectedAttendance) * 100) : 0; 

        // 5. Salaries Stats (Current Month vs Last 6 Months)
        $currentMonthString = $now->format('Y-m');
        $totalSalaries = Payroll::where('month', $currentMonthString)->sum('amount');
        $employeesPaidCount = Payroll::where('month', $currentMonthString)->distinct('user_id')->count('user_id');

        // 6 Month Trend
        $salaryTrendLabels = [];
        $salaryTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mStr = $date->format('Y-m');
            $salaryTrendLabels[] = $date->translatedFormat('F');
            $salaryTrendData[] = Payroll::where('month', $mStr)->sum('amount');
        }

        // 6. Leaves Stats
        $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count(); 
        
        // 7. AI Insights
        $insights = [];
        if ($avgRating > 4.5) {
            $insights[] = ['type' => 'success', 'icon' => 'trophy', 'message' => 'أداء الفريق ممتاز هذا الشهر بمتوسط تقييم ' . number_format($avgRating, 1)];
        } elseif ($avgRating < 3 && $ratedTasksCount > 5) {
            $insights[] = ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'انخفاض في مستوى الأداء العام، يرجى مراجعة التقييمات.'];
        }

        if ($attendanceRate < 70 && $totalEmployees > 5) {
            $insights[] = ['type' => 'danger', 'icon' => 'person-x', 'message' => 'معدل الحضور منخفض (' . number_format($attendanceRate, 0) . '%)، يرجى التحقق من سجلات الغياب.'];
        }

        if (end($salaryTrendData) > prev($salaryTrendData) * 1.2 && prev($salaryTrendData) > 0) {
             $insights[] = ['type' => 'info', 'icon' => 'cash-stack', 'message' => 'ارتفاع ملحوظ في كتلة الرواتب مقارنة بالشهر السابق.'];
        }

        if ($tasksPending > $tasksCompleted * 2 && $tasksCompleted > 0) {
            $insights[] = ['type' => 'warning', 'icon' => 'hourglass-split', 'message' => 'تراكم المهام المعلقة يتطلب تدخلاً إدارياً لتوزيع الأحمال.'];
        }

        return view('hr.dashboard', compact(
            'tasksTotal', 'tasksCompleted', 'tasksPending', 'tasksInProgress',
            'avgRating', 'ratedTasksCount', 
            'employeeAttendanceCount', 'volunteerAttendanceCount',
            'totalEmployees', 'totalVolunteers',
            'totalSalaries', 'employeesPaidCount',
            'salaryTrendLabels', 'salaryTrendData',
            'pendingLeaves', 'topPerformers', 'attendanceRate', 'insights'
        ));
    }
}
