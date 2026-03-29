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
            'image' => 'required|image|max:5120',
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

    // --- Case Applications (e.g. needy cases applying) ---
    public function casesIndex()
    {
        $applications = MobileCaseApplication::orderByDesc('created_at')->get();
        return view('mobile.cases', compact('applications'));
    }

    public function caseUpdateStatus(Request $request, MobileCaseApplication $application)
    {
        $application->update(['status' => $request->status, 'admin_notes' => $request->admin_notes]);
        return back()->with('success', 'Case status updated');
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

        return view('mobile.home_content', compact('heroes', 'gallery', 'services', 'shareItems', 'campaigns', 'finalSection', 'aboutUs'));
    }

    public function homeContentStore(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'icon' => 'nullable|nullable', // Keep validation simple, handle as file if present
            'price' => 'nullable|numeric',
            'share_price' => 'nullable|numeric',
            'details' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'cards' => 'nullable|array',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.image' => 'nullable|image|max:5120'
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
            'image' => 'nullable|image|max:5120',
            'icon' => 'nullable|nullable',
            'price' => 'nullable|numeric',
            'share_price' => 'nullable|numeric',
            'details' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'cards' => 'nullable|array',
            'cards.*.id' => 'nullable|integer',
            'cards.*.title' => 'nullable|string',
            'cards.*.description' => 'nullable|string',
            'cards.*.image' => 'nullable|image|max:5120'
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
            'category' => 'nullable',
            'image' => 'nullable|image'
        ]);
        $news = \App\Models\MobileNews::create($data);
        if ($request->hasFile('image')) {
            $news->uploadImage($request->file('image'), 'mobile/news');
        }
        return back()->with('success', 'News added');
    }

    public function newsUpdate(Request $request, \App\Models\MobileNews $news)
    {
        $data = $request->validate([
            'title'                  => 'required|string',
            'content'                => 'required|string',
            'category'               => 'nullable|string',
            'image'                  => 'nullable|image|max:5120',
            'delete_image'           => 'nullable|boolean',
            'published_at'           => 'nullable|date',
            'views_count'            => 'nullable|string',
            'shares_count'           => 'nullable|string',
            'statistic_number'       => 'nullable|string',
            'statistic_description'  => 'nullable|string',
            'contact_name'           => 'nullable|string',
            'contact_number'         => 'nullable|string',
        ]);

        unset($data['image'], $data['delete_image']);
        $news->update($data);

        if ($request->hasFile('image')) {
            $news->uploadImage($request->file('image'), 'mobile/news');
        } elseif ($request->input('delete_image') == '1' && $news->image_path) {
            Storage::disk('public')->delete($news->image_path);
            $news->update(['image_path' => null]);
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
        $donation->update(['status' => $request->status]);
        return back()->with('success', 'Donation status updated');
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
        $mobileBookings = \App\Models\MobileRoomBooking::latest()->get();
        $webBookings = \App\Models\WebRoomBooking::latest()->get();
        return view('mobile.bookings', compact('mobileBookings', 'webBookings'));
    }

    public function updateBookingStatus(Request $request, \App\Models\MobileRoomBooking $booking)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'Mobile booking status updated');
    }

    public function updateWebBookingStatus(Request $request, \App\Models\WebRoomBooking $booking)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
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
}
