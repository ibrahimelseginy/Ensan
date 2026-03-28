<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final class SalesTargetWebController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        // Parse month
        try {
            $monthDate = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $monthDate = Carbon::now();
            $month = $monthDate->format('Y-m');
        }

        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth   = $monthDate->copy()->endOfMonth();

        // Get all sales employees (is_sales = true OR role = 'sales')
        $salesEmployees = User::where('is_employee', true)
            ->where(function($q) {
                $q->where('is_sales', true)
                  ->orWhereHas('roles', function($r) {
                      $r->whereIn('key', ['sales', 'sales_rep', 'marketer']);
                  });
            })
            ->orderBy('name')
            ->get();

        // Calculate stats for each employee
        $employeeStats = [];
        $totalCommission = 0;
        $totalBaseSalary = 0;
        $totalEntitlements = 0;

        foreach ($salesEmployees as $employee) {
            // Donations created by this employee in the selected month
            $donationsQuery = Donation::where('created_by', $employee->id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            $donationsCount = $donationsQuery->count();
            $donationsTotal = $donationsQuery->sum('amount') + 
                              Donation::where('created_by', $employee->id)
                                      ->where('status', '!=', 'cancelled')
                                      ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                      ->sum('estimated_value');

            // Actually recalculate cleanly
            $cashTotal = Donation::where('created_by', $employee->id)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'cash')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $inkindTotal = Donation::where('created_by', $employee->id)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'in_kind')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('estimated_value');

            $totalAmount = (float)($cashTotal + $inkindTotal);

            // Target and commission calculations
            $target = (float)($employee->monthly_target ?? 0);
            $commissionRate = (float)($employee->commission_rate ?? 0);
            $baseSalary = (float)($employee->salary ?? 0);

            // Target achievement
            $targetPct = $target > 0 ? min(($totalAmount / $target) * 100, 999) : ($totalAmount > 0 ? 999 : 0);

            // Commission calculation
            $commission = $target > 0 && $totalAmount >= $target 
                ? ($totalAmount * $commissionRate / 100) 
                : 0;

            // If amount is less than target, check partial commission (commission only on amount if target > 0)
            if ($target > 0 && $totalAmount < $target) {
                // No commission if target not met (strict mode)
                // Uncomment below for partial commission:
                // $commission = $totalAmount * $commissionRate / 100;
                $commission = 0;
            }

            $totalEntitlement = $baseSalary + $commission;

            // Tier label
            if ($target <= 0) {
                $tierLabel = 'لا يوجد تارجت';
                $tierColor = 'secondary';
            } elseif ($targetPct >= 150) {
                $tierLabel = 'استثنائي ★★★';
                $tierColor = 'warning';
            } elseif ($targetPct >= 100) {
                $tierLabel = 'حقق التارجت ✓';
                $tierColor = 'success';
            } elseif ($targetPct >= 75) {
                $tierLabel = 'قريب من التارجت';
                $tierColor = 'info';
            } elseif ($targetPct >= 50) {
                $tierLabel = 'نصف الطريق';
                $tierColor = 'primary';
            } elseif ($totalAmount > 0) {
                $tierLabel = 'لم يحقق تارجت';
                $tierColor = 'danger';
            } else {
                $tierLabel = 'لم يحقق تارجت';
                $tierColor = 'danger';
            }

            $totalCommission += $commission;
            $totalBaseSalary += $baseSalary;
            $totalEntitlements += $totalEntitlement;

            $employeeStats[] = [
                'user'              => $employee,
                'donations_count'   => $donationsCount,
                'donations_total'   => $totalAmount,
                'target'            => $target,
                'target_pct'        => round($targetPct, 1),
                'commission_rate'   => $commissionRate,
                'commission'        => $commission,
                'base_salary'       => $baseSalary,
                'total_entitlement' => $totalEntitlement,
                'tier_label'        => $tierLabel,
                'tier_color'        => $tierColor,
            ];
        }

        // Sort by donations_total descending (ranking)
        usort($employeeStats, fn($a, $b) => $b['donations_total'] <=> $a['donations_total']);

        // Total donations created by all sales employees this month
        $salesEmployeeIds = $salesEmployees->pluck('id')->toArray();
        $totalDonationsByAll = 0;
        if (!empty($salesEmployeeIds)) {
            $totalDonationsByAll = (float)(
                Donation::whereIn('created_by', $salesEmployeeIds)
                    ->where('status', '!=', 'cancelled')
                    ->where('type', 'cash')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount') +
                Donation::whereIn('created_by', $salesEmployeeIds)
                    ->where('status', '!=', 'cancelled')
                    ->where('type', 'in_kind')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('estimated_value')
            );
        }

        return view('sales.target', compact(
            'employeeStats',
            'month',
            'monthDate',
            'totalCommission',
            'totalBaseSalary',
            'totalEntitlements',
            'totalDonationsByAll',
            'salesEmployees'
        ));
    }

    public function updateEmployee(Request $request)
    {
        $data = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'monthly_target'  => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_sales'        => 'nullable|boolean',
        ]);

        $user = User::findOrFail($data['user_id']);
        $user->update([
            'monthly_target'  => $data['monthly_target'] ?? $user->monthly_target,
            'commission_rate' => $data['commission_rate'] ?? $user->commission_rate,
            'is_sales'        => $data['is_sales'] ?? $user->is_sales,
        ]);

        return back()->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }
}
