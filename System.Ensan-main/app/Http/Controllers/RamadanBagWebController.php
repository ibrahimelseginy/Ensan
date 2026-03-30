<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RamadanBag;
use App\Models\Campaign;
use App\Models\Project;
use App\Services\RamadanBagService;
use App\Http\Requests\StoreRamadanBagRequest;
use App\Http\Requests\UpdateRamadanBagRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RamadanBagWebController extends Controller
{
    public function __construct(
        private RamadanBagService $bagService
    ) {}

    public function index(Request $request): View
    {
        $filters    = $request->only(['q', 'status']);
        $bags       = $this->bagService->getFilteredBags($filters, 50);
        $statistics = $this->bagService->getStatistics();

        return view('ramadan_bags.index', compact('bags', 'statistics'));
    }

    public function create(): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        return view('ramadan_bags.create', compact('campaigns', 'projects'));
    }

    public function store(StoreRamadanBagRequest $request): RedirectResponse
    {
        $this->bagService->createBag($request->validated());
        return redirect()->route('ramadan-bags.index')->with('success', 'تم تسجيل بيانات الشنطة بنجاح.');
    }

    public function edit(RamadanBag $ramadan_bag): View
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        return view('ramadan_bags.edit', compact('ramadan_bag', 'campaigns', 'projects'));
    }

    public function update(UpdateRamadanBagRequest $request, RamadanBag $ramadan_bag): RedirectResponse
    {
        $this->bagService->updateBag($ramadan_bag, $request->validated());
        return redirect()->route('ramadan-bags.index')->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy(RamadanBag $ramadan_bag): RedirectResponse
    {
        $this->bagService->deleteBag($ramadan_bag);
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}
