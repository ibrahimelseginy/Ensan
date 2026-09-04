<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\Payroll;
use App\Models\VolunteerAttendance;
use App\Models\EmployeeAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final readonly class HrEvaluationService
{
    public function getGlobalEvaluationStats(): array
    {
        $currentMonth       = Carbon::now()->format('m');
        $currentYear        = Carbon::now()->format('Y');
        $currentMonthString = Carbon::now()->format('Y-m');

        return [
            // Task Stats
            'tasksTotal'               => Task::count(),
            'tasksCompleted'           => Task::where('status', 'done')->count(),
            'tasksPending'             => Task::where('status', 'pending')->count(),
            'tasksInProgress'          => Task::where('status', 'in_progress')->count(),

            // Evaluation Stats (from Tasks)
            'avgRating'                => (float) (Task::whereNotNull('rating')->avg('rating') ?? 0.0),
            'ratedTasksCount'          => Task::whereNotNull('rating')->count(),
            
            // Rating Distribution
            'ratingsDist'              => [
                5 => Task::where('rating', 5)->count(),
                4 => Task::where('rating', 4)->count(),
                3 => Task::where('rating', 3)->count(),
                2 => Task::where('rating', 2)->count(),
                1 => Task::where('rating', 1)->count(),
            ],

            // Attendance Stats
            'employeeAttendanceCount'  => EmployeeAttendance::whereMonth('date', $currentMonth)
                                            ->whereYear('date', $currentYear)
                                            ->count(),
            'volunteerAttendanceCount' => VolunteerAttendance::whereMonth('date', $currentMonth)
                                            ->whereYear('date', $currentYear)
                                            ->count(),
            
            'totalEmployees'           => User::where('is_employee', true)->count(),
            'totalVolunteers'          => User::where('is_volunteer', true)->count(),

            // Salaries Stats
            'totalSalaries'            => (float) Payroll::where('month', 'LIKE', $currentMonthString . '%')->sum('amount'),
            'totalSalariesCount'       => Payroll::where('month', 'LIKE', $currentMonthString . '%')->count(),
            'employeesPaidCount'       => Payroll::where('month', 'LIKE', $currentMonthString . '%')->distinct('user_id')->count('user_id'),
        ];
    }
}
