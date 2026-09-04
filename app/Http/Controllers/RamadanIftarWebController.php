<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RamadanIftar;
use App\Models\Campaign;
use App\Models\Project;
use App\Services\RamadanIftarService;
use App\Http\Requests\StoreRamadanIftarRequest;
use App\Http\Requests\UpdateRamadanIftarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RamadanIftarWebController extends Controller
{
    public function __construct(
        private RamadanIftarService $iftarService
    ) {}

    public function index(Request $request): View
    {
        $filters    = $request->only(['q']);
        $iftars     = $this->iftarService->getFilteredIftars($filters, 50);
        $statistics = $this->iftarService->getStatistics();

        return view('ramadan_iftars.index', compact('iftars', 'statistics'));
    }

    public function create(): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        return view('ramadan_iftars.create', compact('campaigns', 'projects'));
    }

    public function store(StoreRamadanIftarRequest $request): RedirectResponse
    {
        $this->iftarService->createIftar($request->validated());
        return redirect()->route('ramadan-iftars.index')->with('success', 'تم تسجيل بيانات الإفطار بنجاح.');
    }

    public function edit(RamadanIftar $ramadan_iftar): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        return view('ramadan_iftars.edit', compact('ramadan_iftar', 'campaigns', 'projects'));
    }

    public function update(UpdateRamadanIftarRequest $request, RamadanIftar $ramadan_iftar): RedirectResponse
    {
        $this->iftarService->updateIftar($ramadan_iftar, $request->validated());
        return redirect()->route('ramadan-iftars.index')->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy(RamadanIftar $ramadan_iftar): RedirectResponse
    {
        $this->iftarService->deleteIftar($ramadan_iftar);
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}
