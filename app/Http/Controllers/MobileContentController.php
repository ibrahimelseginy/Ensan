<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\MobileNotification;
use App\Models\MobileBanner;
use App\Models\MobileCaseApplication;
use App\Models\MobileInKindDonation;
use App\Models\MobileHomeItem;
use App\Models\EnsanPillar;
use Illuminate\Support\Facades\Storage;

final class MobileContentController extends Controller
{
    // --- Dashboard View to Manage All Mobile Content ---
    public function index()
    {
        $projects = Project::where('show_on_mobile', true)->get();
        $campaigns = Campaign::where('show_on_mobile', true)->get();
        return view('mobile.dashboard', compact('projects', 'campaigns'));
    }

    // --- Banners Management ---
    public function bannersIndex()
    {
        $banners = MobileBanner::orderBy('sort_order')->get();
        return view('mobile.banners', compact('banners'));
    }

    public function bannerStore(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|any_image|max:5120',
            'title' => 'nullable|string',
            'link_type' => 'nullable|string',
            'link_id' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $banner = MobileBanner::create($data);

        if ($request->hasFile('image')) {
            $banner->uploadImage($request->file('image'), 'mobile/banners');
        }
        return back()->with('success', 'Banner created successfully');
    }

    public function bannerDestroy(MobileBanner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner deleted successfully');
    }

    // --- Notifications Management ---
    public function notificationsIndex()
    {
        $notifications = MobileNotification::orderByDesc('created_at')->get();
        return view('mobile.notifications', compact('notifications'));
    }

    public function notificationStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'category' => 'nullable|string|in:عام,حملات,تبرعات,عاجل',
            'target_audience' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        // Logic to send push notification via Firebase/OneSignal would go here
        $data['is_sent'] = true;
        $data['sent_at'] = now();

        $notification = MobileNotification::create($data);

        if ($request->hasFile('image')) {
            $notification->uploadImage($request->file('image'), 'mobile/notifications');
        }
        return back()->with('success', 'Notification sent successfully');
    }

    public function notificationUpdate(Request $request, MobileNotification $notification)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'category' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $notification->update($data);

        if ($request->hasFile('image')) {
            $notification->uploadImage($request->file('image'), 'mobile/notifications');
        }

        return back()->with('success', 'Notification updated successfully');
    }

    public function notificationDestroy(MobileNotification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted successfully');
    }


    // --- In-Kind Donations (e.g. clothes, furniture) ---
    public function inKindDonationsIndex()
    {
        $donations = MobileInKindDonation::orderByDesc('created_at')->get();
        return view('mobile.in_kind_donations', compact('donations'));
    }

    public function inKindDonationUpdateStatus(Request $request, MobileInKindDonation $donation)
    {
        $donation->update(['status' => $request->status]);
        return back()->with('success', 'Donation status updated');
    }

    // --- Update Mobile Specific Contentfor Projects/Campaigns ---
    public function updateProjectMobileContent(Request $request, Project $project)
    {
        $project->update([
            'mobile_content' => $request->mobile_content,
            'show_on_mobile' => $request->has('show_on_mobile')
        ]);
        return back()->with('success', 'Project mobile content updated');
    }

    public function updateCampaignMobileContent(Request $request, Campaign $campaign)
    {
        $campaign->update([
            'mobile_content' => $request->mobile_content,
            'show_on_mobile' => $request->has('show_on_mobile')
        ]);
        return back()->with('success', 'Campaign mobile content updated');
    }

    // --- Home Content Management ---
    public function homeContentIndex()
    {
        $heroes = MobileHomeItem::with('cards')->where('type', 'hero')->orderBy('sort_order')->get();
        $gallery = MobileHomeItem::where('type', 'gallery')->orderBy('sort_order')->get();
        $services = MobileHomeItem::where('type', 'service')->orderBy('sort_order')->get();
        $shareItems = MobileHomeItem::where('type', 'share')->orderBy('sort_order')->get();
        $campaigns = MobileHomeItem::where('type', 'campaign')->orderBy('sort_order')->get();
        $finalSection = MobileHomeItem::where('type', 'final')->first();
        $aboutUs = MobileHomeItem::where('type', 'about_us')->first();

        $pillars = EnsanPillar::with(['projects', 'services'])->orderBy('sort_order')->get();
        $allProjects = Project::where('show_on_mobile', true)->get();
        $serviceItems = MobileHomeItem::where('type', 'service')->orderBy('sort_order')->get();

        return view('mobile.home_content', compact('heroes', 'gallery', 'services', 'shareItems', 'campaigns', 'finalSection', 'aboutUs', 'pillars', 'allProjects', 'serviceItems'));
    }

    public function homeContentStore(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'icon' => 'nullable|nullable', // Keep validation simple, handle as file if present
            'price' => 'nullable|numeric',
            'share_price' => 'nullable|numeric',
            'details' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'cards' => 'nullable|array',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.image' => 'nullable|any_image|max:5120'
        ]);

        if ($request->hasFile('icon')) {
             unset($data['icon']);
        }

        unset($data['image'], $data['cards']);
        $item = MobileHomeItem::create($data);

        if ($request->hasFile('image')) {
            $item->uploadImage($request->file('image'), 'mobile/home');
        }

        if ($request->hasFile('icon')) {
            $item->uploadImage($request->file('icon'), 'mobile/home/icons', 'icon');
        }

        if ($request->has('cards') && is_array($request->cards)) {
            foreach ($request->cards as $index => $cardData) {
                if (empty($cardData['title']) && empty($cardData['description']) && !$request->hasFile("cards.{$index}.image")) {
                    continue;
                }
                $card = $item->cards()->create([
                    'title' => $cardData['title'] ?? null,
                    'description' => $cardData['description'] ?? null,
                ]);
                if ($request->hasFile("cards.{$index}.image")) {
                    $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/home/cards');
                }
            }
        }

        return back()->with('success', 'Item added successfully');
    }

    public function homeContentUpdate(Request $request, MobileHomeItem $item)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'icon' => 'nullable|nullable',
            'price' => 'nullable|numeric',
            'share_price' => 'nullable|numeric',
            'details' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'cards' => 'nullable|array',
            'cards.*.id' => 'nullable|integer',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.image' => 'nullable|any_image|max:5120'
        ]);

        if ($request->hasFile('icon')) {
            unset($data['icon']);
        }

        unset($data['image'], $data['cards']);
        $item->update($data);

        if ($request->hasFile('image')) {
            $item->uploadImage($request->file('image'), 'mobile/home');
        }

        if ($request->hasFile('icon')) {
            $item->uploadImage($request->file('icon'), 'mobile/home/icons', 'icon');
        }

        $submittedCardIds = [];
        if ($request->has('cards') && is_array($request->cards)) {
            foreach ($request->cards as $index => $cardData) {
                if (empty($cardData['id']) && empty($cardData['title']) && empty($cardData['description']) && !$request->hasFile("cards.{$index}.image")) {
                    continue;
                }
                if (!empty($cardData['id'])) {
                    $card = $item->cards()->find($cardData['id']);
                    if ($card) {
                        $card->update([
                            'title' => $cardData['title'] ?? null,
                            'description' => $cardData['description'] ?? null,
                        ]);
                        $submittedCardIds[] = $card->id;
                        if ($request->hasFile("cards.{$index}.image")) {
                            $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/home/cards');
                        }
                    }
                } else {
                    $card = $item->cards()->create([
                        'title' => $cardData['title'] ?? null,
                        'description' => $cardData['description'] ?? null,
                    ]);
                    $submittedCardIds[] = $card->id;
                    if ($request->hasFile("cards.{$index}.image")) {
                        $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/home/cards');
                    }
                }
            }
        }
        if ($item->type === 'hero') {
            $cardsToDelete = $item->cards()->whereNotIn('id', $submittedCardIds)->get();
            foreach ($cardsToDelete as $card) {
                if ($card->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($card->image_path);
                }
                $card->delete();
            }
        }

        // Clear landing cache if applicable
        if (class_exists('\App\Http\Controllers\WebsiteWebController')) {
            \App\Http\Controllers\WebsiteWebController::clearLandingCache();
        }

        return back()->with('success', 'Item updated successfully');
    }

    public function homeContentDestroy(MobileHomeItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item deleted successfully');
    }

    // --- Mobile Volunteer Requests ---
    public function volunteerRequestsIndex()
    {
        $requests = \App\Models\MobileVolunteerRequest::orderByDesc('created_at')->get();
        return view('mobile.volunteer_requests', compact('requests'));
    }

    public function updateVolunteerRequestStatus(Request $request, \App\Models\MobileVolunteerRequest $volunteerRequest)
    {
        $volunteerRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);
        return back()->with('success', 'Status updated');
    }

    public function destroyVolunteerRequest(\App\Models\MobileVolunteerRequest $volunteerRequest)
    {
        $volunteerRequest->delete();
        return back()->with('success', 'Request deleted');
    }

    public function downloadVolunteerCV(\App\Models\MobileVolunteerRequest $volunteerRequest)
    {
        if ($volunteerRequest->cv_path && Storage::disk('public')->exists($volunteerRequest->cv_path)) {
            return response()->file(Storage::disk('public')->path($volunteerRequest->cv_path));
        }
        return back()->with('error', 'File not found');
    }

    // --- Mobile News Management ---
    public function newsIndex()
    {
        $news = \App\Models\MobileNews::orderByDesc('created_at')->get();
        return view('mobile.news', compact('news'));
    }

    public function newsStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category' => 'nullable|in:عام,حملات,تبرعات,عاجل',
            'image' => 'nullable|image',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'any_image|max:5120'
        ]);

        $creationData = $data;
        unset($creationData['additional_images']);

        $news = \App\Models\MobileNews::create($creationData);
        if ($request->hasFile('image')) {
            $news->uploadImage($request->file('image'), 'mobile/news');
        }

        if ($request->hasFile('additional_images')) {
            $additional = [];
            foreach ($request->file('additional_images') as $file) {
                $path = $file->store('mobile/news/gallery', 'public');
                $additional[] = $path;
            }
            $news->update(['additional_images' => $additional]);
        }

        return back()->with('success', 'News added');
    }

    public function newsUpdate(Request $request, \App\Models\MobileNews $news)
    {
        $data = $request->validate([
            'title'                  => 'required|string',
            'content'                => 'required|string',
            'category'               => 'nullable|string|in:عام,حملات,تبرعات,عاجل',
            'image'                  => 'nullable|any_image|max:5120',
            'delete_image'           => 'nullable|boolean',
            'additional_images'      => 'nullable|array',
            'additional_images.*'    => 'any_image|max:5120',
            'delete_additional_images'=> 'nullable|boolean',
            'published_at'           => 'nullable|date',
            'views_count'            => 'nullable|string',
            'shares_count'           => 'nullable|string',
            'statistic_number'       => 'nullable|string',
            'statistic_description'  => 'nullable|string',
            'contact_name'           => 'nullable|string',
            'contact_number'         => 'nullable|string',
        ]);

        unset($data['image'], $data['delete_image'], $data['additional_images'], $data['delete_additional_images']);
        $news->update($data);

        if ($request->hasFile('image')) {
            $news->uploadImage($request->file('image'), 'mobile/news');
        } elseif ($request->input('delete_image') == '1' && $news->image_path) {
            Storage::disk('public')->delete($news->image_path);
            $news->update(['image_path' => null]);
        }

        $currentAdditional = $news->additional_images ?? [];

        if ($request->input('delete_additional_images') == '1') {
            foreach ($currentAdditional as $path) {
                Storage::disk('public')->delete($path);
            }
            $currentAdditional = [];
            $news->update(['additional_images' => null]);
        }

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $path = $file->store('mobile/news/gallery', 'public');
                $currentAdditional[] = $path;
            }
            $news->update(['additional_images' => $currentAdditional]);
        }

        return back()->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function newsDestroy(\App\Models\MobileNews $news)
    {
        $news->delete();
        return back()->with('success', 'News deleted');
    }

    // --- Mobile Contact Messages ---
    public function contactMessagesIndex()
    {
        $messages = \App\Models\MobileContactMessage::orderByDesc('created_at')->get();
        return view('mobile.contact_messages', compact('messages'));
    }

    public function contactMessageUpdate(Request $request, \App\Models\MobileContactMessage $message)
    {
        $message->update($request->only(['status', 'admin_notes']));
        return back()->with('success', 'Message updated');
    }

    public function contactMessageDestroy(\App\Models\MobileContactMessage $message)
    {
        $message->delete();
        return back()->with('success', 'Message deleted');
    }

    // --- Mobile Case Applications & Initial Research (البحث المبدئي) ---
    public function caseApplicationsIndex(Request $request)
    {
        $query = \App\Models\MobileCaseApplication::query();
        $type = $request->get('type');
        $status = $request->get('status');
        $search = $request->get('q');

        if ($type) {
            $query->where('case_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_phone', 'like', "%{$search}%")
                  ->orWhere('applicant_id_number', 'like', "%{$search}%")
                  ->orWhere('submitter_name', 'like', "%{$search}%")
                  ->orWhere('submitter_phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('mobile.case_applications', compact('applications', 'type', 'status', 'search'));
    }

    public function storeCaseApplication(Request $request)
    {
        $validated = $request->validate([
            'submitter_type' => 'nullable|string|in:self,other',
            'submitter_name' => 'nullable|string|max:255',
            'submitter_phone' => 'nullable|string|max:30',
            'submitter_relation' => 'nullable|string|max:100',
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:30',
            'backup_phone' => 'nullable|string|max:30',
            'applicant_id_number' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'case_type' => 'nullable|string|max:100',
            'governorate' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'description' => 'required|string|max:3000',
            'status' => 'nullable|string|in:pending,under_research,approved,completed,rejected',
            'admin_notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:1000',
            'source_name' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:255',
            'deceased_husband_name' => 'nullable|string|max:255',
            'submitter_relation_to_mother' => 'nullable|string|max:255',
            'transit_details' => 'nullable|string|max:1000',
            'death_details' => 'nullable|string|max:1000',
            'children_count' => 'nullable|string|max:50',
            'children_details' => 'nullable|string|max:3000',
            'income_aid_sources' => 'nullable|string|max:2000',
            'expenses_debts_details' => 'nullable|string|max:3000',
            'need_type' => 'nullable|string|max:100',
            'family_members_count' => 'nullable|string|max:50',
            'social_status_description' => 'nullable|string|max:3000',
            'guide_name' => 'nullable|string|max:255',
            'guide_phone' => 'nullable|string|max:50',
            'hospital_name' => 'nullable|string|max:255',
            'tumor_location' => 'nullable|string|max:255',
            'treatment_protocol' => 'nullable|string|max:2000',
        ]);

        if (empty($validated['applicant_id_number']) && !empty($validated['national_id'])) {
            $validated['applicant_id_number'] = $validated['national_id'];
        }
        unset($validated['national_id']);

        $validated['source'] = 'system';
        $validated['status'] = $validated['status'] ?? 'pending';

        $app = \App\Models\MobileCaseApplication::create($validated);

        if (in_array($app->status, ['approved', 'completed'], true) && $request->filled('start_visit')) {
            return redirect()->route('visits.create', ['application_id' => $app->id])
                ->with('success', 'تم حفظ طلب البحث المبدئي وتحويل الحالة فوراً إلى استمارة البحث الاجتماعي الميداني');
        }

        return back()->with('success', 'تم تسجيل طلب البحث المبدئي بنجاح (' . $app->status_label . ')');
    }

    public function updateCaseApplicationStatus(Request $request, \App\Models\MobileCaseApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,under_research,approved,completed,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ], [
            'rejection_reason.required_if' => 'يرجى كتابة سبب الرفض لتوضيح أسباب عدم القبول في البحث المبدئي.',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $application->admin_notes,
        ];

        if ($validated['status'] === 'rejected') {
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        }

        $application->update($updateData);

        // إذا تم اختيار "تم البحث" وطلب التحويل المباشر للاستمارة الميدانية
        if (in_array($validated['status'], ['approved', 'completed'], true) && ($request->filled('start_visit') || $request->input('action') === 'start_visit')) {
            return redirect()->route('visits.create', ['application_id' => $application->id])
                ->with('success', 'تم اعتماد نتيجة البحث المبدئي (تم البحث) وتحويل الحالة إلى استمارة البحث الاجتماعي الميداني');
        }

        return back()->with('success', 'تم تحديث حالة البحث المبدئي بنجاح إلى: ' . $application->status_label);
    }

    public function destroyCaseApplication(\App\Models\MobileCaseApplication $application)
    {
        $application->delete();
        return back()->with('success', 'تم حذف طلب البحث المبدئي بنجاح');
    }

    // --- صفحة تقديم الحالات العامة على الويب ---
    public function publicApplyShow()
    {
        return view('public.apply_case');
    }

    public function publicApplyStore(Request $request)
    {
        $validated = $request->validate([
            'submitter_type' => 'required|string|in:self,other',
            'submitter_name' => 'nullable|required_if:submitter_type,other|string|max:255',
            'submitter_phone' => 'nullable|required_if:submitter_type,other|string|max:30',
            'submitter_relation' => 'nullable|string|max:100',
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:30',
            'backup_phone' => 'nullable|string|max:30',
            'applicant_id_number' => 'nullable|string|max:20',
            'case_type' => 'required|string|max:100',
            'governorate' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'description' => 'required|string|max:3000',
        ], [
            'applicant_name.required' => 'اسم المستفيد أو صاحب الحالة مطلوب.',
            'applicant_phone.required' => 'رقم هاتف المستفيد مطلوب للتواصل.',
            'case_type.required' => 'يرجى اختيار نوع المساعدة أو المشروع.',
            'description.required' => 'يرجى وصف الحالة والظروف الاجتماعية باختصار.',
        ]);

        $validated['source'] = 'website';
        $validated['status'] = 'pending';

        \App\Models\MobileCaseApplication::create($validated);

        return back()->with('success', 'تم إرسال طلب البحث المبدئي بنجاح. سيقوم فريق مؤسسة إنسان الخيرية بمراجعة الطلب والتواصل معكم.');
    }

    // --- إستمارة تقديم مشروع زاد (الأيتام) 2026 ---
    public function applyZadShow()
    {
        return view('public.apply_zad');
    }

    public function applyZadStore(Request $request)
    {
        $validated = $request->validate([
            'source_name' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'mother_name' => 'required|string|max:255',
            'deceased_husband_name' => 'required|string|max:255',
            'submitter_relation_to_mother' => 'required|string|max:150',
            'phone_1' => 'required|string|max:30',
            'phone_2' => 'nullable|string|max:30',
            'transit_details' => 'required|string|max:1000',
            'death_details' => 'required|string|max:1000',
            'children_count' => 'required|string|max:50',
            'children_details' => 'required|string|max:3000',
            'income_aid_sources' => 'required|string|max:2000',
            'notes_expenses_debts' => 'required|string|max:3000',
        ], [
            'mother_name.required' => 'اسم الأم مطلوب.',
            'deceased_husband_name.required' => 'اسم الزوج المتوفي مطلوب.',
            'submitter_relation_to_mother.required' => 'يرجى تحديد صفة مقدم الطلب للأم.',
            'phone_1.required' => 'رقم الهاتف الأساسي مطلوب.',
            'transit_details.required' => 'يرجى توضيح العنوان والموقف والمواصلة للوصول.',
            'death_details.required' => 'يرجى توضيح تاريخ وسبب الوفاة وعمل الزوج رحمة الله عليه.',
            'children_count.required' => 'يرجى اختيار عدد الأولاد (ممنوع التقديم لطفل واحد).',
            'children_details.required' => 'يرجى توضيح أسماء الأطفال وسنهم ومصاريفهم وحالتهم الصحية.',
            'income_aid_sources.required' => 'يرجى توضيح مصادر الدخل والمعاشات والمساعدات.',
            'notes_expenses_debts.required' => 'يرجى كتابة الملاحظات ومصاريف المرافق والإيجار والديون إن وجدت.',
        ]);

        $app = \App\Models\MobileCaseApplication::create([
            'applicant_name' => $validated['mother_name'],
            'mother_name' => $validated['mother_name'],
            'deceased_husband_name' => $validated['deceased_husband_name'],
            'submitter_type' => 'other',
            'submitter_name' => $validated['source_name'] ?: $validated['submitter_relation_to_mother'],
            'submitter_relation' => $validated['submitter_relation_to_mother'],
            'submitter_relation_to_mother' => $validated['submitter_relation_to_mother'],
            'source_name' => $validated['source_name'],
            'reference_number' => $validated['reference_number'],
            'applicant_phone' => $validated['phone_1'],
            'backup_phone' => $validated['phone_2'],
            'address' => $validated['transit_details'],
            'transit_details' => $validated['transit_details'],
            'death_details' => $validated['death_details'],
            'children_count' => $validated['children_count'],
            'children_details' => $validated['children_details'],
            'income_aid_sources' => $validated['income_aid_sources'],
            'expenses_debts_details' => $validated['notes_expenses_debts'],
            'case_type' => 'zad',
            'status' => 'pending',
            'source' => 'website_zad',
            'description' => "إستمارة تقديم زاد (الأيتام) 2026:\n" .
                "• اسم الأم: {$validated['mother_name']}\n" .
                "• الزوج المتوفي: {$validated['deceased_husband_name']}\n" .
                "• تفاصيل الوفاة والعمل: {$validated['death_details']}\n" .
                "• عدد الأولاد: {$validated['children_count']}\n" .
                "• الأطفال والدروس والأمراض: {$validated['children_details']}\n" .
                "• المعاش والمساعدات: {$validated['income_aid_sources']}\n" .
                "• المصاريف والإيجار والديون: {$validated['notes_expenses_debts']}\n" .
                "• المواصلات والعنوان: {$validated['transit_details']}",
        ]);

        return back()->with('success', 'تم تسجيل إستمارة تقديم زاد (الأيتام) بنجاح برقم قيد مبدئي #' . $app->id . '. سيقوم فريق مؤسسة إنسان الخيرية بمراجعة البيانات والتواصل معكم لترتيب البحث الميداني.');
    }

    public function applyMidrarShow()
    {
        return view('public.apply_midrar');
    }

    public function applyMidrarStore(Request $request)
    {
        $validated = $request->validate([
            'submitter_name' => 'required|string|max:255',
            'submitter_phone' => 'required|string|max:30',
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:30',
            'backup_phone' => 'nullable|string|max:30',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'family_members_count' => 'required|string|max:50',
            'social_status_description' => 'required|string|max:3000',
            'need_type' => 'required|string|max:100',
            'guide_name' => 'nullable|string|max:255',
            'guide_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:3000',
        ], [
            'submitter_name.required' => 'اسم المسجل للحالة مطلوب.',
            'submitter_phone.required' => 'رقم تليفون المسجل مطلوب.',
            'applicant_name.required' => 'اسم الحالة رباعي مطلوب.',
            'applicant_phone.required' => 'رقم تليفون الحالة مطلوب.',
            'city.required' => 'يرجى تحديد المركز التابع له.',
            'address.required' => 'العنوان بالتفصيل مطلوب.',
            'family_members_count.required' => 'يرجى إدخال عدد أفراد الأسرة.',
            'social_status_description.required' => 'يرجى كتابة وصف الحالة الاجتماعية بالتفصيل.',
            'need_type.required' => 'يرجى تحديد نوع الاحتياج (وصلة مياه، سقف، أخرى).',
        ]);

        $app = \App\Models\MobileCaseApplication::create([
            'applicant_name' => $validated['applicant_name'],
            'submitter_type' => 'other',
            'submitter_name' => $validated['submitter_name'],
            'submitter_phone' => $validated['submitter_phone'],
            'applicant_phone' => $validated['applicant_phone'],
            'backup_phone' => $validated['backup_phone'] ?? null,
            'city' => $validated['city'],
            'address' => $validated['address'],
            'family_members_count' => $validated['family_members_count'],
            'children_count' => $validated['family_members_count'],
            'social_status_description' => $validated['social_status_description'],
            'need_type' => $validated['need_type'],
            'guide_name' => $validated['guide_name'] ?? null,
            'guide_phone' => $validated['guide_phone'] ?? null,
            'admin_notes' => $validated['notes'] ?? null,
            'case_type' => 'midrar',
            'status' => 'pending',
            'source' => 'website_midrar',
            'midrar_details' => [
                'registrar_name' => $validated['submitter_name'],
                'registrar_phone' => $validated['submitter_phone'],
                'applicant_name' => $validated['applicant_name'],
                'applicant_phone' => $validated['applicant_phone'],
                'backup_phone' => $validated['backup_phone'] ?? null,
                'center' => $validated['city'],
                'address' => $validated['address'],
                'family_count' => $validated['family_members_count'],
                'social_status' => $validated['social_status_description'],
                'need_type' => $validated['need_type'],
                'guide_name' => $validated['guide_name'] ?? null,
                'guide_phone' => $validated['guide_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
            'description' => "إستمارة تقديم مشروع مدرار (الصدقات الجارية والوقف الخيري):\n" .
                "• المسجل للحالة: {$validated['submitter_name']} (هاتف: {$validated['submitter_phone']})\n" .
                "• اسم الحالة: {$validated['applicant_name']} (هاتف: {$validated['applicant_phone']})\n" .
                "• نوع الاحتياج: {$validated['need_type']}\n" .
                "• عدد أفراد الأسرة: {$validated['family_members_count']}\n" .
                "• المركز والعنوان: {$validated['city']} - {$validated['address']}\n" .
                "• الحالة الاجتماعية: {$validated['social_status_description']}\n" .
                "• الدليل: " . ($validated['guide_name'] ? "{$validated['guide_name']} ({$validated['guide_phone']})" : 'لا يوجد') . "\n" .
                ($validated['notes'] ? "• ملاحظات: {$validated['notes']}" : ''),
        ]);

        return back()->with('success', 'تم تسجيل إستمارة تقديم مشروع مدرار بنجاح برقم قيد مبدئي #' . $app->id . '. سيقوم فريق مؤسسة إنسان الخيرية بمراجعة البيانات والتواصل للترتيب للبحث الميداني.');
    }

    public function applyHopeShow()
    {
        return view('public.apply_hope');
    }

    public function applyHopeStore(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'phone_1' => 'required|string|max:30',
            'phone_2' => 'nullable|string|max:30',
            'city' => 'required|string|max:255',
            'transit_details' => 'nullable|string|max:1000',
            'address' => 'required|string|max:1000',
            'hospital_name' => 'required|string|max:255',
            'tumor_location' => 'required|string|max:255',
            'treatment_protocol' => 'nullable|string|max:2000',
            'chemo_sessions_taken' => 'nullable|string|max:100',
            'chemo_sessions_remaining' => 'nullable|string|max:100',
            'radiation_sessions_taken' => 'nullable|string|max:100',
            'radiation_sessions_remaining' => 'nullable|string|max:100',
            'medical_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'id_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'notes' => 'nullable|string|max:3000',
        ], [
            'patient_name.required' => 'اسم مريض الأورام رباعياً مطلوب.',
            'phone_1.required' => 'رقم الهاتف الأساسي للتواصل مطلوب.',
            'city.required' => 'يرجى تحديد المحافظة أو المركز.',
            'address.required' => 'العنوان بالتفصيل مطلوب.',
            'hospital_name.required' => 'اسم المستشفى المتابع بها مطلوب.',
            'tumor_location.required' => 'مكان ونوع الورم مطلوب.',
        ]);

        $appData = [
            'applicant_name' => $validated['patient_name'],
            'applicant_id_number' => $validated['national_id'] ?? null,
            'submitter_name' => $validated['guardian_name'] ?: $validated['patient_name'],
            'submitter_relation' => $validated['guardian_name'] ? 'مرافق / ولي أمر' : 'المريض نفسه',
            'applicant_phone' => $validated['phone_1'],
            'backup_phone' => $validated['phone_2'] ?? null,
            'city' => $validated['city'],
            'address' => $validated['address'],
            'transit_details' => $validated['transit_details'] ?? null,
            'hospital_name' => $validated['hospital_name'],
            'tumor_location' => $validated['tumor_location'],
            'treatment_protocol' => $validated['treatment_protocol'] ?? null,
            'case_type' => 'hope',
            'status' => 'pending',
            'source' => 'website_hope',
            'admin_notes' => $validated['notes'] ?? null,
            'tumor_details' => [
                'hospital_name' => $validated['hospital_name'],
                'tumor_location' => $validated['tumor_location'],
                'treatment_protocol' => $validated['treatment_protocol'] ?? null,
                'chemo_sessions_taken' => $validated['chemo_sessions_taken'] ?? null,
                'chemo_sessions_remaining' => $validated['chemo_sessions_remaining'] ?? null,
                'radiation_sessions_taken' => $validated['radiation_sessions_taken'] ?? null,
                'radiation_sessions_remaining' => $validated['radiation_sessions_remaining'] ?? null,
            ],
            'description' => "إستمارة تقديم مشروع بعثاء الأمل (رعاية مرضى الأورام):\n" .
                "• اسم المريض: {$validated['patient_name']}\n" .
                ($validated['guardian_name'] ? "• المرافق / ولي الأمر: {$validated['guardian_name']}\n" : '') .
                "• المستشفى المتابع بها: {$validated['hospital_name']}\n" .
                "• مكان ونوع الورم: {$validated['tumor_location']}\n" .
                ($validated['treatment_protocol'] ? "• بروتوكول العلاج: {$validated['treatment_protocol']}\n" : '') .
                ($validated['chemo_sessions_taken'] || $validated['chemo_sessions_remaining'] ? "• جلسات الكيماوي: (أخذ {$validated['chemo_sessions_taken']} - متبقي {$validated['chemo_sessions_remaining']})\n" : '') .
                ($validated['radiation_sessions_taken'] || $validated['radiation_sessions_remaining'] ? "• جلسات الإشعاعي: (أخذ {$validated['radiation_sessions_taken']} - متبقي {$validated['radiation_sessions_remaining']})\n" : '') .
                "• العنوان: {$validated['city']} - {$validated['address']}\n" .
                "• أرقام التواصل: {$validated['phone_1']}" . ($validated['phone_2'] ? " / {$validated['phone_2']}" : ''),
        ];

        $app = \App\Models\MobileCaseApplication::create($appData);

        if ($request->hasFile('medical_report')) {
            $path = $request->file('medical_report')->store('case_applications/medical', 'public');
            $app->update(['medical_report_path' => $path]);
        }

        if ($request->hasFile('id_image')) {
            $path = $request->file('id_image')->store('case_applications/ids', 'public');
            $app->update(['id_image_path' => $path]);
        }

        return back()->with('success', 'تم تسجيل إستمارة تقديم مشروع بعثاء الأمل (رعاية مرضى الأورام) بنجاح برقم قيد مبدئي #' . $app->id . '. سيقوم فريق الرعاية الطبية بمراجعة التقرير والتواصل معكم.');
    }

    // --- Mobile Donations Management ---
    public function donationsIndex()
    {
        $donations = \App\Models\MobileDonation::orderByDesc('created_at')->get();
        return view('mobile.donations', compact('donations'));
    }

    public function updateDonationStatus(Request $request, \App\Models\MobileDonation $donation)
    {
        $oldStatus = $donation->status;
        $newStatus = $request->status;

        $donation->update(['status' => $newStatus]);

        // If the donation is newly verified, update the project/campaign funding stats
        if ($newStatus === 'verified' && $oldStatus !== 'verified') {
            $this->updateFundingStats($donation);
        }

        return back()->with('success', 'Donation status updated');
    }

    /**
     * Internal helper to update project or campaign current_amount based on mobile donation string
     */
    private function updateFundingStats(\App\Models\MobileDonation $donation): void
    {
        $amount = (float) $donation->donation_amount;
        $targetStr = (string) $donation->donation_for;
        $target = null;

        // 1. Try to parse "Project ID: X" or "Campaign ID: X"
        if (preg_match('/Project ID: (\d+)/i', $targetStr, $matches)) {
            $target = \App\Models\Project::withoutGlobalScopes()->find($matches[1]);
        } elseif (preg_match('/Campaign ID: (\d+)/i', $targetStr, $matches)) {
            $target = \App\Models\Campaign::find($matches[1]);
        }

        // 2. Fallback: Search by exact name
        if (!$target) {
            $target = \App\Models\Project::withoutGlobalScopes()->where('name', trim($targetStr))->first();
        }
        if (!$target) {
            $target = \App\Models\Campaign::where('name', trim($targetStr))->first();
        }

        // 3. Update the current_amount if target found
        if ($target && isset($target->current_amount)) {
            $target->current_amount += $amount;
            $target->save();
            \Log::info("Mobile funding updated for project/campaign: {$target->name} (+{$amount})");
        }
    }

    public function destroyDonation(\App\Models\MobileDonation $donation)
    {
        $donation->delete();
        return back()->with('success', 'Donation record deleted');
    }

    // --- Mobile Contact Info Management ---
    public function contactInfoIndex()
    {
        $contacts = \App\Models\MobileContactInfo::with('phones')->orderBy('sort_order')->get();
        return view('mobile.contact_info', compact('contacts'));
    }

    public function contactInfoStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string|max:30',
        ]);

        $contact = \App\Models\MobileContactInfo::create([
            'name' => $request->name,
            'sort_order' => \App\Models\MobileContactInfo::max('sort_order') + 1,
        ]);

        foreach ($request->phones as $phone) {
            $contact->phones()->create(['phone' => $phone]);
        }

        return back()->with('success', 'Contact info added successfully');
    }

    public function contactInfoUpdate(Request $request, \App\Models\MobileContactInfo $contactInfo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string|max:30',
        ]);

        $contactInfo->update(['name' => $request->name]);

        // Simple sync: delete existing and create new
        $contactInfo->phones()->delete();
        foreach ($request->phones as $phone) {
            $contactInfo->phones()->create(['phone' => $phone]);
        }

        return back()->with('success', 'Contact info updated successfully');
    }

    public function contactInfoDestroy(\App\Models\MobileContactInfo $contactInfo)
    {
        $contactInfo->delete();
        return back()->with('success', 'Contact info deleted successfully');
    }

    // --- Mobile Guest House Bookings (Unified) ---
    public function bookingsIndex()
    {
        $mobileBookings = \App\Models\WebRoomBooking::where('source', 'mobile')->latest()->get();
        return view('mobile.bookings', compact('mobileBookings'));
    }

    public function updateBookingStatus(Request $request, \App\Models\MobileRoomBooking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'Mobile booking status updated');
    }

    public function updateWebBookingStatus(Request $request, \App\Models\WebRoomBooking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'Web booking status updated');
    }

    public function destroyBooking(\App\Models\MobileRoomBooking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Mobile booking deleted');
    }

    public function destroyWebBooking(\App\Models\WebRoomBooking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Web booking deleted');
    }

    // --- Mobile Donor Auth Management ---
    public function mobileDonorsIndex()
    {
        $donors = \App\Models\User::where('registration_source', 'mobile')
            ->orderByDesc('created_at')
            ->get();
        return view('mobile.donors_auth', compact('donors'));
    }

    public function mobileDonorUpdate(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6',
            'active' => 'boolean'
        ]);

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'active' => $request->boolean('active', false)
        ];

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'تم تحديث بيانات المتبرع بنجاح');
    }

    public function mobileDonorDestroy(\App\Models\User $user)
    {
        $user->delete();
        return back()->with('success', 'تم حذف المتبرع بنجاح');
    }

    // --- Ensan Pillars (Integrated Services) Management ---
    public function pillarStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:ensan_pillars,slug',
            'description' => 'nullable|string',
            'icon' => 'required|any_image|max:2048',
            'cover' => 'nullable|any_image|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'cards' => 'nullable|array',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.price' => 'nullable|numeric',
            'cards.*.image' => 'nullable|any_image|max:5120'
        ]);

        $creationData = $request->except(['icon', 'cover', 'cards']);
        $creationData['is_active'] = $request->has('is_active');

        $pillar = EnsanPillar::create($creationData);

        if ($request->has('project_ids')) {
            $pillar->projects()->sync($request->project_ids);
        }
        if ($request->has('service_ids')) {
            $pillar->services()->sync($request->service_ids);
        }

        if ($request->hasFile('icon')) {
            $pillar->uploadImage($request->file('icon'), 'mobile/pillars/icons', 'icon_path');
        }
        if ($request->hasFile('cover')) {
            $pillar->uploadImage($request->file('cover'), 'mobile/pillars/covers', 'cover_path');
        }

        if ($request->has('cards') && is_array($request->cards)) {
            foreach ($request->cards as $index => $cardData) {
                if (empty($cardData['title']) && empty($cardData['description']) && empty($cardData['price']) && !$request->hasFile("cards.{$index}.image")) {
                    continue;
                }
                $card = $pillar->cards()->create([
                    'title' => $cardData['title'] ?? null,
                    'description' => $cardData['description'] ?? null,
                    'price' => $cardData['price'] ?? null,
                ]);
                if ($request->hasFile("cards.{$index}.image")) {
                    $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/pillars/cards');
                }
            }
        }

        return back()->with('success', 'تم إضافة المبادرة بنجاح');
    }

    public function pillarUpdate(Request $request, EnsanPillar $pillar)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:ensan_pillars,slug,' . $pillar->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|any_image|max:2048',
            'cover' => 'nullable|any_image|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'cards' => 'nullable|array',
            'cards.*.id' => 'nullable|integer',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.price' => 'nullable|numeric',
            'cards.*.image' => 'nullable|any_image|max:5120'
        ]);

        $updateData = $request->except(['icon', 'cover', 'cards']);
        $updateData['is_active'] = $request->has('is_active');

        $pillar->update($updateData);

        if ($request->has('project_ids')) {
            $pillar->projects()->sync($request->project_ids);
        } else {
            $pillar->projects()->detach();
        }

        if ($request->has('service_ids')) {
            $pillar->services()->sync($request->service_ids);
        } else {
            $pillar->services()->detach();
        }

        if ($request->hasFile('icon')) {
            $pillar->uploadImage($request->file('icon'), 'mobile/pillars/icons', 'icon_path');
        }
        if ($request->hasFile('cover')) {
            $pillar->uploadImage($request->file('cover'), 'mobile/pillars/covers', 'cover_path');
        }

        $submittedCardIds = [];
        if ($request->has('cards') && is_array($request->cards)) {
            foreach ($request->cards as $index => $cardData) {
                // If it's an existing card
                if (!empty($cardData['id'])) {
                    $card = $pillar->cards()->find($cardData['id']);
                    if ($card) {
                        $card->update([
                            'title' => $cardData['title'] ?? $card->title,
                            'description' => $cardData['description'] ?? $card->description,
                            'price' => $cardData['price'] ?? $card->price,
                        ]);
                        $submittedCardIds[] = $card->id;

                        if ($request->hasFile("cards.{$index}.image")) {
                            $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/pillars/cards');
                        }
                    }
                }
                // If it's a new card (skip if totally empty)
                else {
                    if (empty($cardData['title']) && empty($cardData['description']) && empty($cardData['price']) && !$request->hasFile("cards.{$index}.image")) {
                        continue;
                    }

                    $card = $pillar->cards()->create([
                        'title' => $cardData['title'] ?? null,
                        'description' => $cardData['description'] ?? null,
                        'price' => $cardData['price'] ?? null,
                    ]);
                    $submittedCardIds[] = $card->id;

                    if ($request->hasFile("cards.{$index}.image")) {
                        $card->uploadImage($request->file("cards.{$index}.image"), 'mobile/pillars/cards');
                    }
                }
            }
        }

        // Correctly identify and delete removed cards
        $cardsToDelete = $pillar->cards()->whereNotIn('id', $submittedCardIds)->get();
        foreach ($cardsToDelete as $card) {
            if ($card->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($card->image_path);
            }
            $card->delete();
        }

        return back()->with('success', 'تم تحديث المبادرة بنجاح');
    }

    public function pillarDestroy(EnsanPillar $pillar)
    {
        $pillar->delete();
        return back()->with('success', 'تم حذف المبادرة بنجاح');
    }

    public function bulkDestroyCaseApplications(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'لم يتم تحديد أي عناصر للحذف');
        }

        $applications = \App\Models\MobileCaseApplication::whereIn('id', $ids)->get();

        // Use each->delete() to ensure Model Events (and thus file deletion) are triggered
        $applications->each->delete();

        return back()->with('success', 'تم حذف الطلبات المختارة بنجاح');
    }
}
