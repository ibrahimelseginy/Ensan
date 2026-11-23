<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\LeaveService;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LeaveWebController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    public function index(Request $request): View
    {
        $user    = request()->user();
        $filters = $request->only(['status', 'type']);

        if (!$user || !$user->hasPermission('leaves.manage')) {
            $filters['user_id'] = (int)$user?->id;
        }

        $leaves = $this->leaveService->getFilteredLeaves($filters, 20);

        return view('leaves.index', compact('leaves'));
    }

    public function create(): View
    {
        $user = request()->user();
        $employees = ($user && $user->hasPermission('leaves.manage'))
            ? User::where('is_employee', true)->orderBy('name')->get()
            : collect();

        return view('leaves.create', compact('employees'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $user = request()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $validated = $request->validated();
        $targetUserId = ($user->hasPermission('leaves.manage') && !empty($validated['user_id']))
            ? (int) $validated['user_id']
            : (int) $user->id;

        if ($user->hasPermission('leaves.manage') && !empty($validated['status'])) {
            if ($validated['status'] === 'approved') {
                $validated['approved_by'] = $user->id;
            }
        } else {
            $validated['status'] = 'pending';
        }

        $result = $this->leaveService->createLeave($validated, $targetUserId);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('leaves.index')->with('success', 'تم إرسال طلب الإجازة للمراجعة');
        }

        return redirect()->route('leaves.index')->with('success', 'تم تسجيل طلب الإجازة بنجاح');
    }

    public function approve(Leave $leave): RedirectResponse
    {
        $user = request()->user();
        if (!$user || !$user->hasPermission('leaves.manage')) {
            abort(403, 'غير مصرح لك باعتماد الإجازات');
        }

        if ($leave->status === 'approved') {
            return back()->with('info', 'الطلب معتمد بالفعل');
        }

        $leave->update([
            'status'      => 'approved',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', "تمت الموافقة على طلب إجازة {$leave->user?->name} ({$leave->days} يوم) وخصمها من الرصيد بنجاح.");
    }

    public function reject(Request $request, Leave $leave): RedirectResponse
    {
        $user = request()->user();
        if (!$user || !$user->hasPermission('leaves.manage')) {
            abort(403, 'غير مصرح لك برفض الإجازات');
        }

        $reason = $request->input('rejection_reason') ?: 'تم الرفض بواسطة الموارد البشرية';

        $leave->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return back()->with('success', "تم رفض طلب الإجازة بنجاح.");
    }

    public function show(Leave $leave): View
    {
        return view('leaves.show', compact('leave'));
    }

    public function edit(Leave $leave): View
    {
        $user = request()->user();
        if (!$user) {
            abort(401);
        }

        if ($leave->user_id !== $user->id && !$user->hasPermission('leaves.manage')) {
            abort(403);
        }

        return view('leaves.edit', compact('leave'));
    }

    public function update(UpdateLeaveRequest $request, Leave $leave): RedirectResponse
    {
        $user = request()->user();
        if (!$user) {
            abort(401);
        }

        if ($leave->user_id !== $user->id && !$user->hasPermission('leaves.manage')) {
            abort(403);
        }

        $isManager = $user->hasPermission('leaves.manage');
        $result    = $this->leaveService->updateLeave($leave, $request->validated(), $isManager);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب التعديل للموافقة');
        }

        return redirect()->route('leaves.index')->with('success', 'تم تحديث الإجازة');
    }

    public function destroy(Leave $leave): RedirectResponse
    {
        $user = request()->user();
        if (!$user) {
            abort(401);
        }

        if ($leave->user_id !== $user->id && !$user->hasPermission('leaves.manage')) {
            abort(403);
        }

        $result = $this->leaveService->deleteLeave($leave);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء الإجازة للموافقة');
        }

        return redirect()->route('leaves.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:leaves,id'
        ]);

        $user = request()->user();
        if (!$user) {
            abort(401);
        }

        $isManager = $user->hasPermission('leaves.manage');
        $count     = $this->leaveService->bulkDelete($request->input('ids'), (int)$user->id, $isManager);

        return back()->with('success', "تم إرسال طلبات الحذف لـ $count من الطلبات للموافقة");
    }
}
