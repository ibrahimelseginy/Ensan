<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Workspace;
use App\Models\GuestHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseWebController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $campaignId = $request->get('campaign_id');
        $workspaceId = $request->get('workspace_id');
        $guestHouseId = $request->get('guest_house_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type');

        $query = Expense::with(['beneficiary', 'project', 'campaign', 'workspace'])
            ->when($projectId, function ($q) use ($projectId) { $q->where('project_id', $projectId); })
            ->when($campaignId, function ($q) use ($campaignId) { $q->where('campaign_id', $campaignId); })
            ->when($workspaceId, function ($q) use ($workspaceId) { $q->where('workspace_id', $workspaceId); })
            ->when($guestHouseId, function ($q) use ($guestHouseId) { $q->where('guest_house_id', $guestHouseId); })
            ->when($type, function ($q) use ($type) { $q->where('type', $type); })
            ->when($startDate, function ($q) use ($startDate) { $q->whereDate('created_at', '>=', $startDate); })
            ->when($endDate, function ($q) use ($endDate) { $q->whereDate('created_at', '<=', $endDate); });

        // Basic Pagination
        $expenses = (clone $query)->orderByDesc('id')->paginate(20);

        // --- AI & Financial Analysis ---
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        $totalCurrentMonth = Expense::where('status', '!=', 'cancelled')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
        $totalLastMonth = Expense::where('status', '!=', 'cancelled')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $lastMonthYear)->sum('amount');
        
        // Growth Calculation
        $growth = 0;
        if($totalLastMonth > 0) {
            $growth = (($totalCurrentMonth - $totalLastMonth) / $totalLastMonth) * 100;
        }

        // Category Breakdown
        $categoryBreakdown = Expense::where('status', '!=', 'cancelled')->groupBy('type')
            ->selectRaw('type, sum(amount) as total')
            ->get()
            ->pluck('total', 'type');

        // Monthly Comparison (Last 6 Months)
        $monthlyComparison = [];
        for($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthlyComparison[] = [
                'label' => $monthDate->translatedFormat('F'),
                'total' => Expense::where('status', '!=', 'cancelled')->whereMonth('created_at', $monthDate->month)->whereYear('created_at', $monthDate->year)->sum('amount')
            ];
        }

        // Smart Insights (Simulated AI)
        $insights = [];
        if($growth > 10) {
            $insights[] = ['type' => 'warning', 'msg' => "ارتفاع ملحوظ في مصروفات الشهر الحالي بنسبة " . round($growth, 1) . "%"];
        } elseif($growth < 0) {
            $insights[] = ['type' => 'success', 'msg' => "انخفاض جيد في المصروفات بنسبة " . abs(round($growth, 1)) . "% مقارنة بالشهر السابق"];
        }

        $highestCategory = $categoryBreakdown->sortDesc()->keys()->first();
        if($highestCategory) {
            $insights[] = ['type' => 'info', 'msg' => "التصنيف الأكثر استهلاكاً هو: $highestCategory"];
        }

        return view('expenses.index', compact('expenses', 'totalCurrentMonth', 'growth', 'categoryBreakdown', 'monthlyComparison', 'insights'));
    }

    public function export(Request $request)
    {
        $projectId = $request->get('project_id');
        $campaignId = $request->get('campaign_id');
        $workspaceId = $request->get('workspace_id');
        $month = $request->get('month');
        $year = $request->get('year');

        $query = Expense::with(['beneficiary', 'project', 'campaign', 'workspace'])
            ->when($projectId, function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            })
            ->when($campaignId, function ($q) use ($campaignId) {
                $q->where('campaign_id', $campaignId);
            })
            ->when($workspaceId, function ($q) use ($workspaceId) {
                $q->where('workspace_id', $workspaceId);
            })
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('created_at', $month);
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('created_at', $year);
            })
            ->orderByDesc('id');

        $filename = "expenses_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

            fputcsv($handle, ['ID', 'الوصف', 'المبلغ', 'العملة', 'التصنيف', 'النوع', 'المشروع', 'الحملة', 'المسكن', 'التاريخ']);
            $query->chunk(100, function ($expenses) use ($handle) {
                foreach ($expenses as $e) {
                    fputcsv($handle, [
                        $e->id,
                        $e->description ?? '—',
                        $e->amount,
                        $e->currency,
                        $e->category ?? '—',
                        $e->type,
                        $e->project->name ?? '—',
                        $e->campaign->name ?? '—',
                        $e->workspace->name ?? '—',
                        $e->created_at->format('Y-m-d')
                    ]);
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create()
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        // Fetch projects - you might want to remove the specific where clause if you want all projects
        $projects = Project::query()
            ->orderBy('name')
            ->get();
            
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $workspaces = Workspace::where('status', '!=', 'maintenance')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();
        
        // Fetch active treasuries for expenses
        $treasuries = \App\Models\Treasury::active()->forDepartment('expenses')->get();

        return view('expenses.create', compact('beneficiaries', 'projects', 'campaigns', 'workspaces', 'guestHouses', 'treasuries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:operational,aid,logistics',
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
            'currency' => 'nullable|string',
            'treasury_id' => 'required|exists:treasuries,id',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'workspace_id' => 'nullable|exists:workspaces,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'paid_at' => 'nullable|date'
        ]);

        if (empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        // Use ChangeRequestService
        $executor = function () use ($data, $request) {
            DB::beginTransaction();
            try {
                // Create Expense
                $exp = \App\Models\Expense::create($data);

                // Process with Treasury
                $treasuryService = new \App\Services\TreasuryIntegrationService();
                $treasuryService->processExpenseFromTreasury($exp, $data['treasury_id']);

                DB::commit();
                return $exp;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        };

        try {
            $result = \App\Services\ChangeRequestService::handleRequest(
                \App\Models\Expense::class,
                null,
                'create',
                $data,
                $executor
            );

            if ($result instanceof \App\Models\ChangeRequest) {
                return redirect()->route('expenses.index')->with('success', 'تم إرسال طلب إضافة المصروف للموافقة.');
            }

            return redirect()->route('expenses.show', $result)
                ->with('success', 'تم تسجيل المصروف وخصمه من الخزينة بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ المصروف: ' . $e->getMessage());
        }
    }

    public function show(Expense $expense)
    {
        $pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Expense::class)
            ->where('model_id', $expense->id)
            ->where('status', 'pending')
            ->first();
        return view('expenses.show', compact('expense', 'pendingRequest'));
    }

    public function edit(Expense $expense)
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $workspaces = Workspace::where('status', '!=', 'maintenance')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'beneficiaries', 'projects', 'campaigns', 'workspaces', 'guestHouses'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:operational,aid,logistics',
            'category' => 'nullable|string|max:100',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'workspace_id' => 'nullable|exists:workspaces,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'paid_at' => 'nullable|date'
        ]);
        $executor = function () use ($expense, $data) {
            $expense->update($data);
            return $expense;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Expense::class,
            $expense->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المصروف للموافقة.');
        }

        return redirect()->route('expenses.show', $expense)->with('success', 'تم تعديل المصروف بنجاح.');
    }

    public function destroy(Expense $expense)
    {
        $reason = request('reason', 'إلغاء يدوي من قبل المدير');

        $executor = function () use ($expense, $reason) {
            DB::beginTransaction();
            try {
                // 1. Update Status
                $expense->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'cancellation_reason' => $reason
                ]);

                // 2. Reverse Treasury Transaction
                $treasuryService = new \App\Services\TreasuryIntegrationService();
                $treasuryService->cancelExpenseTransaction($expense, $reason);

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Expense::class,
            $expense->id,
            'cancel',
            ['reason' => $reason],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء المصروف للموافقة.');
        }

        return redirect()->route('expenses.index')->with('success', 'تم إلغاء المصروف وعكس العملية المالية بنجاح.');
    }
}
