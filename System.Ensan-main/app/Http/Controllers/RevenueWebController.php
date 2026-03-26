<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueWebController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtering
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        // --- Data Source 1: Donations ---
        $donationsQuery = Donation::query()
            ->whereDate('received_at', '>=', $startDate)
            ->whereDate('received_at', '<=', $endDate)
            ->where('status', '!=', 'cancelled');
            
        $donations = (clone $donationsQuery)->with(['donor', 'project'])->get();

        // --- Data Source 2: Workspace Rentals ---
        $rentals = \App\Models\WorkspaceRental::whereIn('status', ['completed', 'confirmed', 'paid'])
            ->whereDate('start_time', '>=', $startDate)
            ->whereDate('start_time', '<=', $endDate)
            ->with('workspace')
            ->get();

        // --- Data Source 3: Expenses (Negative Revenue for Analysis) ---
        $expenses = \App\Models\Expense::where('status', '!=', 'cancelled')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->with(['project'])
            // Note: If you have a separate category model or just string, handle accordingly. 
            // Most Laravel setups use string or simple relation.
            ->get();

        // --- Merge & Unify Data Streams ---
        $allRevenues = collect();

        // Process Donations
        foreach ($donations as $d) {
            $source = 'donation';
            $label = 'تبرعات عامة';
            
            if ($d->project_id == 1) {
                $source = 'project_revenue';
                $label = 'إيرادات مشروع كسوة';
                if ($d->allocation_note && (str_contains($d->allocation_note, 'إعلان') || str_contains($d->allocation_note, 'رعاية'))) {
                    $label = 'إعلانات ورعايات';
                }
            }

            $allRevenues->push([
                'id' => 'DON-' . $d->id,
                'date' => $d->received_at ?: $d->created_at,
                'amount' => (float) ($d->type === 'cash' ? ($d->amount ?: 0) : ($d->estimated_value ?: 0)),
                'type' => $d->type === 'cash' ? 'money' : 'items',
                'source' => $source, 
                'source_label' => $label,
                'description' => 'تبرع ' . ($d->type === 'cash' ? 'نقدي' : 'عيني') . ' - ' . ($d->donor->name ?? 'فاعل خير'),
                'project_name' => $d->project->name ?? 'تبرع عام',
                'entity' => 'Donation'
            ]);
        }

        // Process Rentals
        foreach ($rentals as $r) {
            $allRevenues->push([
                'id' => 'WRK-' . $r->id,
                'date' => $r->start_time,
                'amount' => (float) $r->total_price,
                'type' => 'service',
                'source' => 'workspace',
                'source_label' => 'إيرادات مساحات العمل',
                'description' => 'حجز مساحة: ' . ($r->workspace->name ?? 'غير محدد') . ' - ' . $r->renter_name,
                'project_name' => 'Workspace Ensan',
                'entity' => 'WorkspaceRental'
            ]);
        }

        // Process Expenses (Optional: Usually revenue dashboards show gross income, but sometimes nets)
        // Here we will add them as "Outflow" or simply for the Recent Transactions list
        foreach ($expenses as $e) {
            $allRevenues->push([
                'id' => 'EXP-' . $e->id,
                'date' => $e->paid_at ?: $e->created_at,
                'amount' => (float) $e->amount,
                'type' => 'expense',
                'source' => 'expense',
                'source_label' => 'مصروفات',
                'description' => 'مصروف - ' . ($e->description ?: ($e->category ?? 'غير محدد')),
                'project_name' => $e->project->name ?? 'مصروفات عامة',
                'entity' => 'Expense',
                'is_outflow' => true
            ]);
        }

        // Sort by Date Descending
        $allRevenues = $allRevenues->sortByDesc('date');

        // --- Aggregates (Inflows only for total revenue) ---
        $inflows = $allRevenues->where('is_outflow', '!=', true);
        $totalRevenue = $inflows->sum('amount');
        $totalCount = $inflows->count();

        // Initialize default sources with zero
        $defaultSources = [
            'تبرعات عامة' => ['total' => 0, 'count' => 0, 'percentage' => 0],
            'إيرادات مشروع كسوة' => ['total' => 0, 'count' => 0, 'percentage' => 0],
            'إعلانات ورعايات' => ['total' => 0, 'count' => 0, 'percentage' => 0],
            'إيرادات مساحات العمل' => ['total' => 0, 'count' => 0, 'percentage' => 0]
        ];

        // Group by Source Label and merge with defaults
        $revenueBySource = $inflows->groupBy('source_label')->map(function ($row) use ($totalRevenue) {
            $sum = $row->sum('amount');
            return [
                'total' => $sum,
                'count' => $row->count(),
                'percentage' => $totalRevenue > 0 ? ($sum / $totalRevenue) * 100 : 0
            ];
        });
        
        $revenueBySource = collect($defaultSources)->merge($revenueBySource)->sortDesc();    

        // Group by Payment Type
        $revenueByType = $inflows->groupBy('type')->map(function ($row) use ($totalRevenue) {
            return [
                'total' => $row->sum('amount'),
                'count' => $row->count(),
                'percentage' => $totalRevenue > 0 ? ($row->sum('amount') / $totalRevenue) * 100 : 0
            ];
        });

        // Top Projects
        $revenueByProject = $inflows->whereNotNull('project_name')->groupBy('project_name')->map(function ($row) {
            return $row->sum('amount');
        })->sortDesc()->take(5);

        // Recent Transactions (First 15, mixed)
        $recentRevenues = $allRevenues->take(15);

        // --- AI Insights ---
        $insights = [];
        
        if ($totalRevenue > 1000) {
            $insights[] = ['type' => 'success', 'message' => 'الأداء المالي جيد، إجمالي الإيرادات في نمو مستمر.'];
        }

        $topSource = $revenueBySource->keys()->first();
        if ($topSource) {
            $insights[] = ['type' => 'info', 'message' => "المصدر الرئيسي للدخل حالياً هو: $topSource"];
        }

        $workspaceIncome = $revenueBySource['إيرادات مساحات العمل']['total'] ?? 0;
        if ($totalRevenue > 0 && $workspaceIncome > 0) {
             $insights[] = ['type' => 'primary', 'message' => 'مساحات العمل تساهم بنسبة ' . number_format(($workspaceIncome / $totalRevenue * 100), 1) . '% من الدخل.'];
        }

        if (isset($revenueBySource['إيرادات مشروع كسوة']) && $revenueBySource['إيرادات مشروع كسوة']['total'] > 0) {
             $insights[] = ['type' => 'warning', 'message' => 'مشروع كسوة يحقق عوائد، يرجى مراجعة تفاصيل الإعلانات لتعزيزها.'];
        }

        return view('revenues.index', compact(
            'totalRevenue', 
            'totalCount', 
            'revenueByType', 
            'revenueBySource', 
            'revenueByProject', 
            'recentRevenues', 
            'insights',
            'startDate',
            'endDate'
        ));
    }
}
