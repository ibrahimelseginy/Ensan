<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FinancialClosure;
use App\Services\FinancialClosureService;
use App\Http\Requests\StoreFinancialClosureRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class FinancialClosureController extends Controller
{
    public function __construct(
        private FinancialClosureService $financialClosureService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->financialClosureService->getAllClosures(20);
    }

    public function store(StoreFinancialClosureRequest $request): mixed
    {
        $userId = $request->user()?->id;
        // API often approves immediately or follows specific rules.
        // For simplicity, let's keep the core closure creation.
        return $this->financialClosureService->createClosure($request->validated(), $userId, true);
    }

    public function approve(Request $request, FinancialClosure $closure): FinancialClosure
    {
        $userId = (int)($request->user()?->id ?? 0);
        $this->financialClosureService->approveClosure($closure, $userId);
        
        return $closure->fresh();
    }
}
