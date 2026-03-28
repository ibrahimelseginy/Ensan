<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ItemWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->withCount('transactions')->orderBy('name')->paginate(20);

        // Add pending check for each item
        $items->each(function($i) {
            $i->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Item::class)
                ->where('model_id', $i->id)
                ->where('status', 'pending')
                ->first();
        });

        $stats = [
            'total' => Item::count(),
            'with_value' => Item::whereNotNull('estimated_value')->where('estimated_value', '>', 0)->count(),
        ];

        return view('items.index', compact('items', 'stats'));
    }
    public function create()
    {
        return view('items.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'nullable|string',
            'name' => 'required|string',
            'unit' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
            'original_price' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|between:0,100'
        ]);
        $executor = function () use ($data) {
             return Item::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Item::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الصنف للموافقة.');
        }

        return redirect()->route('items.show', $result);
    }

    public function show(Item $item)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        // Stock by Warehouse
        $stock_by_warehouse = InventoryTransaction::where('item_id', $item->id)
            ->select('warehouse_id', DB::raw("SUM(CASE WHEN type = 'in' THEN quantity WHEN type = 'out' THEN -quantity ELSE 0 END) as current_stock"))
            ->groupBy('warehouse_id')
            ->having('current_stock', '>', 0)
            ->with('warehouse')
            ->get();

        // Recent Transactions
        $recent_transactions = $item->transactions()
            ->with(['warehouse'])
            ->latest()
            ->take(10)
            ->get();

        return view('items.show', compact('item', 'stock_by_warehouse', 'recent_transactions'));
    }

    public function update(Request $request, Item $item)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'sku' => 'nullable|string',
            'name' => 'required|string',
            'unit' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
            'original_price' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|between:0,100',
            'category' => 'nullable|string',
            'min_stock_level' => 'nullable|integer',
            'max_stock_level' => 'nullable|integer',
            'barcode' => 'nullable|string'
        ]);

        $executor = function() use ($item, $data) {
            $item->update($data);
            return $item;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Item::class,
            $item->id,
            'update',
            $data,
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تحديث الصنف للمراجعة');
        }

        return redirect()->route('items.index')->with('success', 'تم تحديث بيانات الصنف');
    }

    public function edit(Item $item)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        return view('items.edit', compact('item'));
    }
    public function destroy(Item $item)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        $executor = function () use ($item) {
            $item->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Item::class,
            $item->id,
            'delete',
            ['note' => 'حذف صنف'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الصنف للموافقة');
        }

        return redirect()->route('items.index')->with('success', 'تم حذف الصنف بنجاح');
    }
}
