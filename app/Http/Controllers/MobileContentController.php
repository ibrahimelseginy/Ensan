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

    // --- Mobile Case Applications ---
    public function caseApplicationsIndex(Request $request)
    {
        $query = \App\Models\MobileCaseApplication::query();
        $type = $request->get('type');

        if ($type) {
            $query->where('case_type', $type);
        }

        $applications = $query->orderByDesc('created_at')->get();
        return view('mobile.case_applications', compact('applications', 'type'));
    }

    public function updateCaseApplicationStatus(Request $request, \App\Models\MobileCaseApplication $application)
    {
        $application->update($request->only(['status', 'admin_notes']));
        return back()->with('success', 'Application updated');
    }

    public function destroyCaseApplication(\App\Models\MobileCaseApplication $application)
    {
        $application->delete();
        return back()->with('success', 'Application deleted');
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
