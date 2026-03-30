<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\HrEvaluationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HrEvaluationWebController extends Controller
{
    public function __construct(
        private HrEvaluationService $hrEvaluationService
    ) {}

    public function index(): View
    {
        $stats = $this->hrEvaluationService->getGlobalEvaluationStats();
        return view('hr.evaluations', $stats);
    }
}
