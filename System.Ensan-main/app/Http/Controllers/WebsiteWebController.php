<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebBoardMember;
use App\Models\WebPartner;
use App\Models\WebNews;
use App\Models\WebRoomBooking;
use App\Models\WebVolunteerRequest;
use App\Models\WebContactMessage;
use App\Models\WebPage;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\WebTestimonial;
use App\Models\WebBranch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class WebsiteWebController extends Controller
{
    // --- Dynamic Pages (API & Management) ---
    public function pages()
    {
        $pages = WebPage::orderBy('sort_order')->get();
        return view('website.pages', compact('pages'));
    }

    public function pageStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:web_pages,slug',
            'content' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'sort_order' => 'integer',
            'is_published' => 'boolean'
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure unique slug if auto-generated
        $originalSlug = $data['slug'];
        $count = 1;
        while (WebPage::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count++;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/pages');
        }

        $data['is_published'] = $request->has('is_published'); // Checkbox handling

        WebPage::create($data);
        return back()->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    public function pageUpdate(Request $request, WebPage $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:web_pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($page->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/pages');
        }

        $data['is_published'] = $request->has('is_published');

        $page->update($data);
        return back()->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function pageDestroy(WebPage $page)
    {
        if ($page->image_path)
            Storage::disk('public')->delete($page->image_path);
        $page->delete();
        return back()->with('success', 'تم حذف الصفحة بنجاح');
    }

    // --- Public API for Pages ---
    public function apiGetPages()
    {
        $pages = WebPage::where('is_published', true)->orderBy('sort_order')->select('id', 'title', 'slug', 'image_path', 'updated_at')->get();
        return response()->json($pages);
    }

    public function apiGetPage($slug)
    {
        $page = WebPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return response()->json($page);
    }
    // --- Board Members ---
    public function boardMembers()
    {
        $members = WebBoardMember::orderBy('sort_order')->get();
        return view('website.board_members', compact('members'));
    }

    public function boardMemberStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'sort_order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/board');
        }

        WebBoardMember::create($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم إضافة عضو مجلس الأمناء بنجاح');
    }

    public function boardMemberUpdate(Request $request, WebBoardMember $member)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'sort_order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($member->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/board');
        }

        unset($data['image']);
        $member->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات العضو بنجاح');
    }

    public function boardMemberDestroy(WebBoardMember $member)
    {
        if ($member->image_path)
            Storage::disk('public')->delete($member->image_path);
        $member->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حذف العضو بنجاح');
    }

    // --- Partners & Supporters ---
    public function partners()
    {
        $partners = WebPartner::all();
        $leaders = \App\Models\WebVolunteerWall::orderBy('rank')->get();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key');
        return view('website.partners', compact('partners', 'leaders', 'settings'));
    }

    public function partnerStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:gold,platinum,silver,bronze,corporate',
            'logo' => 'nullable|any_image|max:5120',
            'website_url' => 'nullable|url'
        ]);

        if ($request->hasFile('logo')) {
            @ini_set('memory_limit', '512M');
            $data['logo_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('logo'), 'website/partners');
        }

        unset($data['logo']);
        WebPartner::create($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم إضافة الشريك/الداعم بنجاح');
    }

    public function partnerUpdate(Request $request, WebPartner $partner)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:gold,platinum,silver,bronze,corporate',
            'logo' => 'nullable|any_image|max:5120',
            'website_url' => 'nullable|url'
        ]);

        if ($request->hasFile('logo')) {
            @ini_set('memory_limit', '512M');
            app(\App\Services\ImageUploadService::class)->delete($partner->logo_path);
            $data['logo_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('logo'), 'website/partners');
        }

        unset($data['logo']);
        $partner->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات الشريك بنجاح');
    }

    public function partnerDestroy(WebPartner $partner)
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }
        $partner->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم الحذف بنجاح');
    }

    // --- News ---
    public function news()
    {
        $news = WebNews::orderByDesc('published_at')->get();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key');
        return view('website.news', compact('news', 'settings'));
    }

    public function newsStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'any_image|max:5120',
            'category' => 'nullable|string',
            'statistic_number' => 'nullable|string',
            'statistic_description' => 'nullable|string',
            'views_count' => 'nullable|string',
            'shares_count' => 'nullable|string',
            'published_at' => 'nullable|date',
            'contact_name' => 'nullable|string',
            'contact_number' => 'nullable|string'
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = app(\App\Services\ImageUploadService::class)->upload($image, 'website/news');
            }
            $data['images'] = $imagePaths;
            $data['image_path'] = $imagePaths[0] ?? null;
        }

        // Ensure non-nullable columns always have a value
        $data['views_count']  = $data['views_count']  ?? '0';
        $data['shares_count'] = $data['shares_count'] ?? '0';

        WebNews::create($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم نشر الخبر بنجاح');
    }

    public function newsUpdate(Request $request, WebNews $news)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'any_image|max:5120',
            'views_count' => 'nullable|string',
            'shares_count' => 'nullable|string',
            'statistic_number' => 'nullable|string',
            'statistic_description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'category' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'remove_images' => 'nullable|array'
        ]);

        $currentImages = $news->images ?? [];

        // Remove selected images
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $path) {
                app(\App\Services\ImageUploadService::class)->delete($path);
                $currentImages = array_filter($currentImages, fn($img) => $img !== $path);
            }
        }

        // Handle full image reset if requested (backward compatibility)
        if ($request->input('delete_image') == '1') {
            if ($news->image_path) {
                app(\App\Services\ImageUploadService::class)->delete($news->image_path);
            }
            $news->image_path = null;
            // Also clear images array if it's the only one
            if (count($currentImages) <= 1) {
                $currentImages = [];
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $currentImages[] = app(\App\Services\ImageUploadService::class)->upload($image, 'website/news');
            }
        }

        $data['images'] = array_values($currentImages);
        $data['image_path'] = $data['images'][0] ?? null;

        $news->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function newsDestroy(WebNews $news)
    {
        // Delete all associated images
        $allImages = $news->images ?? [];
        if ($news->image_path && !in_array($news->image_path, $allImages)) {
            $allImages[] = $news->image_path;
        }

        foreach ($allImages as $path) {
            app(\App\Services\ImageUploadService::class)->delete($path);
        }

        $news->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حذف الخبر بنجاح');
    }

    // --- Room Bookings ---
    public function bookings()
    {
        $bookings = WebRoomBooking::whereNull('source')->orWhere('source', '!=', 'mobile')->orderByDesc('created_at')->get();
        return view('website.bookings', compact('bookings'));
    }

    public function bookingUpdateStatus(Request $request, WebRoomBooking $booking)
    {
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة الحجز');
    }

    // --- Volunteer Requests & Content ---
    public function volunteerRequests()
    {
        $requests = WebVolunteerRequest::orderByDesc('created_at')->get();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key');
        return view('website.volunteer_requests', compact('requests', 'settings'));
    }

    public function updateVolunteerContent(Request $request)
    {
        $allowedKeys = [
            'volunteer_title', 'volunteer_description',
            'volunteer_stats_volunteers', 'volunteer_stats_hours', 'volunteer_stats_branches', 'volunteer_stats_projects'
        ];

        // 1. Handle Regular Text Settings
        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                \App\Models\WebSetting::set($key, $request->input($key));
            }
        }

        // 2. Handle Hero Image
        $heroKey = 'volunteer_hero_image';
        if ($request->hasFile($heroKey)) {
            $this->handleImageUpload($request, $heroKey, 'website/volunteer', 'volunteer');
        }
        elseif ($request->input("delete_{$heroKey}") == '1') {
            $oldPath = \App\Models\WebSetting::get($heroKey);
            app(\App\Services\ImageUploadService::class)->delete($oldPath);
            \App\Models\WebSetting::where('key', $heroKey)->delete();
        }

        // 3. Handle Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $key = "volunteer_slider_$i";
            if ($request->hasFile($key)) {
                $this->handleImageUpload($request, $key, 'website/volunteer', 'volunteer');
            }
            elseif ($request->input("delete_{$key}") == '1') {
                $oldPath = \App\Models\WebSetting::get($key);
                app(\App\Services\ImageUploadService::class)->delete($oldPath);
                \App\Models\WebSetting::where('key', $key)->delete();
            }
        }

        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث محتوى صفحة التطوع بنجاح');
    }

    public function updateVolunteerRequestStatus(Request $request, WebVolunteerRequest $volunteerRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:new,contacted,accepted,rejected'
        ]);

        $volunteerRequest->update($data);
        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function destroyVolunteerRequest(WebVolunteerRequest $volunteerRequest)
    {
        if ($volunteerRequest->cv_path) {
            Storage::disk('public')->delete($volunteerRequest->cv_path);
        }
        $volunteerRequest->delete();
        return back()->with('success', 'تم حذف طلب التطوع بنجاح');
    }

    public function downloadCV(WebVolunteerRequest $volunteerRequest)
    {
        $cvPath = $volunteerRequest->cv_path;
        if (!$cvPath) {
            return back()->with('error', 'لا توجد سيرة ذاتية مرفقة بهذا الطلب.');
        }

        // Clean up path variations
        if (str_starts_with($cvPath, 'http')) {
            $parsed = parse_url($cvPath);
            $cvPath = ltrim(str_replace('/storage/', '', $parsed['path']), '/');
        }
        else {
            $cvPath = ltrim($cvPath, '/');
            if (str_starts_with($cvPath, 'storage/')) {
                $cvPath = substr($cvPath, 8);
            }
        }

        $pathsToTry = [
            Storage::disk('public')->path($cvPath),
            Storage::disk('local')->path($cvPath),
            storage_path("app/public/" . $cvPath),
            storage_path("app/private/" . $cvPath),
            public_path("storage/" . $cvPath)
        ];

        foreach ($pathsToTry as $path) {
            if (file_exists($path)) {
                return response()->file($path);
            }
        }

        return back()->with('error', "ملف السيرة الذاتية غير موجود فعلياً على الخادم. تم حذفه مسبقاً من المجلد: " . basename($cvPath));
    }

    // --- Contact Messages ---
    public function contactMessages()
    {
        $messages = WebContactMessage::orderByDesc('created_at')->get();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        return view('website.contact_messages', compact('messages', 'settings'));
    }

    // --- Newsletter Subscriptions ---
    public function subscriptions()
    {
        $subscriptions = \App\Models\NewsletterSubscription::orderByDesc('created_at')->get();
        return view('website.subscriptions', compact('subscriptions'));
    }

    public function destroySubscription(\App\Models\NewsletterSubscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'تم حذف الاشتراك بنجاح');
    }


    public function contactMessageMarkRead(WebContactMessage $message)
    {
        $message->update(['read' => true]);
        return back()->with('success', 'تم تعليم الرسالة كمقروءة');
    }

    public function contactMessageDestroy(WebContactMessage $message)
    {
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }
        $message->delete();
        return back()->with('success', 'تم حذف الرسالة بنجاح');
    }

    // --- Projects & Campaigns Content ---
    public function content()
    {
        $projects = Project::all();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        return view('website.content', compact('projects', 'settings'));
    }

    public function campaignsContent()
    {
        $campaigns = Campaign::all();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        return view('website.campaigns_content', compact('campaigns', 'settings'));
    }

    public function updateProjectContent(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'sponsorship_details' => 'nullable|string',
            'image' => 'nullable|any_image|max:10240',
            'icon' => 'nullable|any_image|max:5120',
            'badge_icon_file' => 'nullable|any_image|max:2048',
            'action_icon_file' => 'nullable|any_image|max:2048',
            'short_description' => 'nullable|string',
            'action_text' => 'nullable|string',
            'action_url' => 'nullable|string',
            'ui_button_color' => 'nullable|string',
            'badge_text' => 'nullable|string',
            'badge_icon' => 'nullable|string',
            'action_icon' => 'nullable|string',
            'subcategory_text' => 'nullable|string',
            'is_visible' => 'nullable',
            'show_badge' => 'nullable',
            'show_subcategory' => 'nullable',
            'features' => 'nullable|array',
            'stats' => 'nullable|array',
            'theme_colors' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($project->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/projects');
        }
        if ($request->hasFile('icon')) {
            app(\App\Services\ImageUploadService::class)->delete($project->icon_path);
            $data['icon_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('icon'), 'website/projects/icons');
        }
        if ($request->hasFile('badge_icon_file')) {
            app(\App\Services\ImageUploadService::class)->delete($project->badge_icon);
            $data['badge_icon'] = app(\App\Services\ImageUploadService::class)->upload($request->file('badge_icon_file'), 'website/projects/badges');
        }
        if ($request->hasFile('action_icon_file')) {
            app(\App\Services\ImageUploadService::class)->delete($project->action_icon);
            $data['action_icon'] = app(\App\Services\ImageUploadService::class)->upload($request->file('action_icon_file'), 'website/projects/actions');
        }

        // Handle Dynamic Icons in Features
        $features = $request->input('features', []);
        if ($request->hasFile('features')) {
            foreach ($request->file('features') as $idx => $fFiles) {
                if (isset($fFiles['icon_file'])) {
                    // Delete old icon if it was a file
                    if (isset($project->features[$idx]['icon']) && Storage::disk('public')->exists($project->features[$idx]['icon'])) {
                        Storage::disk('public')->delete($project->features[$idx]['icon']);
                    }
                    $features[$idx]['icon'] = $fFiles['icon_file']->store('website/projects/dynamic', 'public');
                }
            }
        }
        $data['features'] = $features;

        // Handle Dynamic Icons in Stats
        $stats = $request->input('stats', []);
        if ($request->hasFile('stats')) {
            foreach ($request->file('stats') as $idx => $sFiles) {
                if (isset($sFiles['icon_file'])) {
                    // Delete old icon if it was a file
                    if (isset($project->stats[$idx]['icon']) && Storage::disk('public')->exists($project->stats[$idx]['icon'])) {
                        Storage::disk('public')->delete($project->stats[$idx]['icon']);
                    }
                    $stats[$idx]['icon'] = $sFiles['icon_file']->store('website/projects/dynamic', 'public');
                }
            }
        }
        $data['stats'] = $stats;

        $data['is_visible'] = $request->has('is_visible');
        $data['show_badge'] = $request->has('show_badge');
        $data['show_subcategory'] = $request->has('show_subcategory');

        unset($data['image'], $data['icon'], $data['badge_icon_file'], $data['action_icon_file']);
        $project->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث محتوى المشروع بنجاح');
    }

    public function destroyProject(Project $project)
    {
        if ($project->image_path)
            Storage::disk('public')->delete($project->image_path);
        if ($project->icon_path)
            Storage::disk('public')->delete($project->icon_path);
        // Delete badge icon if it's a file
        if ($project->badge_icon && Storage::disk('public')->exists($project->badge_icon)) {
            Storage::disk('public')->delete($project->badge_icon);
        }
        // Delete action icon if it's a file
        if ($project->action_icon && Storage::disk('public')->exists($project->action_icon)) {
            Storage::disk('public')->delete($project->action_icon);
        }
        // Delete dynamic feature icons
        if ($project->features) {
            foreach ($project->features as $feature) {
                if (isset($feature['icon']) && Storage::disk('public')->exists($feature['icon'])) {
                    Storage::disk('public')->delete($feature['icon']);
                }
            }
        }
        // Delete dynamic stat icons
        if ($project->stats) {
            foreach ($project->stats as $stat) {
                if (isset($stat['icon']) && Storage::disk('public')->exists($stat['icon'])) {
                    Storage::disk('public')->delete($stat['icon']);
                }
            }
        }
        $project->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حذف المشروع بنجاح');
    }

    public function storeProject(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'sponsorship_details' => 'nullable|string',
            'image' => 'nullable|any_image|max:10240',
            'icon' => 'nullable|any_image|max:5120',
            'badge_icon_file' => 'nullable|any_image|max:2048',
            'action_icon_file' => 'nullable|any_image|max:2048',
            'short_description' => 'nullable|string',
            'action_text' => 'nullable|string',
            'action_url' => 'nullable|string',
            'ui_button_color' => 'nullable|string',
            'badge_text' => 'nullable|string',
            'badge_icon' => 'nullable|string',
            'action_icon' => 'nullable|string',
            'subcategory_text' => 'nullable|string',
            'is_visible' => 'nullable',
            'show_badge' => 'nullable',
            'show_subcategory' => 'nullable',
            'features' => 'nullable|array',
            'stats' => 'nullable|array',
            'theme_colors' => 'nullable|array',
            'status' => 'nullable|in:active,archived',
            'fixed' => 'nullable',
        ]);

        $data['status'] = $data['status'] ?? 'active';
        $data['fixed'] = $request->has('fixed') ? (bool)$request->fixed : true;
        $data['is_visible'] = $request->has('is_visible');
        $data['show_badge'] = $request->has('show_badge');
        $data['show_subcategory'] = $request->has('show_subcategory');

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/projects');
        }
        if ($request->hasFile('icon')) {
            $data['icon_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('icon'), 'website/projects/icons');
        }
        if ($request->hasFile('badge_icon_file')) {
            $data['badge_icon'] = app(\App\Services\ImageUploadService::class)->upload($request->file('badge_icon_file'), 'website/projects/badges');
        }
        if ($request->hasFile('action_icon_file')) {
            $data['action_icon'] = app(\App\Services\ImageUploadService::class)->upload($request->file('action_icon_file'), 'website/projects/actions');
        }

        // Handle Dynamic Icons in Features
        $features = $request->input('features', []);
        if ($request->hasFile('features')) {
            foreach ($request->file('features') as $idx => $fFiles) {
                if (isset($fFiles['icon_file'])) {
                    $features[$idx]['icon'] = $fFiles['icon_file']->store('website/projects/dynamic', 'public');
                }
            }
        }
        $data['features'] = $features;

        // Handle Dynamic Icons in Stats
        $stats = $request->input('stats', []);
        if ($request->hasFile('stats')) {
            foreach ($request->file('stats') as $idx => $sFiles) {
                if (isset($sFiles['icon_file'])) {
                    $stats[$idx]['icon'] = $sFiles['icon_file']->store('website/projects/dynamic', 'public');
                }
            }
        }
        $data['stats'] = $stats;

        unset($data['image'], $data['icon'], $data['badge_icon_file'], $data['action_icon_file']);

        try {
            // Increase memory for image processing
            @ini_set('memory_limit', '512M');

            Project::create($data);
            Cache::forget('website_landing_page_data');
            return back()->with('success', 'تم إضافة المشروع الجديد بنجاح');
        }
        catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Project Creation Failed: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);
            return back()->withInput()->with('error', 'خطأ في عملية الإضافة: ' . $e->getMessage());
        }
    }


    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'season_title' => 'nullable|string|max:255',
            'season_year' => 'nullable|integer',
            'website_content' => 'nullable|string',
            'goal_amount' => 'nullable|numeric',
            'goal_unit' => 'nullable|string',
            'current_amount' => 'nullable|numeric',
            'beneficiaries_count' => 'nullable|integer',
            'share_price' => 'nullable|numeric',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'image' => 'nullable|any_image|max:10240',
            'ui_contribute_btn' => 'nullable|string',
            'ui_remind_btn' => 'nullable|string',
            'ui_ended_btn' => 'nullable|string',
            'ui_filter_upcoming' => 'nullable|string',
            'ui_collected_label' => 'nullable|string',
            'ui_benefited_label' => 'nullable|string',
            'ui_share_label' => 'nullable|string',
            'ui_goal_label' => 'nullable|string',
            'icon' => 'nullable|any_image|max:5120',
            'start_date_text' => 'nullable|string',
            'ui_progress_override' => 'nullable|string',
            'ui_collected_override' => 'nullable|string',
            'ui_goal_override' => 'nullable|string',
            'ui_beneficiaries_override' => 'nullable|string',
            'ui_share_override' => 'nullable|string',
            'ui_theme_color' => 'nullable|string',
            'ui_button_color' => 'nullable|string',
            'action_url' => 'nullable|string'
        ]);

        if (!$request->filled('season_year')) {
            $data['season_year'] = date('Y');
        }

        try {
            // Increase memory for image processing
            @ini_set('memory_limit', '512M');

            if ($request->hasFile('image')) {
                $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/campaigns');
            }
            if ($request->hasFile('icon')) {
                $data['icon_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('icon'), 'website/campaigns/icons');
            }
            unset($data['image'], $data['icon']);

            Campaign::create($data);

            Cache::forget('website_landing_page_data');
            return back()->with('success', 'تم إضافة الحملة الجديدة بنجاح');
        }
        catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Campaign Creation Failed: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);
            return back()->withInput()->with('error', 'خطأ في الإضافة: ' . $e->getMessage());
        }
    }

    public function updateCampaignContent(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'season_title' => 'nullable|string|max:255',
            'season_year' => 'nullable|integer',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'goal_amount' => 'nullable|numeric',
            'current_amount' => 'nullable|numeric',
            'beneficiaries_count' => 'nullable|integer',
            'share_price' => 'nullable|numeric',
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'image' => 'nullable|any_image|max:10240',
            'ui_contribute_btn' => 'nullable|string',
            'ui_remind_btn' => 'nullable|string',
            'ui_ended_btn' => 'nullable|string',
            'ui_filter_upcoming' => 'nullable|string',
            'ui_collected_label' => 'nullable|string',
            'ui_benefited_label' => 'nullable|string',
            'ui_share_label' => 'nullable|string',
            'ui_goal_label' => 'nullable|string',
            'icon' => 'nullable|any_image|max:5120',
            'start_date_text' => 'nullable|string',
            'ui_progress_override' => 'nullable|string',
            'ui_collected_override' => 'nullable|string',
            'ui_goal_override' => 'nullable|string',
            'ui_beneficiaries_override' => 'nullable|string',
            'ui_share_override' => 'nullable|string',
            'ui_theme_color' => 'nullable|string',
            'ui_button_color' => 'nullable|string',
            'action_url' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($campaign->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/campaigns');
        }
        if ($request->hasFile('icon')) {
            app(\App\Services\ImageUploadService::class)->delete($campaign->icon_path);
            $data['icon_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('icon'), 'website/campaigns/icons');
        }

        unset($data['image'], $data['icon']);
        $campaign->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث محتوى الحملة بنجاح');
    }

    public function destroyCampaign(Campaign $campaign)
    {
        if ($campaign->image_path) {
            Storage::disk('public')->delete($campaign->image_path);
        }
        if ($campaign->icon_path) {
            Storage::disk('public')->delete($campaign->icon_path);
        }
        $campaign->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حذف الحملة بنجاح');
    }

    // --- Testimonials ---
    public function testimonials()
    {
        $testimonials = WebTestimonial::orderByDesc('created_at')->get();
        return view('website.testimonials', compact('testimonials'));
    }

    public function shareOpinion()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $testimonials = WebTestimonial::orderByDesc('created_at')->get();
        return view('website.share_opinion', compact('settings', 'testimonials'));
    }

    // --- General Site Settings & Stats ---
    public function showSettings()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $testimonials = WebTestimonial::all();
        return view('website.settings', compact('settings', 'testimonials'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except([
            '_token',
            'gallery_image_1', 'gallery_image_2', 'gallery_image_3', 'gallery_image_4', 'gallery_image_5', 'gallery_image_6',
            'field_image_1', 'field_image_2', 'field_image_3', 'field_image_4',
            'banner_image_1', 'banner_image_2'
        ]);

        // Handle text settings
        $allowedKeys = [
            'stats_beneficiaries', 'stats_branches', 'stats_donations', 'stats_volunteers', 'stats_governorates',
            'stats_beneficiaries_label', 'stats_branches_label', 'stats_donations_label', 'stats_volunteers_label', 'stats_governorates_label',
            'stats_projects', 'stats_projects_label',
            'partners_stats_donors', 'partners_stats_volunteers', 'partners_stats_campaigns', 'partners_stats_institutions',
            'partners_stats_donors_label', 'partners_stats_volunteers_label', 'partners_stats_campaigns_label', 'partners_stats_institutions_label',
            'volunteer_wall_description',
            'hero_title_primary', 'hero_title_secondary', 'hero_description',
            'notification_label', 'notification_text', 'notification_link_text', 'notification_link_url',
            'campaigns_title', 'campaigns_subtitle', 'campaigns_active_label', 'campaigns_upcoming_label',
            'campaigns_contribute_btn', 'campaigns_remind_btn', 'campaigns_collected_label', 'campaigns_goal_label',
            'featured_campaign_title', 'featured_campaign_desc', 'featured_campaign_goal', 'featured_campaign_collected',
            'featured_campaign_beneficiaries', 'featured_campaign_days', 'featured_campaign_progress', 'featured_campaign_status',
            'featured_campaign_button_option', 'featured_campaign_button_text',
            'featured_campaign_share_price',
            'featured_campaign_starts_days', 'featured_campaign_starts_hours',
            'featured_campaign_btn_color',
            'featured_campaign_primary_color', 'featured_campaign_tint_color', 'featured_campaign_border_color', 'featured_campaign_icon_color',
            'featured_campaign_show_countdown', 'featured_campaign_show_custom_text', 'featured_campaign_custom_text',
            'featured_campaign_show_register_stats',
            'featured_campaign_register_label', 'featured_campaign_register_label2', 'featured_campaign_register_value1', 'featured_campaign_register_value2',
            'featured_campaign_progress_label',
            'featured_campaign_icon1_label', 'featured_campaign_icon1_value', 'featured_campaign_icon1_class',
            'featured_campaign_icon2_label', 'featured_campaign_icon2_value', 'featured_campaign_icon2_class',
            'campaign_stats_beneficiaries', 'campaign_stats_active', 'campaign_stats_donations', 'campaign_stats_governorates',
            'volunteer_description', 'volunteer_stats_volunteers', 'volunteer_stats_hours', 'volunteer_stats_branches',
            'cta_title', 'cta_text', 'cta_stat1_value', 'cta_stat1_label', 'cta_stat2_value', 'cta_stat2_label', 'cta_stat3_value', 'cta_stat3_label',
            'gh_home_title', 'gh_home_content',
            'ideal_partner_title',
            'ideal_partner_item1_label', 'ideal_partner_item1_value',
            'ideal_partner_item2_label', 'ideal_partner_item2_value',
            'ideal_partner_item3_label', 'ideal_partner_item3_value',
            'ideal_partner_item4_label', 'ideal_partner_item4_value'
        ];

        $inputs = $request->only($allowedKeys);
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                // Remove invisible characters and weird artifacts
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
            }
            \App\Models\WebSetting::set($key, $value);
        }

        // Handle Checkbox
        if ($request->has('notification_active') || $request->isMethod('POST')) {
            $notifActive = $request->has('notification_active') ? 'on' : 'off';
            // Only update if it was actually in the request (to avoid overriding when submitting other forms)
            if ($request->has('notification_label') || $request->has('notification_active')) {
                \App\Models\WebSetting::set('notification_active', $notifActive);
            }
        }

        // Check if any files were uploaded or any delete flags are present
        $hasDeletions = collect($request->all())->keys()->contains(function ($key) {
            return \Illuminate\Support\Str::startsWith($key, 'delete_');
        });

        if ($request->files->count() > 0 || $hasDeletions) {

            // Handle Gallery Images Deletion
            for ($i = 1; $i <= 6; $i++) {
                if ($request->input("delete_gallery_image_$i") == '1') {
                    $key = "gallery_image_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                        \App\Models\WebSetting::where('key', $key)->delete();
                    }
                }
            }

            // Handle Field Images Deletion
            for ($i = 1; $i <= 4; $i++) {
                if ($request->input("delete_field_image_$i") == '1') {
                    $key = "field_image_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                        \App\Models\WebSetting::where('key', $key)->delete();
                    }
                }
            }

            // Featured Campaign Deletions
            foreach (['featured_campaign_image', 'featured_campaign_icon_image', 'featured_campaign_icon'] as $imgKey) {
                if ($request->input("delete_$imgKey") == '1') {
                    $oldPath = \App\Models\WebSetting::get($imgKey);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                        \App\Models\WebSetting::where('key', $imgKey)->delete();
                    }
                }
            }

            // Featured Campaign Main Images
            $this->handleImageUpload($request, 'featured_campaign_image', 'website/campaigns', 'featured_campaign');
            $this->handleImageUpload($request, 'featured_campaign_icon_image', 'website/campaigns', 'featured_campaign');
            $this->handleImageUpload($request, 'featured_campaign_icon', 'website/campaigns', 'featured_campaign'); // Old key

            // Featured Campaign Icon Images
            for ($i = 1; $i <= 2; $i++) {
                $this->handleImageUpload($request, "featured_campaign_icon{$i}_image", 'website/campaigns', 'featured_campaign');
            }

            // Handle Gallery Images (1-6)
            for ($i = 1; $i <= 6; $i++) {
                $this->handleImageUpload($request, "gallery_image_$i", 'website/gallery', 'gallery');
            }

            // Handle Campaign Slider (1-10)
            for ($i = 1; $i <= 10; $i++) {
                $this->handleImageUpload($request, "campaign_slider_$i", 'website/campaigns/slider', 'campaign_slider');
            }

            // Handle Field Images (1-4)
            for ($i = 1; $i <= 4; $i++) {
                $this->handleImageUpload($request, "field_image_$i", 'website/field', 'field');
            }

            // Handle Campaign Slider Images (1-10)
            for ($i = 1; $i <= 10; $i++) {
                $this->handleImageUpload($request, "campaign_slider_$i", 'website/campaigns', 'campaign_slider');
            }

            // Handle Honor Wall Slider (1-3)
            for ($i = 1; $i <= 3; $i++) {
                $this->handleImageUpload($request, "honor_wall_slider_$i", 'website/honor_wall', 'honor_wall');
            }

            // Handle News Slider (1-10)
            for ($i = 1; $i <= 10; $i++) {
                if ($request->input("delete_news_slider_$i") == '1') {
                    $key = "news_slider_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    }
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
                $this->handleImageUpload($request, "news_slider_$i", 'website/news', 'news');
            }

            // Handle Contact Slider (1-10)
            for ($i = 1; $i <= 10; $i++) {
                if ($request->input("delete_contact_slider_$i") == '1') {
                    $key = "contact_slider_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    }
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
                $this->handleImageUpload($request, "contact_slider_$i", 'website/contact', 'contact');
            }

            // Handle Volunteer Slider (1-10)
            for ($i = 1; $i <= 10; $i++) {
                if ($request->input("delete_volunteer_slider_$i") == '1') {
                    $key = "volunteer_slider_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    }
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
                $this->handleImageUpload($request, "volunteer_slider_$i", 'website/volunteer', 'volunteer_slider');
            }

            // Handle Volunteer Hero Deletion
            if ($request->input('delete_volunteer_hero_image') == '1') {
                $key = 'volunteer_hero_image';
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                }
                \App\Models\WebSetting::where('key', $key)->delete();
            }

            // Handle Guest House Image Deletion
            if ($request->input('delete_gh_home_image') == '1') {
                $key = 'gh_home_image';
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                }
                \App\Models\WebSetting::where('key', $key)->delete();
            }

            // Handle Hero Image
            $this->handleImageUpload($request, 'hero_image', 'website/hero', 'hero');
            $this->handleImageUpload($request, 'volunteer_hero_image', 'website/volunteer', 'volunteer');
            $this->handleImageUpload($request, 'gh_home_image', 'website/gh', 'gh');

            // Handle Honor Wall Slider (1-10)
            for ($i = 1; $i <= 10; $i++) {
                if ($request->input("delete_honor_wall_slider_$i") == '1') {
                    $key = "honor_wall_slider_$i";
                    $oldPath = \App\Models\WebSetting::get($key);
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    }
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
                $this->handleImageUpload($request, "honor_wall_slider_$i", 'website/honor_wall', 'honor_wall_slider');
            }
        }

        self::clearLandingCache();
        return back()->with('success', 'تم تحديث إعدادات الصفحة الرئيسية بنجاح');
    }

    private function handleImageUpload(Request $request, $key, $path, $group)
    {
        if ($request->hasFile($key)) {
            try {
                // Critical for multiple heavy images (Slider)
                @ini_set('memory_limit', '1G');
                @set_time_limit(300);

                $oldPath = \App\Models\WebSetting::get($key);

                // Optimize: resize + convert to WebP + compress
                $savedPath = app(\App\Services\ImageUploadService::class)->upload($request->file($key), $path);

                if ($savedPath) {
                    if ($oldPath) {
                        app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    }
                    \App\Models\WebSetting::set($key, $savedPath, $group, 'image');
                }
                else {
                    \Illuminate\Support\Facades\Log::warning("Image optimization failed for key: $key");
                }
            }
            catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Image upload failed for key: $key", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public function updateContactSettings(Request $request)
    {
        $keys = [
            // Section Headers
            'contact_section_title', 'contact_section_subtitle',
            // Contact Channels
            'contact_phone_link',
            'contact_email_link',
            'contact_whatsapp_link',
            // Detailed Info
            'contact_hq_address', 'contact_hq_details', 'contact_hotline',
            'contact_support_email', 'contact_working_days', 'contact_working_hours',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                \App\Models\WebSetting::set($key, $request->input($key));
            }
        }

        // Handle Contact Slider Images Deletion
        for ($i = 1; $i <= 10; $i++) {
            if ($request->input("delete_contact_slider_$i") == '1') {
                $key = "contact_slider_$i";
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
            }
        }

        // Handle Contact Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $this->handleImageUpload($request, "contact_slider_$i", 'website/contact', 'contact_slider');
        }

        \App\Http\Controllers\WebsiteWebController::clearLandingCache();
        return back()->with('success', 'تم تحديث إعدادات قنوات التواصل بنجاح');
    }

    public function updateCampaignStats(Request $request)
    {
        $keys = [
            'campaign_stats_beneficiaries',
            'campaign_stats_active',
            'campaign_stats_donations',
            'campaign_stats_governorates',
            'featured_campaign_title',
            'featured_campaign_beneficiaries',
            'featured_campaign_progress',
            'featured_campaign_button_text',
            // Keep old keys just in case
            'campaign_featured_title', 'campaign_featured_year', 'campaign_featured_subtitle', 'campaign_featured_button',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                \App\Models\WebSetting::set($key, $request->input($key));
            }
        }

        // Handle Campaign Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $this->handleImageUpload($request, "campaign_slider_$i", 'website/campaigns', 'campaign_slider');
        }

        // Handle Featured Banner Icon (as Image)
        if ($request->input('delete_featured_campaign_icon') == '1') {
            $key = 'featured_campaign_icon';
            $oldPath = \App\Models\WebSetting::get($key);
            if ($oldPath) {
                app(\App\Services\ImageUploadService::class)->delete($oldPath);
                \App\Models\WebSetting::where('key', $key)->delete();
            }
        }

        if ($request->hasFile('featured_campaign_icon')) {
            $this->handleImageUpload($request, 'featured_campaign_icon', 'website/campaigns', 'featured_campaign');
        }

        \App\Http\Controllers\WebsiteWebController::clearLandingCache();
        return back()->with('success', 'تم تحديث إحصائيات الحملات بنجاح');
    }

    public function updateProjectStats(Request $request)
    {
        $keys = [
            'stats_donations',
            'stats_projects',
            'stats_governorates',
            'stats_beneficiaries',
            'projects_page_title',
            'projects_page_description',
            // Sponsorship Section Headers
            'sponsorship_section_badge', 'sponsorship_section_title', 'sponsorship_section_subtitle',
            // Hope
            'hope_title', 'hope_pill1', 'hope_pill2', 'hope_action_url', 'hope_stats_beneficiaries', 'hope_stats_medical', 'hope_description', 'hope_sponsorship_amount', 'hope_category',
            // Midrar
            'midrar_title', 'midrar_stats_roofs', 'midrar_stats_water', 'midrar_description', 'midrar_category',
            // Zad
            'zad_title', 'zad_pill1', 'zad_pill2', 'zad_action_url', 'zad_stats_orphans', 'zad_stats_support', 'zad_description', 'zad_sponsorship_amount', 'zad_category',
            // Kiswah
            'kiswah_title', 'kiswah_stats_families', 'kiswah_stats_pieces', 'kiswah_description', 'kiswah_category',
            'stats_donations_label', 'stats_projects_label', 'stats_governorates_label', 'stats_beneficiaries_label',
            'stats_branches_label', 'stats_volunteers_label',
            // Sponsorship Programs
            'sponsorship_prog1_title', 'sponsorship_prog1_desc', 'sponsorship_prog1_feature1', 'sponsorship_prog1_feature2', 'sponsorship_prog1_price', 'sponsorship_prog1_currency',
            'sponsorship_prog2_title', 'sponsorship_prog2_desc', 'sponsorship_prog2_feature1', 'sponsorship_prog2_feature2', 'sponsorship_prog2_price', 'sponsorship_prog2_currency',
        ];


        foreach ($keys as $key) {
            if ($request->has($key)) {
                \App\Models\WebSetting::set($key, $request->input($key));
            }
        }

        // Handle Project Slider Images Deletion
        for ($i = 1; $i <= 10; $i++) {
            if ($request->input("delete_slider_$i") == '1') {
                $key = "project_slider_$i";
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
            }
        }

        // Handle Project Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $this->handleImageUpload($request, "project_slider_$i", 'website/projects', 'project_slider');
        }

        \App\Http\Controllers\WebsiteWebController::clearLandingCache();
        return back()->with('success', 'تم تحديث إحصائيات المشاريع بنجاح');
    }

    public function updateGuestHouseStats(Request $request)
    {
        $keys = [
            'gh_stats_patients', 'gh_stats_patients_label',
            'gh_stats_beds', 'gh_stats_beds_label',
            'gh_stats_reception', 'gh_stats_reception_label',
            'gh_stats_branches', 'gh_stats_branches_label',
            'gh_hero_subtitle'
        ];


        foreach ($keys as $key) {
            if ($request->has($key)) {
                $val = $request->input($key);
                \App\Models\WebSetting::set($key, $val);

                // Sync with positional keys for API
                if ($key == 'gh_stats_beds')
                    \App\Models\WebSetting::set('gh_stat1_value', $val, 'guest_house');
                if ($key == 'gh_stats_beds_label')
                    \App\Models\WebSetting::set('gh_stat1_label', $val, 'guest_house');
                if ($key == 'gh_stats_patients')
                    \App\Models\WebSetting::set('gh_stat2_value', $val, 'guest_house');
                if ($key == 'gh_stats_patients_label')
                    \App\Models\WebSetting::set('gh_stat2_label', $val, 'guest_house');
                if ($key == 'gh_stats_branches')
                    \App\Models\WebSetting::set('gh_stat3_value', $val, 'guest_house');
                if ($key == 'gh_stats_branches_label')
                    \App\Models\WebSetting::set('gh_stat3_label', $val, 'guest_house');
                if ($key == 'gh_stats_reception')
                    \App\Models\WebSetting::set('gh_stat4_value', $val, 'guest_house');
                if ($key == 'gh_stats_reception_label')
                    \App\Models\WebSetting::set('gh_stat4_label', $val, 'guest_house');
            }
        }


        // Handle Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            // Check for deletion request
            if ($request->input("delete_gh_slider_$i") == '1') {
                $key = "gh_slider_$i";
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
            }
            $this->handleImageUpload($request, "gh_slider_$i", 'website/guest_house', 'guest_house');
        }

        // Handle Gallery Images (4 images)
        for ($i = 1; $i <= 4; $i++) {
            $key = "gh_gallery_image_$i";
            if ($request->hasFile($key)) {
                $oldPath = \App\Models\WebSetting::get($key);
                app(\App\Services\ImageUploadService::class)->delete($oldPath);
                $path = app(\App\Services\ImageUploadService::class)->upload($request->file($key), 'website/guest_house');
                \App\Models\WebSetting::set($key, $path, 'guest_house', 'image');
            }
        }

        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث إحصائيات وصور دار الضيافة بنجاح');
    }

    // --- Guest House Content ---
    public function guestHouseContent(Request $request)
    {
        $page = WebPage::firstOrCreate(
        ['slug' => 'guest-house'],
        ['title' => 'دار الضيافة - المقر الرئيسي', 'is_published' => true]
        );
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();

        $query = WebRoomBooking::query();

        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_query')) {
            $term = $request->search_query;
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($term) {
                $q->where('phone', 'like', "%{$term}%")
                    ->orWhere('national_id', 'like', "%{$term}%");
            });
        }

        $bookings = $query->orderByDesc('created_at')->get();

        return view('website.guest_house_content', compact('page', 'settings', 'bookings'));
    }

    public function guestHouseContentUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $page = \App\Models\WebPage::where('slug', 'guest-house')->firstOrFail();

        $data = $request->only(['title', 'content', 'meta_title', 'meta_description']);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($page->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/pages');
        }

        $page->update($data);

        // Handle Stats from view (gh_stat1_value, etc.)
        $statsKeys = [
            'gh_hero_subtitle',
            'gh_stat1_value', 'gh_stat1_label',
            'gh_stat2_value', 'gh_stat2_label',
            'gh_stat3_value', 'gh_stat3_label',
            'gh_stat4_value', 'gh_stat4_label',
        ];

        foreach ($statsKeys as $key) {
            if ($request->has($key)) {
                \App\Models\WebSetting::set($key, $request->input($key), 'guest_house');
            }
        }

        // Handle Slider Images (1-10)
        for ($i = 1; $i <= 10; $i++) {
            // Deletion
            if ($request->input("delete_gh_slider_$i") == '1') {
                $key = "gh_slider_$i";
                $oldPath = \App\Models\WebSetting::get($key);
                if ($oldPath) {
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    \App\Models\WebSetting::where('key', $key)->delete();
                }
            }

            $this->handleImageUpload($request, "gh_slider_$i", 'website/guest_house', 'guest_house');
        }

        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث محتوى وإحصائيات دار الضيافة بنجاح');
    }

    // --- Volunteer Wall (Leaders of Giving) ---
    public function volunteerWall()
    {
        $leaders = \App\Models\WebVolunteerWall::orderBy('rank')->get();
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        return view('website.volunteer_wall', compact('leaders', 'settings'));
    }

    public function volunteerWallStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'nullable|string',
            'hours' => 'required|string',
            'rank' => 'required|integer',
            'image' => 'nullable|any_image|max:5120'
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/volunteers');
        }

        \App\Models\WebVolunteerWall::create($data);
        \Illuminate\Support\Facades\Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم إضافة قائد العطاء بنجاح');
    }

    public function guestHouseSlider()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        return view('website.guest_house_slider', compact('settings'));
    }

    public function volunteerWallUpdate(Request $request, \App\Models\WebVolunteerWall $leader)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'nullable|string',
            'hours' => 'required|string',
            'rank' => 'required|integer',
            'image' => 'nullable|any_image|max:5120'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($leader->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/volunteers');
        }

        $leader->update($data);
        \Illuminate\Support\Facades\Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات قائد العطاء بنجاح');
    }

    public function volunteerWallDestroy(\App\Models\WebVolunteerWall $leader)
    {
        app(\App\Services\ImageUploadService::class)->delete($leader->image_path);
        $leader->delete();
        \Illuminate\Support\Facades\Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم الحذف بنجاح');
    }

    // --- Dynamic Cards ---
    public function cards()
    {
        $cards = \App\Models\WebDynamicCard::orderBy('sort_order')->get();
        return view('website.cards.index', compact('cards'));
    }

    public function cardStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'badge_text' => 'nullable|string',
            'badge_icon' => 'nullable|string',
            'tag_text' => 'nullable|string',
            'main_button_text' => 'nullable|string',
            'main_button_action' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'card_color' => 'nullable|string',
            'stats_data' => 'nullable',
            'buttons_data' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/cards');
        }

        $data['badge_visible'] = $request->has('badge_visible');
        $data['is_active'] = $request->has('is_active');

        if (isset($request->stats_data) && is_string($request->stats_data))
            $data['stats_data'] = json_decode($request->stats_data, true);
        if (isset($request->buttons_data) && is_string($request->buttons_data))
            $data['buttons_data'] = json_decode($request->buttons_data, true);

        \App\Models\WebDynamicCard::create($data);

        return back()->with('success', 'تم إضافة البطاقة بنجاح');
    }

    public function cardUpdate(Request $request, \App\Models\WebDynamicCard $card)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'badge_text' => 'nullable|string',
            'badge_icon' => 'nullable|string',
            'tag_text' => 'nullable|string',
            'main_button_text' => 'nullable|string',
            'main_button_action' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'card_color' => 'nullable|string',
            'stats_data' => 'nullable',
            'buttons_data' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($card->image);
            $data['image'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/cards');
        }

        $data['badge_visible'] = $request->has('badge_visible');
        $data['is_active'] = $request->has('is_active');

        if (isset($request->stats_data) && is_string($request->stats_data))
            $data['stats_data'] = json_decode($request->stats_data, true);
        if (isset($request->buttons_data) && is_string($request->buttons_data))
            $data['buttons_data'] = json_decode($request->buttons_data, true);

        $card->update($data);

        return back()->with('success', 'تم تحديث البطاقة بنجاح');
    }

    public function cardDestroy(\App\Models\WebDynamicCard $card)
    {
        $card->delete();
        return back()->with('success', 'تم حذف البطاقة بنجاح');
    }

    // --- Testimonials ---
    public function testimonialStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'image' => 'nullable|any_image|max:2048'
        ]);

        $data = $request->only(['name', 'role', 'content', 'rating', 'status']);
        $data['rating'] = $data['rating'] ?? 5;
        $data['status'] = $data['status'] ?? 'approved';

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
        }

        WebTestimonial::create($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم إضافة التعليق بنجاح');
    }

    public function testimonialUpdate(Request $request, WebTestimonial $testimonial)
    {
        $request->validate([
            'name' => 'required',
            'role' => 'nullable|string|max:255',
            'content' => 'required',
            'image' => 'nullable|any_image|max:5120',
            'rating' => 'nullable|integer|min:1|max:5',
        ], [
            'name.required' => 'الاسم مطلوب',
            'content.required' => 'المحتوى مطلوب',
        ]);

        $data = $request->only(['name', 'role', 'content', 'rating', 'status']);

        // Ensure rating is not null to prevent DB crash (it's not nullable)
        if (!isset($data['rating']) || $data['rating'] === null) {
            unset($data['rating']);
        }

        if ($request->hasFile('image')) {
            if ($testimonial->image_path) {
                app(\App\Services\ImageUploadService::class)->delete($testimonial->image_path);
            }
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
        }

        $testimonial->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات رأي العميل بنجاح');
    }

    public function testimonialDestroy(WebTestimonial $testimonial)
    {
        app(\App\Services\ImageUploadService::class)->delete($testimonial->image_path);
        $testimonial->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حفظ التعديلات بنجاح!');
    }

    public function headquarters()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $branches = WebBranch::all();
        return view('website.headquarters', compact('settings', 'branches'));
    }

    public function branchStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable',
            'working_hours' => 'nullable',
            'google_maps_url' => 'nullable',
            'status_text' => 'nullable',
            'description' => 'nullable',
        ]);

        $data['is_main'] = $request->has('is_main');
        $data['status_text'] = (string)($data['status_text'] ?? '');

        if ($data['is_main']) {
            \App\Models\WebBranch::where('is_main', true)->update(['is_main' => false]);
        }

        \App\Models\WebBranch::create($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم إضافة الفرع بنجاح');
    }

    public function branchUpdate(Request $request, $id)
    {
        $branch = \App\Models\WebBranch::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable',
            'working_hours' => 'nullable',
            'google_maps_url' => 'nullable',
            'status_text' => 'nullable',
            'description' => 'nullable',
        ]);

        $data['is_main'] = $request->has('is_main');
        $data['status_text'] = (string)($data['status_text'] ?? '');

        if ($data['is_main']) {
            \App\Models\WebBranch::where('id', '!=', $branch->id)->where('is_main', true)->update(['is_main' => false]);
        }

        $branch->update($data);
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات الفرع بنجاح');
    }

    public function branchDestroy(WebBranch $branch)
    {
        $branch->delete();
        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم حذف الفرع بنجاح');
    }

    public function updateHeadquarters(Request $request)
    {
        $allowedKeys = [
            'headquarters_title', 'headquarters_description',
            'headquarters_address', 'headquarters_phone', 'headquarters_working_hours',
            'headquarters_feature1', 'headquarters_feature2', 'headquarters_feature3',
            'headquarters_directions_label', 'headquarters_call_label',
            'headquarters_stats_branches', 'headquarters_stats_governorates',
            'headquarters_stats_employees', 'headquarters_stats_donors',
            'headquarters_stats_branches_label', 'headquarters_stats_governorates_label',
            'headquarters_stats_employees_label', 'headquarters_stats_donors_label'
        ];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                elseif (is_string($value)) {
                    $value = trim($value);
                    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                }
                \App\Models\WebSetting::set($key, (string)$value);
            }
        }

        Cache::forget('website_landing_page_data');
        return back()->with('success', 'تم تحديث بيانات المقر بنجاح');
    }
    public function createDummyBookings()
    {
        $dummyData = [
            [
                'name' => 'أحمد محمد علي',
                'phone' => '01012345678',
                'email' => 'ahmed@example.com',
                'room_type' => 'غرفة مفردة',
                'check_in' => \Carbon\Carbon::now()->addDays(2),
                'check_out' => \Carbon\Carbon::now()->addDays(5),
                'status' => 'pending',
                'national_id' => '29001010101234',
                'medical_center' => 'مستشفى السلام',
                'arrival_date' => \Carbon\Carbon::now()->addDays(2),
                'expected_duration' => '3 أيام',
                'notes' => 'حالة حرجة، مريض كبد',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'name' => 'سارة محمود حسن',
                'phone' => '01198765432',
                'email' => 'sara@example.com',
                'room_type' => 'غرفة مزدوجة',
                'check_in' => \Carbon\Carbon::now()->addDays(1),
                'check_out' => \Carbon\Carbon::now()->addDays(10),
                'status' => 'confirmed',
                'national_id' => '29505050109876',
                'medical_center' => 'مركز الأورام',
                'arrival_date' => \Carbon\Carbon::now()->addDays(1),
                'expected_duration' => '9 أيام',
                'companion_name' => 'محمد حسن',
                'companion_phone' => '01234567890',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'name' => 'محمود إبراهيم',
                'phone' => '01511223344',
                'email' => 'mahmoud@example.com',
                'room_type' => 'جناح عائلي',
                'check_in' => \Carbon\Carbon::now()->subDays(2),
                'check_out' => \Carbon\Carbon::now()->addDays(2),
                'status' => 'cancelled',
                'national_id' => '28003030101122',
                'medical_center' => 'مستشفى عين شمس',
                'arrival_date' => \Carbon\Carbon::now()->subDays(2),
                'expected_duration' => '4 أيام',
                'created_at' => \Carbon\Carbon::now()
            ],
        ];

        foreach ($dummyData as $data) {
            \App\Models\WebRoomBooking::create($data);
        }

        return redirect()->route('guest-house.content')->with('success', 'تم إنشاء بيانات الحجز التجريبية بنجاح');
    }
    public function donationPage()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $categories = \App\Models\DonationCategory::orderBy('sort_order')->get();
        return view('website.donation_page', compact('settings', 'categories'));
    }

    public function updateDonationPage(Request $request)
    {
        $allowedKeys = [
            'donation_page_campaign_title',
            'donation_page_campaign_link',
            'donation_page_hero_text',
            'donation_page_hero_desc',
            'donation_page_stats_donors',
            'donation_page_stats_today_collected',
            'donation_page_projects_title',
            'donation_page_projects_desc'
        ];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                if (is_string($value)) {
                    $value = trim($value);
                    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                }
                \App\Models\WebSetting::set($key, (string)$value, 'donation_page');

                // Sync with service keys
                if ($key === 'donation_page_campaign_link') {
                    \App\Models\WebSetting::set('donation_banner_urgent_link', (string)$value, 'donation_page');
                }
                if ($key === 'donation_page_campaign_title') {
                    \App\Models\WebSetting::set('donation_banner_urgent_campaign', (string)$value, 'donation_page');
                }
                if ($key === 'donation_page_projects_title') {
                    \App\Models\WebSetting::set('donation_projects_title', (string)$value, 'donation_page');
                }
                if ($key === 'donation_page_projects_desc') {
                    \App\Models\WebSetting::set('donation_projects_subtitle', (string)$value, 'donation_page');
                }

                // Global Notification Sync
                if ($key === 'donation_page_campaign_title') {
                    \App\Models\WebSetting::set('notification_text', (string)$value, 'general');
                    \App\Models\WebSetting::set('notification_active', 'on', 'general');
                }
                if ($key === 'donation_page_campaign_link') {
                    \App\Models\WebSetting::set('notification_link_url', (string)$value, 'general');
                }
            }
        }

        // Categories are now managed via the DonationCategory model in the unified page.

        // Banner image handling removed as requested

        self::clearLandingCache();
        return back()->with('success', 'تم تحديث إعدادات صفحة التبرعات بنجاح');
    }

    public function accounts()
    {
        // Base paginated list of web donors
        $accounts = \App\Models\WebDonor::latest()->paginate(20);

        // Collect all phone numbers for lookup
        $phones = $accounts->getCollection()->pluck('phone')->filter()->unique()->values()->toArray();

        // Fetch legacy donor IDs by phone, keyed by phone
        $legacyDonors = \App\Models\Donor::whereIn('phone', $phones)
            ->get(['id', 'name', 'phone'])
            ->keyBy('phone');

        // Aggregate web_donations per donor_id (for guest donations linked by donor.phone)
        // and per web_donor_id (for authenticated donations)
        $donorIds = $legacyDonors->pluck('id')->toArray();
        $webDonorIds = $accounts->getCollection()->pluck('id')->toArray();

        // Count & sum by web_donor_id (authenticated)
        $byWebDonorId = \DB::table('web_donations')
            ->whereIn('web_donor_id', $webDonorIds)
            ->selectRaw('web_donor_id, COUNT(*) as cnt, SUM(amount) as total_amount')
            ->groupBy('web_donor_id')
            ->get()
            ->keyBy('web_donor_id');

        // Count & sum by donor_id + phone lookup (guest)
        $byDonorId = \DB::table('web_donations')
            ->whereIn('donor_id', $donorIds)
            ->whereNull('web_donor_id')
            ->selectRaw('donor_id, COUNT(*) as cnt, SUM(amount) as total_amount')
            ->groupBy('donor_id')
            ->get()
            ->keyBy('donor_id');

        $isTemporaryName = static function (?string $name): bool {
            $normalized = trim((string) $name);
            return $normalized !== '' && preg_match('/^Donor\s+\d{4}$/i', $normalized) === 1;
        };

        foreach ($accounts as $account) {
            $donor = $legacyDonors->get($account->phone);
            $account->donor_id = $donor ? $donor->id : null;
            $account->display_name = $isTemporaryName($account->name) && !empty($donor?->name)
                ? $donor->name
                : $account->name;
            $account->is_temporary_name = $isTemporaryName($account->display_name);

            // Donations linked directly by web_donor_id
            $authStats  = $byWebDonorId->get($account->id);
            // Donations linked via the legacy donor record (guest)
            $guestStats = $donor ? $byDonorId->get($donor->id) : null;

            $account->total_donations_count  = ($authStats->cnt ?? 0) + ($guestStats->cnt ?? 0);
            $account->total_donations_amount = ($authStats->total_amount ?? 0) + ($guestStats->total_amount ?? 0);
        }

        return view('website.accounts', compact('accounts'));
    }

    public function accountStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:web_donors,phone',
            'email' => 'nullable|email|unique:web_donors,email',
        ]);

        \App\Models\WebDonor::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'active' => true,
            'email' => $data['email'] ?? ($data['phone'] . '@anasen.charity'),
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16))
        ]);

        return back()->with('success', 'تم إنشاء الحساب بنجاح');
    }

    public function accountUpdate(Request $request, \App\Models\WebDonor $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => trim($data['name']),
        ]);

        return back()->with('success', 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ø§Ø³Ù… Ø§Ù„Ø­Ø³Ø§Ø¨ Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function accountDestroy(\App\Models\WebDonor $user)
    {
        $user->delete();
        return back()->with('success', 'تم حذف الحساب بنجاح');
    }

    public static function clearLandingCache()
    {
        Cache::forget('website_landing_page_data');
    }
}
