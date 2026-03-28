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

final readonly class FinancialClosureWebController extends Controller
{
    public function __construct(
        private FinancialClosureService $financialClosureService
    ) {}

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
        $userId = $request->user()?->id;
        $data   = $request->validated();
        
        $shouldApproveImmediately = (isset($data['approved']) && $data['approved']) && 
                                    ($request->user()->hasRole('admin') || $request->user()->hasRole('manager'));

        $result = $this->financialClosureService->createClosure($data, $userId, $shouldApproveImmediately);

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('closures.index')->with('success', 'تم إرسال طلب إنشاء الإغلاق المالي للمراجعة');
        }

        return redirect()->route('closures.index')->with('success', 'تم إنشاء الإغلاق المالي بنجاح');
    }

    public function approve(FinancialClosure $closure): RedirectResponse
    {
        $this->financialClosureService->approveClosure($closure, (int)auth()->id());
        
        $newState = $closure->fresh()->approved;
        return redirect()->route('closures.index')->with('success', $newState ? 'تم اعتماد الإغلاق' : 'تم إلغاء الاعتماد');
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
