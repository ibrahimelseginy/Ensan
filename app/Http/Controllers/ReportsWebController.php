<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\GuestHouse;
use App\Models\GuestHouseWing;
use App\Models\GuestHouseBed;
use App\Models\GuestHouseCustody;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Task;
use App\Models\VolunteerHour;
use App\Models\VolunteerAttendance;
use App\Models\Complaint;
use App\Models\User;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportsWebController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        // If print requested via query param ?print=1
        if ($request->boolean('print') || $request->query('mode') === 'print') {
            return view('reports.print', $data);
        }

        return view('reports.index', $data);
    }

    public function printReport(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('reports.print', $data);
    }

    public function export(Request $request): StreamedResponse
    {
        $tab = $request->input('tab', 'donations');
        $range = $request->input('range', 'all');
        $filename = "ensan-report-{$tab}-" . now()->format('Y-m-d') . ".csv";

        $data = $this->buildReportData($request);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tab, $data) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($tab === 'donations') {
                fputcsv($handle, ['رقم الإيصال', 'المتبرع', 'الهاتف', 'النوع', 'المبلغ / القيمة', 'الغرض', 'طريقة الدفع', 'المشروع / الحملة', 'التاريخ', 'الحالة']);
                foreach ($data['recentDonations'] as $d) {
                    fputcsv($handle, [
                        $d->receipt_number ?? ('#' . $d->id),
                        $d->donor->name ?? 'فاعل خير',
                        $d->donor->phone ?? '-',
                        $d->type === 'cash' ? 'نقدي' : 'عيني',
                        $d->type === 'cash' ? (float)$d->amount : (float)$d->estimated_value,
                        $d->purpose_label ?? $d->category ?? 'عام',
                        $d->payment_method_label ?? $d->payment_method ?? 'نقدي',
                        $d->campaign->name ?? $d->project->name ?? 'عام',
                        $d->received_at ? $d->received_at->format('Y-m-d') : ($d->created_at ? $d->created_at->format('Y-m-d') : '-'),
                        $d->status ?? 'مكتمل'
                    ]);
                }
            } elseif ($tab === 'beneficiaries') {
                fputcsv($handle, ['كود المستفيد', 'الاسم بالكامل', 'الرقم القومي', 'الهاتف', 'المحافظة / العنوان', 'نوع المساعدة', 'مبلغ الكفالة', 'دار الضيافة', 'الحالة', 'تاريخ التسجيل']);
                foreach ($data['recentBeneficiaries'] as $b) {
                    fputcsv($handle, [
                        $b->code ?? ('#' . $b->id),
                        $b->full_name,
                        $b->national_id ?? '-',
                        $b->phone ?? '-',
                        $b->address ?? '-',
                        $b->assistance_type ?? 'عام',
                        (float)($b->monthly_sponsorship_amount ?? 0),
                        $b->guestHouse->name ?? '-',
                        $b->status ?? 'نشط',
                        $b->created_at ? $b->created_at->format('Y-m-d') : '-'
                    ]);
                }
            } elseif ($tab === 'guest_houses') {
                fputcsv($handle, ['دار الضيافة', 'المحافظة', 'السعة الإجمالية', 'عدد الأجنحة', 'إجمالي الأسرة', 'الأسرة المحجوزة', 'الأسرة الشاغرة', 'نسبة الإشغال %', 'الحالة']);
                foreach ($data['guestHousesList'] as $gh) {
                    fputcsv($handle, [
                        $gh->name,
                        $gh->governorate ?? 'غير محدد',
                        $gh->capacity ?? 0,
                        $gh->wings_count ?? 0,
                        $gh->beds_count ?? 0,
                        $gh->occupied_beds ?? 0,
                        $gh->available_beds ?? 0,
                        $gh->occupancy_rate . '%',
                        $gh->status === 'active' ? 'نشط' : 'مؤرشف'
                    ]);
                }
            } elseif ($tab === 'inventory') {
                fputcsv($handle, ['كود الصنف', 'اسم الصنف', 'الوحدة', 'إجمالي الوارد', 'إجمالي المنصرف', 'الرصيد الحالي', 'حالة المخزون']);
                foreach ($data['itemsStockList'] as $item) {
                    fputcsv($handle, [
                        $item->sku ?? ('#' . $item->id),
                        $item->name,
                        $item->unit ?? 'قطعة',
                        (float)$item->total_in,
                        (float)$item->total_out,
                        (float)$item->net_qty,
                        $item->net_qty <= 0 ? 'منخفض / نافذ' : 'متوفر'
                    ]);
                }
            } elseif ($tab === 'finance') {
                fputcsv($handle, ['رقم السند', 'البيان', 'النوع', 'المبلغ', 'التاريخ', 'الحساب / الجهة']);
                foreach ($data['recentExpenses'] as $ex) {
                    fputcsv($handle, [
                        '#' . $ex->id,
                        $ex->description ?? 'مصروف تشغيلي',
                        $ex->type ?? 'عام',
                        (float)$ex->amount,
                        $ex->paid_at ? $ex->paid_at->format('Y-m-d') : ($ex->created_at ? $ex->created_at->format('Y-m-d') : '-'),
                        $ex->guestHouse->name ?? $ex->campaign->name ?? $ex->project->name ?? 'المركز الرئيسي'
                    ]);
                }
            } else {
                fputcsv($handle, ['المؤشر', 'القيمة']);
                foreach ($data['kpis'] as $k => $v) {
                    fputcsv($handle, [$k, is_numeric($v) ? (float)$v : $v]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function buildReportData(Request $request): array
    {
        $tab = $request->input('tab', 'all');
        $range = $request->input('range', 'all');
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $governorate = $request->input('governorate', 'all');
        $q = trim((string)$request->input('q', ''));

        // Handle predefined ranges
        if ($range === 'today') {
            $from = now()->startOfDay()->toDateString();
            $to = now()->endOfDay()->toDateString();
        } elseif ($range === 'this_week') {
            $from = now()->startOfWeek()->toDateString();
            $to = now()->endOfWeek()->toDateString();
        } elseif ($range === 'this_month') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        } elseif ($range === 'this_year') {
            $from = now()->startOfYear()->toDateString();
            $to = now()->endOfYear()->toDateString();
        }

        $applyDateFilter = function ($query, string $column = 'created_at') use ($from, $to) {
            if (!empty($from)) {
                $query->whereDate($column, '>=', $from);
            }
            if (!empty($to)) {
                $query->whereDate($column, '<=', $to);
            }
            return $query;
        };

        // 1. DONATIONS & DONORS
        $donationsBase = Donation::query()->where('status', '!=', 'cancelled');
        $applyDateFilter($donationsBase, 'created_at');
        if (!empty($q)) {
            $donationsBase->where(function ($w) use ($q) {
                $w->where('receipt_number', 'like', "%{$q}%")
                  ->orWhere('purpose', 'like', "%{$q}%")
                  ->orWhereHas('donor', fn($d) => $d->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
            });
        }

        $donationsCash = (float) (clone $donationsBase)->where('type', 'cash')->sum('amount');
        $donationsInKind = (float) (clone $donationsBase)->where('type', 'in_kind')->sum('estimated_value');
        $donationsTotalAmount = $donationsCash + $donationsInKind;
        $donationsCount = (int) (clone $donationsBase)->count();

        $donationsByMethod = (clone $donationsBase)->where('type', 'cash')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $donationsByPurpose = (clone $donationsBase)
            ->select('purpose', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->groupBy('purpose')
            ->get();

        $recentDonations = (clone $donationsBase)->with(['donor', 'project', 'campaign', 'guestHouse', 'delegate'])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $donorsTotal = Donor::count();
        $donorsRecurring = Donor::where('classification', 'recurring')->count();
        $topDonors = Donor::withCount(['donations' => fn($d) => $d->where('status', '!=', 'cancelled')])
            ->withSum(['donations' => fn($d) => $d->where('status', '!=', 'cancelled')], 'amount')
            ->orderByDesc('donations_sum_amount')
            ->limit(10)
            ->get();

        // 2. BENEFICIARIES & AID
        $beneficiariesBase = Beneficiary::query();
        $applyDateFilter($beneficiariesBase, 'created_at');
        if ($governorate !== 'all' && !empty($governorate)) {
            $beneficiariesBase->where(function ($b) use ($governorate) {
                $b->where('address', 'like', "%{$governorate}%")
                  ->orWhereHas('guestHouse', fn($g) => $g->where('governorate', 'like', "%{$governorate}%"));
            });
        }
        if (!empty($q)) {
            $beneficiariesBase->where(function ($w) use ($q) {
                $w->where('full_name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('national_id', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $beneficiariesTotal = (int) (clone $beneficiariesBase)->count();
        $beneficiariesByStatus = (clone $beneficiariesBase)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $beneficiariesByAssistance = (clone $beneficiariesBase)
            ->select('assistance_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('assistance_type')
            ->groupBy('assistance_type')
            ->get();

        $recentBeneficiaries = (clone $beneficiariesBase)->with(['guestHouse', 'project', 'campaign'])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $totalSponsorshipAmount = (float) Beneficiary::where('status', 'approved')->sum('monthly_sponsorship_amount');

        // 3. GUEST HOUSES & BEDS
        $guestHouses = GuestHouse::with(['wings.beds', 'beneficiaries', 'manager'])->get();
        $guestHousesList = $guestHouses->map(function ($gh) {
            $totalBeds = $gh->wings->flatMap->beds->count();
            $occupied = $gh->wings->flatMap->beds->where('status', 'occupied')->count();
            $available = $gh->wings->flatMap->beds->where('status', 'available')->count();
            $rate = $totalBeds > 0 ? round(($occupied / $totalBeds) * 100, 1) : 0;
            $gh->wings_count = $gh->wings->count();
            $gh->beds_count = $totalBeds;
            $gh->occupied_beds = $occupied;
            $gh->available_beds = $available;
            $gh->occupancy_rate = $rate;
            return $gh;
        });

        $totalGuestHouses = $guestHousesList->count();
        $totalWings = GuestHouseWing::count();
        $totalBeds = GuestHouseBed::count();
        $occupiedBeds = GuestHouseBed::where('status', 'occupied')->count();
        $availableBeds = GuestHouseBed::where('status', 'available')->count();
        $overallOccupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

        $recentGuestHousePatients = Beneficiary::whereNotNull('guest_house_id')
            ->with('guestHouse')
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $recentCustodies = GuestHouseCustody::with(['guestHouse', 'warehouse', 'treasury'])
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        // 4. INVENTORY & WAREHOUSES
        $warehousesCount = Warehouse::count();
        $itemsCount = Item::count();

        $invInQuery = InventoryTransaction::where('type', 'in');
        $invOutQuery = InventoryTransaction::where('type', 'out');
        $applyDateFilter($invInQuery, 'transaction_date');
        $applyDateFilter($invOutQuery, 'transaction_date');

        $totalInQty = (float) $invInQuery->sum('quantity');
        $totalOutQty = (float) $invOutQuery->sum('quantity');
        $netInventoryQty = $totalInQty - $totalOutQty;

        $itemsStockList = Item::select('items.id', 'items.name', 'items.sku', 'items.unit')
            ->selectRaw("COALESCE(SUM(CASE WHEN it.type = 'in' THEN it.quantity ELSE 0 END), 0) as total_in")
            ->selectRaw("COALESCE(SUM(CASE WHEN it.type = 'out' THEN it.quantity ELSE 0 END), 0) as total_out")
            ->selectRaw("COALESCE(SUM(CASE WHEN it.type = 'in' THEN it.quantity WHEN it.type = 'out' THEN -it.quantity ELSE 0 END), 0) as net_qty")
            ->leftJoin('inventory_transactions as it', 'it.item_id', '=', 'items.id')
            ->groupBy('items.id', 'items.name', 'items.sku', 'items.unit')
            ->orderBy('net_qty')
            ->limit(30)
            ->get();

        $lowStockCount = $itemsStockList->where('net_qty', '<=', 0)->count();

        $recentInventoryTx = InventoryTransaction::with(['item', 'warehouse', 'user'])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        // 5. FINANCE & EXPENSES
        $expensesBase = Expense::query()->where('status', '!=', 'cancelled');
        $applyDateFilter($expensesBase, 'created_at');
        if (!empty($q)) {
            $expensesBase->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            });
        }

        $totalExpenses = (float) (clone $expensesBase)->sum('amount');
        $expensesByType = (clone $expensesBase)
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get();

        $expensesByCategory = (clone $expensesBase)
            ->select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $recentExpenses = (clone $expensesBase)->with(['project', 'campaign', 'guestHouse', 'beneficiary'])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $journalEntriesCount = JournalEntry::count();
        $journalLineTotals = JournalEntryLine::select(DB::raw('SUM(debit) as debit'), DB::raw('SUM(credit) as credit'))->first();
        $totalDebit = (float) ($journalLineTotals->debit ?? 0);
        $totalCredit = (float) ($journalLineTotals->credit ?? 0);

        $netFinancialResult = $donationsCash - $totalExpenses;

        // 6. PROJECTS & CAMPAIGNS
        $projectsList = Project::withCount('donations')->get();
        $campaignsList = Campaign::withCount('donations')->get();
        $projectsCount = $projectsList->count();
        $campaignsCount = $campaignsList->count();

        $totalCampaignsGoal = (float) $campaignsList->sum('goal_amount');
        $totalCampaignsCollected = (float) $campaignsList->sum('current_amount');
        $overallCampaignProgress = $totalCampaignsGoal > 0 ? round(($totalCampaignsCollected / $totalCampaignsGoal) * 100, 1) : 0;

        // 7. VOLUNTEERS & HR
        $volunteersCount = User::whereHas('roles', fn($q) => $q->where('key', 'volunteer'))->count();
        $volunteerHoursTotal = (float) VolunteerHour::sum('hours');
        $volunteerAttendanceCount = VolunteerAttendance::count();

        $tasksTotal = Task::count();
        $tasksCompleted = Task::where('status', 'done')->count();
        $tasksInProgress = Task::where('status', 'in_progress')->count();
        $tasksPending = Task::where('status', 'pending')->count();
        $recentTasks = Task::with(['assignee', 'assigner', 'project', 'campaign'])->orderByDesc('id')->limit(15)->get();

        // 8. COMPLAINTS & FEEDBACK
        $complaintsTotal = Complaint::count();
        $complaintsOpen = Complaint::where('status', 'open')->count();
        $complaintsInProgress = Complaint::where('status', 'in_progress')->count();
        $complaintsClosed = Complaint::where('status', 'closed')->count();
        $recentComplaints = Complaint::orderByDesc('id')->limit(15)->get();

        // MASTER EXECUTIVE KPIS
        $kpis = [
            'إجمالي التبرعات المحصلة' => number_format($donationsTotalAmount, 2) . ' ج.م',
            'التبرعات النقدية' => number_format($donationsCash, 2) . ' ج.م',
            'التبرعات العينية' => number_format($donationsInKind, 2) . ' ج.م',
            'إجمالي عدد التبرعات' => $donationsCount,
            'إجمالي المتبرعين' => $donorsTotal,
            'المتبرعين الدائمين' => $donorsRecurring,
            'إجمالي المستفيدين المخدومين' => $beneficiariesTotal,
            'إجمالي دور الضيافة' => $totalGuestHouses,
            'إجمالي الأسرة بدار الضيافة' => $totalBeds,
            'الأسرة المشغولة حالياً' => $occupiedBeds,
            'نسبة إشغال دار الضيافة' => $overallOccupancyRate . '%',
            'رصيد المخزون الصافي' => number_format($netInventoryQty, 1) . ' وحدة',
            'إجمالي المصروفات' => number_format($totalExpenses, 2) . ' ج.م',
            'صافي الفائض / التدفق المالي' => number_format($netFinancialResult, 2) . ' ج.م',
            'الحملات والمشاريع النشطة' => $campaignsCount + $projectsCount,
            'نسبة إنجاز الحملات' => $overallCampaignProgress . '%',
            'إجمالي المتطوعين' => $volunteersCount,
            'المهام المنجزة' => $tasksCompleted . ' من ' . $tasksTotal,
            'إجمالي الشكاوى الواردة' => $complaintsTotal,
            'الشكاوى المنجزة والمغلقة' => $complaintsClosed,
        ];

        return compact(
            'tab',
            'range',
            'from',
            'to',
            'governorate',
            'q',
            'kpis',
            // Donations
            'donationsCash',
            'donationsInKind',
            'donationsTotalAmount',
            'donationsCount',
            'donationsByMethod',
            'donationsByPurpose',
            'recentDonations',
            'donorsTotal',
            'donorsRecurring',
            'topDonors',
            // Beneficiaries
            'beneficiariesTotal',
            'beneficiariesByStatus',
            'beneficiariesByAssistance',
            'recentBeneficiaries',
            'totalSponsorshipAmount',
            // Guest Houses
            'guestHousesList',
            'totalGuestHouses',
            'totalWings',
            'totalBeds',
            'occupiedBeds',
            'availableBeds',
            'overallOccupancyRate',
            'recentGuestHousePatients',
            'recentCustodies',
            // Inventory
            'warehousesCount',
            'itemsCount',
            'totalInQty',
            'totalOutQty',
            'netInventoryQty',
            'itemsStockList',
            'lowStockCount',
            'recentInventoryTx',
            // Finance
            'totalExpenses',
            'expensesByType',
            'expensesByCategory',
            'recentExpenses',
            'journalEntriesCount',
            'totalDebit',
            'totalCredit',
            'netFinancialResult',
            // Campaigns & Projects
            'projectsList',
            'campaignsList',
            'projectsCount',
            'campaignsCount',
            'totalCampaignsGoal',
            'totalCampaignsCollected',
            'overallCampaignProgress',
            // Volunteers & HR
            'volunteersCount',
            'volunteerHoursTotal',
            'volunteerAttendanceCount',
            'tasksTotal',
            'tasksCompleted',
            'tasksInProgress',
            'tasksPending',
            'recentTasks',
            // Complaints
            'complaintsTotal',
            'complaintsOpen',
            'complaintsInProgress',
            'complaintsClosed',
            'recentComplaints'
        );
    }
}
