<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use App\Models\DonationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class DonationItemWebController extends Controller
{
    public function index()
    {
        $items      = DonationItem::with('category')->orderBy('sort_order')->get();
        $categories = DonationCategory::active()->orderBy('sort_order')->get();
        return view('donation-settings.items.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:donation_categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|any_image|max:2048',
            'image'       => 'nullable|any_image|max:4096',
            'sort_order'  => 'nullable|integer',
            'bg_style'    => 'nullable|string|in:light,dark',
        ]);

        $iconPath  = $request->hasFile('icon')  ? $request->file('icon')->store('donation-items/icons', 'public')  : null;
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('donation-items/images', 'public') : null;

        DonationItem::create([
            'category_id' => $data['category_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'icon'        => $iconPath,
            'image'       => $imagePath,
            'sort_order'  => $data['sort_order'] ?? 0,
            'status'      => $request->boolean('status', true),
            'bg_style'    => $data['bg_style'] ?? 'light',
        ]);

        return back()->with('success', 'تم إضافة العنصر بنجاح');
    }

    public function update(Request $request, DonationItem $donationItem)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:donation_categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|any_image|max:2048',
            'image'       => 'nullable|any_image|max:4096',
            'sort_order'  => 'nullable|integer',
            'bg_style'    => 'nullable|string|in:light,dark',
        ]);

        $iconPath  = $donationItem->icon;
        $imagePath = $donationItem->image;

        if ($request->hasFile('icon')) {
            if ($iconPath) Storage::disk('public')->delete($iconPath);
            $iconPath = $request->file('icon')->store('donation-items/icons', 'public');
        }
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('donation-items/images', 'public');
        }

        $donationItem->update([
            'category_id' => $data['category_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'icon'        => $iconPath,
            'image'       => $imagePath,
            'sort_order'  => $data['sort_order'] ?? $donationItem->sort_order,
            'status'      => $request->boolean('status'),
            'bg_style'    => $data['bg_style'] ?? $donationItem->bg_style,
        ]);

        return back()->with('success', 'تم تحديث العنصر بنجاح');
    }

    public function destroy(DonationItem $donationItem)
    {
        if ($donationItem->icon)  Storage::disk('public')->delete($donationItem->icon);
        if ($donationItem->image) Storage::disk('public')->delete($donationItem->image);
        $donationItem->delete();
        return back()->with('success', 'تم حذف العنصر بنجاح');
    }

    public function toggleStatus(DonationItem $donationItem)
    {
        $donationItem->update(['status' => !$donationItem->status]);
        return back()->with('success', 'تم تغيير الحالة');
    }
}
