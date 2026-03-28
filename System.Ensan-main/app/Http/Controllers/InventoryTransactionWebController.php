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
use Illuminate\Http\Request;

final class InventoryTransactionWebController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with([
                'item','warehouse','beneficiary','project','campaign',
                'sourceDonation.donor','sourceDonation.delegate','sourceDonation.route'
            ]);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderByDesc('id')->paginate(20);

        // Add pending check for each transaction
        $transactions->each(function($t) {
            $t->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\InventoryTransaction::class)
                ->where('model_id', $t->id)
                ->where('status', 'pending')
                ->first();
        });

        // Stats (Preserving existing logic but ensuring safety)
        $today = now()->toDateString();
        $tripsToday = (int) InventoryTransaction::whereDate('created_at',$today)->count();
        
        // Value calculation (linked to donations)
        $valueToday = (float) \DB::table('inventory_transactions as it')
            ->join('donations as d','d.id','=','it.source_donation_id')
            ->whereDate('it.created_at',$today)
            ->selectRaw('COALESCE(SUM(COALESCE(d.amount, d.estimated_value, 0)),0) as total')
            ->value('total');

        $delegateDaily = \DB::table('donations')
            ->select('delegate_id', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereNotNull('delegate_id')
            ->whereDate('created_at',$today)
            ->groupBy('delegate_id')->get();
            
        $delegateMonthly = \DB::table('donations')
            ->select('delegate_id', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->whereNotNull('delegate_id')
            ->whereBetween('created_at',[now()->startOfMonth(), now()])
            ->groupBy('delegate_id')->get();
            
        $delegatesMap = \App\Models\Delegate::whereIn('id', collect($delegateDaily)->pluck('delegate_id')->merge(collect($delegateMonthly)->pluck('delegate_id'))->unique()->filter())->get()->keyBy('id');
        
        $stats = ['date'=>$today,'trips_today'=>$tripsToday,'value_today'=>$valueToday,'delegateDaily'=>$delegateDaily,'delegateMonthly'=>$delegateMonthly,'delegatesMap'=>$delegatesMap];
        
        // Data for filters
        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');
        $items = Item::orderBy('name')->pluck('name', 'id');

        return view('inventory.index', compact('transactions','stats', 'warehouses', 'items'));
    }
    public function create()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $donations = Donation::with(['donor','delegate','route'])->orderByDesc('id')->limit(200)->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $projects = Project::query()
            ->where(function($q){
                $q->where('name','like','%بعثاء%')
                  ->orWhere('name','like','%زاد%')
                  ->orWhere('name','like','%مدرار%')
                  ->orWhere('name','like','%كسو%');
            })
            ->orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $routes = \App\Models\TravelRoute::orderBy('name')->get();
        $delegates = \App\Models\Delegate::orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::where(function($q){
            $q->where('location','like','%كفر%')
              ->orWhere('location','like','%طنطا%')
              ->orWhere('name','like','%كفر%')
              ->orWhere('name','like','%طنطا%');
        })->orderBy('name')->get();
        return view('inventory.create', compact('items','warehouses','donations','beneficiaries','projects','campaigns','routes','delegates','guestHouses'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:in,transfer,out',
            'quantity' => 'required|numeric|min:0.01',
            'source_donation_id' => 'nullable|exists:donations,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'reference' => 'nullable|string'
        ]);

        // Stock Validation
        if (in_array($data['type'], ['out', 'transfer'])) {
            $currentStock = InventoryTransaction::where('item_id', $data['item_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->selectRaw("SUM(CASE WHEN type = 'in' THEN quantity WHEN type IN ('out', 'transfer') THEN -quantity ELSE 0 END) as stock")
                ->value('stock') ?? 0;

            if ($data['quantity'] > $currentStock) {
                return back()->withErrors(['quantity' => "الكمية غير متوفرة. الرصيد الحالي: " . number_format($currentStock, 2)])->withInput();
            }
        }

        $executor = function () use ($data) {
            return InventoryTransaction::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\InventoryTransaction::class,
            null,
            'create',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحركة للموافقة');
        }

        return redirect()->route('inventory-transactions.index')->with('success', 'تم إضافة الحركة بنجاح');
    }
    public function show(InventoryTransaction $inventory_transaction)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\InventoryTransaction::class)
            ->where('model_id', $inventory_transaction->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $inventory_transaction->load(['item','warehouse','beneficiary','project','campaign','sourceDonation']);
        return view('inventory.show', ['t' => $inventory_transaction]);
    }
    public function edit(InventoryTransaction $inventory_transaction)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\InventoryTransaction::class)
            ->where('model_id', $inventory_transaction->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $donations = Donation::where('type','in_kind')->orderByDesc('id')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $projects = Project::query()
            ->where(function($q){
                $q->where('name','like','%بعثاء%')
                  ->orWhere('name','like','%زاد%')
                  ->orWhere('name','like','%مدرار%')
                  ->orWhere('name','like','%كسو%');
            })
            ->orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        return view('inventory.edit', ['t' => $inventory_transaction, 'items' => $items, 'warehouses' => $warehouses, 'donations' => $donations, 'beneficiaries' => $beneficiaries, 'projects' => $projects, 'campaigns' => $campaigns]);
    }
    public function update(Request $request, InventoryTransaction $inventory_transaction)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\InventoryTransaction::class)
            ->where('model_id', $inventory_transaction->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'type' => 'sometimes|in:in,transfer,out',
            'quantity' => 'nullable|numeric|min:0.01',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'reference' => 'nullable|string'
        ]);

        $executor = function() use ($inventory_transaction, $data) {
            $inventory_transaction->update($data);
            return $inventory_transaction;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\InventoryTransaction::class,
            $inventory_transaction->id,
            'update',
            $data,
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('inventory-transactions.index')->with('success', 'تم إرسال طلب تعديل الحركة للمراجعة');
        }

        return redirect()->route('inventory-transactions.show', $inventory_transaction)->with('success', 'تم تعديل الحركة بنجاح');
    }
    public function destroy(InventoryTransaction $inventory_transaction)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\InventoryTransaction::class)
            ->where('model_id', $inventory_transaction->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الحركة لديها طلب مراجعة حالياً');
        }

        $executor = function() use ($inventory_transaction) {
            $inventory_transaction->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\InventoryTransaction::class,
            $inventory_transaction->id,
            'delete',
            ['note' => 'حذف حركة مخزون'],
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('inventory-transactions.index')->with('success', 'تم إرسال طلب حذف الحركة للمراجعة');
        }

        $inventory_transaction->delete();
        return redirect()->route('inventory-transactions.index');
    }

    public function createTransfer()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        
        return view('inventory.transfer', compact('items','warehouses','projects','campaigns'));
    }

    public function storeTransfer(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'notes' => 'nullable|string',
            'date' => 'nullable|date'
        ]);

        // Check stock in source warehouse
        $currentStock = InventoryTransaction::where('item_id', $data['item_id'])
            ->where('warehouse_id', $data['from_warehouse_id'])
            ->selectRaw("SUM(CASE 
                WHEN type IN ('in', 'stock_count_increase', 'transfer_in') THEN quantity 
                WHEN type IN ('out', 'transfer', 'transfer_out', 'stock_count_shortage') THEN -quantity 
                ELSE 0 END) as stock")
            ->value('stock') ?? 0;

        if ($data['quantity'] > $currentStock) {
            return back()->withErrors(['quantity' => "الكمية غير متوفرة في المخزن المصدر. الرصيد الحالي: " . number_format($currentStock, 2)])->withInput();
        }

        \DB::transaction(function() use ($data) {
            $date = $data['date'] ?? now();
            
            // 1. Create OUT transaction (Source)
            $out = InventoryTransaction::create([
                'item_id' => $data['item_id'],
                'warehouse_id' => $data['from_warehouse_id'],
                'type' => 'transfer_out',
                'quantity' => $data['quantity'],
                'project_id' => $data['project_id'],
                'campaign_id' => $data['campaign_id'],
                'notes' => $data['notes'],
                'created_at' => $date,
                'updated_at' => $date
            ]);

            // 2. Create IN transaction (Destination)
            $in = InventoryTransaction::create([
                'item_id' => $data['item_id'],
                'warehouse_id' => $data['to_warehouse_id'],
                'type' => 'transfer_in',
                'quantity' => $data['quantity'],
                'project_id' => $data['project_id'],
                'campaign_id' => $data['campaign_id'],
                'notes' => $data['notes'],
                'related_transaction_id' => $out->id, // Link to the OUT transaction
                'created_at' => $date,
                'updated_at' => $date
            ]);

            // Update OUT to link to IN
            $out->update(['related_transaction_id' => $in->id]);
        });

        return redirect()->route('inventory-transactions.index')->with('success', 'تم تحويل المخزون بنجاح');
    }

    public function createReconcile()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        
        return view('inventory.reconcile', compact('items','warehouses','projects','campaigns'));
    }

    public function storeReconcile(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:stock_count_shortage,stock_count_increase,reconciliation',
            'quantity' => 'required|numeric|min:0.01',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'notes' => 'required|string',
            'date' => 'nullable|date'
        ]);

        // If shortage, check if we have enough stock
        if ($data['type'] == 'stock_count_shortage') {
            $currentStock = InventoryTransaction::where('item_id', $data['item_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->selectRaw("SUM(CASE 
                    WHEN type IN ('in', 'stock_count_increase', 'transfer_in') THEN quantity 
                    WHEN type IN ('out', 'transfer', 'transfer_out', 'stock_count_shortage') THEN -quantity 
                    ELSE 0 END) as stock")
                ->value('stock') ?? 0;

            if ($data['quantity'] > $currentStock) {
                return back()->withErrors(['quantity' => "لا يمكن تسجيل عجز أكبر من الرصيد الحالي ($currentStock)."])->withInput();
            }
        }

        $date = $data['date'] ?? now();

        InventoryTransaction::create([
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'project_id' => $data['project_id'],
            'campaign_id' => $data['campaign_id'],
            'notes' => $data['notes'],
            'created_at' => $date,
            'updated_at' => $date
        ]);

        return redirect()->route('inventory-transactions.index')->with('success', 'تم حفظ تسوية الجرد بنجاح');
    }
}
