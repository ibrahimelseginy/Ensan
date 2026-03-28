<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\FinancialClosure;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

final class FinancialClosureWebController extends Controller
{
    public function index()
    {
        $closures = FinancialClosure::orderByDesc('date')->paginate(20);
        $pendingRequests = \App\Models\ChangeRequest::where('model_type', FinancialClosure::class)
            ->where('status', 'pending')
            ->get()
            ->groupBy('model_id');
            
        return view('closures.index', compact('closures', 'pendingRequests'));
    }
    public function create()
    {
        return view('closures.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string',
            'approved' => 'nullable|boolean'
        ]);

        $shouldApproveImmediately = (isset($data['approved']) && $data['approved']) && 
                                    (request()->user()->hasRole('admin') || request()->user()->hasRole('manager'));

        $executor = function () use ($data, $shouldApproveImmediately) {
            $finalData = $data;
            if ($shouldApproveImmediately) {
                $finalData['approved'] = true;
                $finalData['approved_by'] = request()->user()->id;
            } else {
                $finalData['approved'] = false;
            }
            
            $closure = FinancialClosure::create($finalData);
            
            // Only lock journal entries if approved
            if ($closure->approved) {
                JournalEntry::where('date', '<=', $data['date'])->when($data['branch'] ?? null, function ($q, $b) {
                    $q->where('branch', $b);
                })->update(['locked' => true]);
            }
            
            return $closure;
        };

        // If NOT shouldApproveImmediately, we force a change request
        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\FinancialClosure::class,
            null,
            'create',
            $data,
            $executor,
            !$shouldApproveImmediately // Force request if not immediately approved
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب إنشاء الإغلاق المالي للمراجعة');
        }

        return redirect()->route('closures.index')->with('success', 'تم إنشاء الإغلاق المالي بنجاح');
    }
    public function approve(FinancialClosure $closure)
    {
        $newState = !$closure->approved;
        $closure->update([
            'approved' => $newState,
            'approved_by' => $newState ? request()->user()->id : null
        ]);
        return redirect()->route('closures.index')->with('success', $newState ? 'تم اعتماد الإغلاق' : 'تم إلغاء الاعتماد');
    }

    public function show(FinancialClosure $closure)
    {
        return view('closures.show', compact('closure'));
    }

    public function edit(FinancialClosure $closure)
    {
        return view('closures.edit', compact('closure'));
    }

    public function update(Request $request, FinancialClosure $closure)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string',
            'approved' => 'nullable|boolean'
        ]);

        if (isset($data['approved']) && $data['approved']) {
            if (request()->user()->hasRole('admin') || request()->user()->hasRole('manager')) {
                $data['approved'] = true;
                $data['approved_by'] = request()->user()->id;
            } else {
                // Keep original or fail? Usually, if not authorized, just don't change or keep false.
                // Let's keep it as it was if not authorized, but the simplest is to ignore the input.
                $data['approved'] = $closure->approved;
            }
        } else {
            // Only admins/managers can un-approve too? 
            if (request()->user()->hasRole('admin') || request()->user()->hasRole('manager')) {
                $data['approved'] = false;
                $data['approved_by'] = null;
            } else {
                $data['approved'] = $closure->approved;
            }
        }

        $executor = function () use ($closure, $data) {
            $closure->update($data);
            return $closure;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\FinancialClosure::class,
            $closure->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب تعديل الإغلاق المالي للموافقة');
        }

        return redirect()->route('closures.index')->with('success', 'تم تحديث الإغلاق المالي بنجاح');
    }

    public function destroy(FinancialClosure $closure)
    {
        $executor = function () use ($closure) {
            $closure->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\FinancialClosure::class,
            $closure->id,
            'delete',
            ['note' => 'حذف إغلاق مالي'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب حذف الإغلاق المالي للموافقة');
        }

        $closure->delete();
        return redirect()->route('closures.index')->with('success', 'تم حذف الإغلاق المالي');
    }
}
