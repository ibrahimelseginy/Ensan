<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Donation;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\ChangeRequest;
use App\Models\Delegate;
use App\Models\TravelRoute;
use App\Models\GuestHouse;
use App\Services\InventoryTransactionService;
use App\Http\Requests\StoreInventoryTransactionRequest;
use App\Http\Requests\UpdateInventoryTransactionRequest;
use App\Http\Requests\StoreInventoryTransferRequest;
use App\Http\Requests\StoreInventoryReconcileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

final readonly class InventoryTransactionWebController extends Controller
{
    public function __construct(
        private InventoryTransactionService $transactionService
    ) {}

    public function index(Request $request): View
    {
        $filters      = $request->only(['type', 'warehouse_id', 'item_id', 'date_from', 'date_to']);
        $transactions = $this->transactionService->getFilteredTransactions($filters, 20);

        // Stats Logic
        $today = now()->toDateString();
        $stats = $this->transactionService->getGlobalStats();

        // Delegate stats (preserving existing complex query logic for now)
        $delegateDaily = DB::table('donations')
            ->select('delegate_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereNotNull('delegate_id')
            ->whereDate('created_at', $today)
            ->groupBy('delegate_id')->get();
            
        $delegateMonthly = DB::table('donations')
            ->select('delegate_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereNotNull('delegate_id')
            ->whereBetween('created_at', [now()->startOfMonth(), now()])
            ->groupBy('delegate_id')->get();
            
        $delegatesMap = Delegate::whereIn('id', 
            collect($delegateDaily)->pluck('delegate_id')
                ->merge(collect($delegateMonthly)->pluck('delegate_id'))
                ->unique()
                ->filter()
        )->get()->keyBy('id');

        $stats['delegateDaily']   = $delegateDaily;
        $stats['delegateMonthly'] = $delegateMonthly;
        $stats['delegatesMap']     = $delegatesMap;
        
        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');
        $items      = Item::orderBy('name')->pluck('name', 'id');

        return view('inventory.index', compact('transactions', 'stats', 'warehouses', 'items'));
    }

    public function create(): View
    {
        $items         = Item::orderBy('name')->get();
        $warehouses    = Warehouse::orderBy('name')->get();
        $donations     = Donation::with(['donor', 'delegate', 'route'])->orderByDesc('id')->limit(200)->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        
        $projects = Project::query()
            ->where(fn($q) => $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%')
            )
            ->orderBy('name')->get();

        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $routes      = TravelRoute::orderBy('name')->get();
        $delegates   = Delegate::orderBy('name')->get();
        $guestHouses = GuestHouse::where(fn($q) => $q->where('location', 'like', '%كفر%')
                ->orWhere('location', 'like', '%طنطا%')
                ->orWhere('name', 'like', '%كفر%')
                ->orWhere('name', 'like', '%طنطا%')
            )
            ->orderBy('name')->get();

        return view('inventory.create', compact('items', 'warehouses', 'donations', 'beneficiaries', 'projects', 'campaigns', 'routes', 'delegates', 'guestHouses'));
    }

    public function store(StoreInventoryTransactionRequest $request): RedirectResponse
    {
        try {
            $result = $this->transactionService->createTransaction($request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحركة للموافقة');
            }

            return redirect()->route('inventory-transactions.index')->with('success', 'تم إضافة الحركة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(InventoryTransaction $inventory_transaction): View|RedirectResponse
    {
        if ($this->hasPendingRequest($inventory_transaction)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        return view('inventory.show', ['t' => $inventory_transaction->load(['item', 'warehouse', 'beneficiary', 'project', 'campaign', 'sourceDonation'])]);
    }

    public function edit(InventoryTransaction $inventory_transaction): View|RedirectResponse
    {
        if ($this->hasPendingRequest($inventory_transaction)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $items         = Item::orderBy('name')->get();
        $warehouses    = Warehouse::orderBy('name')->get();
        $donations     = Donation::where('type', 'in_kind')->orderByDesc('id')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        
        $projects = Project::query()
            ->where(fn($q) => $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%')
            )
            ->orderBy('name')->get();

        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();

        return view('inventory.edit', [
            't'             => $inventory_transaction,
            'items'         => $items,
            'warehouses'    => $warehouses,
            'donations'     => $donations,
            'beneficiaries' => $beneficiaries,
            'projects'      => $projects,
            'campaigns'     => $campaigns
        ]);
    }

    public function update(UpdateInventoryTransactionRequest $request, InventoryTransaction $inventory_transaction): RedirectResponse
    {
        if ($this->hasPendingRequest($inventory_transaction)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $result = $this->transactionService->updateTransaction($inventory_transaction, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('inventory-transactions.index')->with('success', 'تم إرسال طلب تعديل الحركة للمراجعة');
        }

        return redirect()->route('inventory-transactions.show', $inventory_transaction)->with('success', 'تم تعديل الحركة بنجاح');
    }

    public function destroy(InventoryTransaction $inventory_transaction): RedirectResponse
    {
        if ($this->hasPendingRequest($inventory_transaction)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $result = $this->transactionService->deleteTransaction($inventory_transaction);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('inventory-transactions.index')->with('success', 'تم إرسال طلب حذف الحركة للمراجعة');
        }

        return redirect()->route('inventory-transactions.index')->with('success', 'تم حذف الحركة بنجاح');
    }

    public function createTransfer(): View
    {
        $items     = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        
        return view('inventory.transfer', compact('items', 'warehouses', 'projects', 'campaigns'));
    }

    public function storeTransfer(StoreInventoryTransferRequest $request): RedirectResponse
    {
        try {
            $this->transactionService->createTransfer($request->validated());
            return redirect()->route('inventory-transactions.index')->with('success', 'تم تحويل المخزون بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function createReconcile(): View
    {
        $items     = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        
        return view('inventory.reconcile', compact('items', 'warehouses', 'projects', 'campaigns'));
    }

    public function storeReconcile(StoreInventoryReconcileRequest $request): RedirectResponse
    {
        try {
            $this->transactionService->createReconcile($request->validated());
            return redirect()->route('inventory-transactions.index')->with('success', 'تم حفظ تسوية الجرد بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    private function hasPendingRequest(InventoryTransaction $transaction): bool
    {
        return ChangeRequest::where('model_type', InventoryTransaction::class)
            ->where('model_id', $transaction->id)
            ->where('status', 'pending')
            ->exists();
    }
}
