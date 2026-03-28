<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

final class LeaveWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'changeRequests' => function($q) {
            $q->where('status', 'pending');
        }])->orderByDesc('start_date');

        $user = request()->user();
        if (!$user || !$user->hasPermission('leaves.manage')) {
            $query->where('user_id', request()->user()->id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $leaves = $query->paginate(20);
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:annual,sick,unpaid,emergency,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $user = request()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        // Integrate with ChangeRequest system
        $executor = function() use ($data) {
            return Leave::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Leave::class,
            null, // model_id is null for create
            'create',
            $data,
            $executor,
            true // Force Request so it shows in the approval list
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('leaves.index')->with('success', 'تم إرسال طلب الإجازة للمراجعة');
        }

        return redirect()->route('leaves.index')->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    public function edit(Leave $leave)
    {
        if ($leave->user_id !== request()->user()->id && !request()->user()->hasPermission('leaves.manage')) {
            abort(403);
        }
        return view('leaves.edit', compact('leave'));
    }

    public function show(Leave $leave)
    {
        /*
        if ($leave->user_id !== request()->user()->id && !request()->user()->hasPermission('leaves.manage')) {
            abort(403);
        }
        */
        // Assuming public view or minimal check for now, can refine later
        return view('leaves.show', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        // Permission check: Owners or Managers
        if ($leave->user_id !== request()->user()->id && !request()->user()->hasPermission('leaves.manage')) {
            abort(403);
        }

        // Validate
        $data = $request->validate([
            'type' => 'sometimes|in:annual,sick,unpaid,emergency,other',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'reason' => 'sometimes|string|max:500',
            'status' => 'sometimes|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string'
        ]);

        $executor = function () use ($leave, $data) {
            if (isset($data['status']) && $data['status'] === 'approved') {
                $data['approved_by'] = auth()->id();
            }
            $leave->update($data);
            return $leave;
        };

        // DECISION LOGIC: 
        // If it is a STATUS change (Approval/Rejection) by a Manager, execute immediately.
        if (request()->user()->hasPermission('leaves.manage') && isset($data['status']) && $leave->status == 'pending') {
             $executor();
             return redirect()->route('leaves.index')->with('success', 'تم تحديث حالة الطلب بنجاح');
        }

        // DATA CHANGE LOGIC (Edit):
        // For any data change (Dates, Type, Reason), OR if a user is trying to "Cancel" (which might be a status change to something else, or a delete request), 
        // OR if changing an already approved request -> Force Change Request.
        
        $action = 'update';
        // If regular user wants to cancel pending request? Usually they delete it.
        // If regular user wants to cancel APPROVED request? They need to request cancel.
        
        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Leave::class,
            $leave->id,
            $action,
            $data,
            $executor,
            true // Force Request for everyone (even admins editing data) to ensure audit trail
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب التعديل للموافقة');
        }

        return redirect()->route('leaves.index')->with('success', 'تم تحديث الإجازة');
    }

    public function destroy(Leave $leave)
    {
        // Permission check
        if ($leave->user_id !== request()->user()->id && !request()->user()->hasPermission('leaves.manage')) {
            abort(403);
        }

        $executor = function () use ($leave) {
            $leave->delete();
            return true;
        };

        // Always create a Change Request for deletion to allow for approval/review
        // This handles "Request Cancellation" logic effectively.
        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Leave::class,
            $leave->id,
            'delete',
            ['note' => 'طلب حذف/إلغاء إجازة'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء الإجازة للموافقة');
        }

        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'تم حذف الطلب بنجاح');
    }
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:leaves,id'
        ]);

        $ids = $request->input('ids');
        $count = 0;

        foreach ($ids as $id) {
            $leave = Leave::find($id);
            if (!$leave) continue;
            
            // Basic Permission Logic: Must be owner or manager
            if ($leave->user_id !== request()->user()->id && !request()->user()->hasPermission('leaves.manage')) {
                continue; 
            }

            $executor = function () use ($leave) {
                $leave->delete();
                return true;
            };

            \App\Services\ChangeRequestService::handleRequest(
                \App\Models\Leave::class,
                $leave->id,
                'delete',
                ['note' => 'حذف جماعي لطلبات الإجازة'],
                $executor,
                true // Force Request
            );
            $count++;
        }

        return back()->with('success', "تم إرسال طلبات الحذف لـ $count من الطلبات للموافقة");
    }
}
