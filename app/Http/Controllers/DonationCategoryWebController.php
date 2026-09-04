<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class DonationCategoryWebController extends Controller
{
    public function index()
    {
        $categories = DonationCategory::with('items')->orderBy('sort_order')->get();
        return view('donation-settings.categories.index', compact('categories'));
    }

    public function unified()
    {
        $categories = DonationCategory::with('items')->orderBy('sort_order')->get();
        return view('donation-settings.unified', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        DonationCategory::create([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name'], '_'),
            'sort_order' => $data['sort_order'] ?? 0,
            'status'     => $request->boolean('status', true),
        ]);

        return back()->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function update(Request $request, DonationCategory $donationCategory)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $donationCategory->update([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name'], '_'),
            'sort_order' => $data['sort_order'] ?? $donationCategory->sort_order,
            'status'     => $request->boolean('status'),
        ]);

        return back()->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(DonationCategory $donationCategory)
    {
        $donationCategory->delete();
        return back()->with('success', 'تم حذف التصنيف بنجاح');
    }

    public function toggleStatus(DonationCategory $donationCategory)
    {
        $donationCategory->update(['status' => !$donationCategory->status]);
        return back()->with('success', 'تم تغيير حالة التصنيف');
    }
}
