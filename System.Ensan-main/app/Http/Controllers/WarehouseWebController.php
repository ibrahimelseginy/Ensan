<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class WarehouseWebController extends Controller
{
    public function index() { 
        $warehouses = Warehouse::withCount('transactions')->orderBy('name')->paginate(20);
        
        // Add pending check for each warehouse
        $warehouses->each(function($w) {
            $w->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Warehouse::class)
                ->where('model_id', $w->id)
                ->where('status', 'pending')
                ->first();
        });
        
        $stats = [
            'total' => Warehouse::count(),
            'total_transactions' => InventoryTransaction::count(),
            'active_items' => InventoryTransaction::distinct('item_id')->count('item_id')
        ];

        return view('warehouses.index', compact('warehouses', 'stats')); 
    }
    public function create() { return view('warehouses.create'); }
    public function store(Request $request)
    { 
        $data = $request->validate(['name' => 'required|string','location' => 'nullable|string']); 
        
        $executor = function () use ($data) {
             return Warehouse::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Warehouse::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المخزن للمواقفة.');
        }

        return redirect()->route('warehouses.show', $result); 
    }
    public function show(Warehouse $warehouse) { 
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Warehouse::class)
            ->where('model_id', $warehouse->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }
        $stock = InventoryTransaction::where('warehouse_id', $warehouse->id)
            ->select('item_id', DB::raw("SUM(CASE WHEN type = 'in' THEN quantity WHEN type = 'out' THEN -quantity ELSE 0 END) as current_stock"))
            ->groupBy('item_id')
            ->having('current_stock', '>', 0)
            ->with('item')
            ->get();

        // Recent transactions
        $recent_transactions = $warehouse->transactions()
            ->with(['item']) 
            ->latest()
            ->take(10)
            ->get();

        return view('warehouses.show', compact('warehouse', 'stock', 'recent_transactions')); 
    }
    public function edit(Warehouse $warehouse) { 
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Warehouse::class)
            ->where('model_id', $warehouse->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        return view('warehouses.edit', compact('warehouse')); 
    }
    public function update(Request $request, Warehouse $warehouse)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Warehouse::class)
            ->where('model_id', $warehouse->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        $data = $request->validate(['name' => 'sometimes|string', 'location' => 'nullable|string']);

        $executor = function () use ($warehouse, $data) {
            $warehouse->update($data);
            return $warehouse;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Warehouse::class,
            $warehouse->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المخزن للموافقة');
        }

        return redirect()->route('warehouses.show', $warehouse)->with('success', 'تم تحديث المخزن بنجاح');
    }
    public function destroy(Warehouse $warehouse)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Warehouse::class)
            ->where('model_id', $warehouse->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        $executor = function () use ($warehouse) {
            $warehouse->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Warehouse::class,
            $warehouse->id,
            'delete',
            ['note' => 'حذف مخزن'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المخزن للموافقة');
        }

        return redirect()->route('warehouses.index')->with('success', 'تم حذف المخزن بنجاح');
    }
}
