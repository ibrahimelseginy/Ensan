<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Collaboration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CollaborationController extends Controller
{
    public const CATEGORIES = [
        'معامل تحاليل ومراكز أشعة',
        'عيادات وأطباء',
        'صيدليات',
        'مستشفيات ومراكز طبية',
        'أكاديميات ومراكز تعليمية',
        'مكتبات ومطابع',
        'جيمات ونوادي رياضية',
        'تجار جملة',
        'شركات تكنولوجيا',
        'أخرى',
    ];

    public function index(Request $request): View
    {
        $query = Collaboration::with('creator')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%")
                  ->orWhere('service_provided', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $collaborations = $query->paginate(24)->withQueryString();

        $stats = [
            'total' => Collaboration::count(),
            'active' => Collaboration::whereIn('status', ['active', 'extended'])->count(),
            'medical' => Collaboration::whereIn('category', [
                'معامل تحاليل ومراكز أشعة',
                'عيادات وأطباء',
                'صيدليات',
                'مستشفيات ومراكز طبية',
            ])->count(),
            'categories_count' => Collaboration::distinct('category')->count('category'),
        ];

        $categories = self::CATEGORIES;

        return view('collaborations.index', compact('collaborations', 'stats', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'category' => 'required|string|max:100',
            'specialty' => 'nullable|string|max:150',
            'service_provided' => 'nullable|string',
            'discount_volunteers' => 'nullable|string|max:191',
            'activation_volunteers' => 'nullable|string|max:191',
            'discount_beneficiaries' => 'nullable|string|max:191',
            'activation_beneficiaries' => 'nullable|string|max:191',
            'target_audience' => 'nullable|string|max:191',
            'working_hours' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'phone_alt' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:191',
            'contact_person_phone' => 'nullable|string|max:50',
            'contact_person_role' => 'nullable|string|max:100',
            'partnership_source' => 'nullable|string|max:191',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|in:active,extended,expired,pending',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['rating'] = $validated['rating'] ?? 5;

        // Process doc links if present
        if ($request->filled('doc_link_url')) {
            $validated['doc_links'] = [
                [
                    'title' => $request->input('doc_link_title', 'رابط الوثيقة / الشيت'),
                    'url' => $request->input('doc_link_url'),
                ]
            ];
        }

        Collaboration::create($validated);

        return back()->with('success', 'تمت إضافة الشراكة / التعاون بنجاح.');
    }

    public function update(Request $request, Collaboration $collaboration): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'category' => 'required|string|max:100',
            'specialty' => 'nullable|string|max:150',
            'service_provided' => 'nullable|string',
            'discount_volunteers' => 'nullable|string|max:191',
            'activation_volunteers' => 'nullable|string|max:191',
            'discount_beneficiaries' => 'nullable|string|max:191',
            'activation_beneficiaries' => 'nullable|string|max:191',
            'target_audience' => 'nullable|string|max:191',
            'working_hours' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'phone_alt' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:191',
            'contact_person_phone' => 'nullable|string|max:50',
            'contact_person_role' => 'nullable|string|max:100',
            'partnership_source' => 'nullable|string|max:191',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|in:active,extended,expired,pending',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('doc_link_url')) {
            $validated['doc_links'] = [
                [
                    'title' => $request->input('doc_link_title', 'رابط الوثيقة / الشيت'),
                    'url' => $request->input('doc_link_url'),
                ]
            ];
        }

        $collaboration->update($validated);

        return back()->with('success', 'تم تحديث بيانات الشراكة / التعاون بنجاح.');
    }

    public function destroy(Collaboration $collaboration): RedirectResponse
    {
        $collaboration->delete();
        return back()->with('success', 'تم حذف التعاون بنجاح.');
    }
}
