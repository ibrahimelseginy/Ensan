<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CharityGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CharityGuideWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = CharityGuide::query()->orderBy('center')->orderBy('village');

        if ($request->filled('center')) {
            $query->where('center', $request->center);
        }

        if ($request->filled('search')) {
            $s = trim((string)$request->search);
            $query->where(function ($q) use ($s) {
                $q->where('village', 'like', "%{$s}%")
                  ->orWhere('coordinator_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $guides = $query->paginate(25)->withQueryString();

        $centers = CharityGuide::select('center')->distinct()->pluck('center')->filter()->values();
        $totalGuides = CharityGuide::count();
        $totalCenters = $centers->count();
        $totalVillages = CharityGuide::select('village')->distinct()->count();

        return view('charity_guides.index', compact('guides', 'centers', 'totalGuides', 'totalCenters', 'totalVillages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'governorate' => 'nullable|string|max:100',
            'center' => 'required|string|max:100',
            'village' => 'required|string|max:150',
            'coordinator_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'secondary_phone' => 'nullable|string|max:50',
            'exhibition_amount' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        CharityGuide::create($validated);

        return redirect()->route('charity-guides.index')->with('success', 'تم إضافة دليل العمل الخيري بنجاح.');
    }

    public function update(Request $request, CharityGuide $charityGuide): RedirectResponse
    {
        $validated = $request->validate([
            'governorate' => 'nullable|string|max:100',
            'center' => 'required|string|max:100',
            'village' => 'required|string|max:150',
            'coordinator_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'secondary_phone' => 'nullable|string|max:50',
            'exhibition_amount' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $charityGuide->update($validated);

        return redirect()->route('charity-guides.index')->with('success', 'تم تحديث بيانات الدليل بنجاح.');
    }

    public function destroy(CharityGuide $charityGuide): RedirectResponse
    {
        $charityGuide->delete();
        return redirect()->route('charity-guides.index')->with('success', 'تم حذف الدليل بنجاح.');
    }
}

