<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Payroll;
use App\Models\VolunteerAttendance;
use App\Models\EmployeeAttendance;
use App\Models\User;
use Carbon\Carbon;

class HrEvaluationWebController extends Controller
{
    public function index()
    {
        // Tasks Stats
        $tasksTotal = Task::count();
        $tasksCompleted = Task::where('status', 'done')->count();
        $tasksPending = Task::where('status', 'pending')->count();
        $tasksInProgress = Task::where('status', 'in_progress')->count();

        // Evaluation Stats (from Tasks)
        $avgRating = Task::whereNotNull('rating')->avg('rating') ?? 0;
        $ratedTasksCount = Task::whereNotNull('rating')->count();
        
        // Rating Distribution
        $ratingsDist = [
            5 => Task::where('rating', 5)->count(),
            4 => Task::where('rating', 4)->count(),
            3 => Task::where('rating', 3)->count(),
            2 => Task::where('rating', 2)->count(),
            1 => Task::where('rating', 1)->count(),
        ];

        // Attendance Stats (Current Month)
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $employeeAttendanceCount = EmployeeAttendance::whereMonth('date', $currentMonth)
                                                     ->whereYear('date', $currentYear)
                                                     ->count();
        
        $volunteerAttendanceCount = VolunteerAttendance::whereMonth('date', $currentMonth)
                                                       ->whereYear('date', $currentYear)
                                                       ->count();
        
        $totalEmployees = User::where('is_employee', true)->count();
        $totalVolunteers = User::where('is_volunteer', true)->count();

        // Salaries Stats (Current Month)
        // Assuming 'month' string format YYYY-MM
        $currentMonthString = Carbon::now()->format('Y-m');
        $totalSalaries = Payroll::where('month', $currentMonthString)->sum('amount');
        $totalSalariesCount = Payroll::where('month', $currentMonthString)->count();
        $employeesPaidCount = Payroll::where('month', $currentMonthString)->distinct('user_id')->count('user_id');

        return view('hr.evaluations', compact(
            'tasksTotal', 'tasksCompleted', 'tasksPending', 'tasksInProgress',
            'avgRating', 'ratedTasksCount', 'ratingsDist',
            'employeeAttendanceCount', 'volunteerAttendanceCount',
            'totalEmployees', 'totalVolunteers',
            'totalSalaries', 'totalSalariesCount', 'employeesPaidCount'
        ));
    }
}
