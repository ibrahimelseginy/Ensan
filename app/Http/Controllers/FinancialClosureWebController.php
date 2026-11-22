<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FinancialClosure;
use App\Services\FinancialClosureService;
use App\Http\Requests\StoreFinancialClosureRequest;
use App\Http\Requests\UpdateFinancialClosureRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

final class FinancialClosureWebController extends Controller
{
    public function __construct(
        private FinancialClosureService $financialClosureService
    ) {}

    private function canUserApproveClosure($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasRole(['admin', 'manager', 'super_admin', 'مدير المؤسسة بالكامل', 'حسابات ماليه', 'حسابات', 'fr', 'accountant', 'finance'])
            || $user->hasPermission('financial_closures.approve')
            || $user->hasPermission('closures.approve')
            || $user->hasPermission('تعديل واعتماد الإغلاقات المالية')
            || in_array((int)$user->id, [2, 6, 8, 50], true);
    }

    public function index(): View
    {
        $closures = $this->financialClosureService->getAllClosures(20);
        $pendingRequests = \App\Models\ChangeRequest::where('model_type', FinancialClosure::class)
            ->where('status', 'pending')
            ->get()
            ->groupBy('model_id');
            
        return view('closures.index', compact('closures', 'pendingRequests'));
    }

    public function create(): View
    {
        return view('closures.create');
    }

    public function store(StoreFinancialClosureRequest $request): RedirectResponse
    {
        $user   = $request->user();
        $userId = $user?->id;
        $data   = $request->validated();
        
        $canApprove = $this->canUserApproveClosure($user);

        // الحسابات والإدارة: يتم اعتماد الإغلاق اليومي فوراً ومباشرة
        $shouldApproveImmediately = $canApprove && (!isset($data['approved']) || (bool)$data['approved']);

        $result = $this->financialClosureService->createClosure($data, $userId, $shouldApproveImmediately);

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب إنشاء الإغلاق المالي للمراجعة');
        }

        return redirect()->route('closures.index')->with('success', 'تم إنشاء واعتماد الإغلاق المالي بنجاح');
    }

    public function approve(FinancialClosure $closure): RedirectResponse
    {
        $user = auth()->user();
        if (!$this->canUserApproveClosure($user)) {
            abort(403, 'غير مصرح لك باعتماد الإغلاق المالي.');
        }

        $this->financialClosureService->approveClosure($closure, (int)$user->id);
        
        $newState = $closure->fresh()->approved;
        return redirect()->route('closures.index')->with('success', $newState ? 'تم اعتماد الإغلاق المالي بنجاح' : 'تم إلغاء الاعتماد');
    }

    public function show(FinancialClosure $closure): View
    {
        return view('closures.show', compact('closure'));
    }

    public function edit(FinancialClosure $closure): View
    {
        return view('closures.edit', compact('closure'));
    }

    public function update(UpdateFinancialClosureRequest $request, FinancialClosure $closure): RedirectResponse
    {
        $result = $this->financialClosureService->updateClosure($closure, $request->validated());

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب تعديل الإغلاق المالي للموافقة');
        }

        return redirect()->route('closures.index')->with('success', 'تم تحديث الإغلاق المالي بنجاح');
    }

    public function destroy(FinancialClosure $closure): RedirectResponse
    {
        $result = $this->financialClosureService->deleteClosure($closure);

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب حذف الإغلاق المالي للموافقة');
        }

        return redirect()->route('closures.index')->with('success', 'تم حذف الإغلاق المالي');
    }
}
