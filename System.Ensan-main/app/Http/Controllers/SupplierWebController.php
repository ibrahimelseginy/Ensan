<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('phone', 'like', '%' . $request->q . '%');
        }
        $suppliers = $query->latest()->paginate(20);

        // Add pending check for each supplier
        $suppliers->each(function ($s) {
            $s->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Supplier::class)
                ->where('model_id', $s->id)
                ->where('status', 'pending')
                ->first();
        });

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'merchant_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'source_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'website' => 'nullable|url|max:255'
        ]);

        $executor = function () use ($data) {
            return Supplier::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Supplier::class ,
            null,
            'create',
            $data,
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب إضافة المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show(Supplier $supplier)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Supplier::class)
            ->where('model_id', $supplier->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        $purchases = $supplier->purchases()->latest('purchase_date')->paginate(20);
        return view('suppliers.show', compact('supplier', 'purchases'));
    }

    public function edit(Supplier $supplier)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Supplier::class)
            ->where('model_id', $supplier->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'merchant_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'source_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'website' => 'nullable|url|max:255'
        ]);

        $executor = function () use ($supplier, $data) {
            $supplier->update($data);
            return $supplier;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Supplier::class ,
            $supplier->id,
            'update',
            $data,
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب تحديث بيانات المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد');
    }

    public function destroy(Supplier $supplier)
    {
        $executor = function () use ($supplier) {
            $supplier->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Supplier::class ,
            $supplier->id,
            'delete',
        ['name' => $supplier->name],
            $executor,
            true // Force Request logic
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب حذف المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد');
    }
}
