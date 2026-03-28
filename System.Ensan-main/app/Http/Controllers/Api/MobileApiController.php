<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileHomeItem;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\WebNews;
use App\Models\WebVolunteerRequest;
use App\Models\MobileCaseApplication;
use App\Models\WebRoomBooking;
use App\Models\MobileNotification;
use App\Models\MobileInKindDonation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

final class MobileApiController extends Controller
{
    /**
     * Get Home Page Content for Mobile App
     */
    public function getHomeContent()
    {
        $heroes = MobileHomeItem::where('type', 'hero')->orderBy('sort_order')->get();
        $gallery = MobileHomeItem::where('type', 'gallery')->orderBy('sort_order')->get();
        $services = MobileHomeItem::where('type', 'service')->orderBy('sort_order')->get();
        $shareItems = MobileHomeItem::where('type', 'share')->orderBy('sort_order')->get();
        $campaigns = MobileHomeItem::where('type', 'campaign')->orderBy('sort_order')->get();
        $finalSection = MobileHomeItem::where('type', 'final')->first();
        $aboutUs = MobileHomeItem::where('type', 'about_us')->first();

        // Format image URLs
        $formatItem = function($item) {
            if ($item && $item->image_path) {
                $item->image_url = $item->getFileUrl('image_path');
            }
            return $item;
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                'heroes' => $heroes->map($formatItem),
                'gallery' => $gallery->map($formatItem),
                'services' => $services->map($formatItem),
                'share_what_you_dont_need' => $shareItems->map($formatItem),
                'seasonal_campaigns' => $campaigns->map($formatItem),
                'final_section' => $formatItem($finalSection),
                'about_us' => $aboutUs ? [
                    'id' => $aboutUs->id,
                    'image_url' => $aboutUs->getFileUrl('image_path')
                ] : null,
            ]
        ]);
    }

    /**
     * Get About Us for Mobile App
     */
    public function getAboutUs()
    {
        $aboutUs = MobileHomeItem::where('type', 'about_us')->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'about_us' => $aboutUs ? [
                    'id' => $aboutUs->id,
                    'image_url' => $aboutUs->getFileUrl('image_path')
                ] : null
            ]
        ]);
    }

    /**
     * Get Projects for Mobile
     */
    public function getProjects()
    {
        $projects = Project::where('show_on_mobile', true)
            ->select('id', 'name', 'mobile_content', 'image_path')
            ->get()
            ->map(function($project) {
                $project->image_url = $project->image_path ? $project->getFileUrl('image_path') : null;
                $project->goal_amount = "0.00";
                $project->current_amount = "0.00";
                return $project;
            });

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    /**
     * Get Campaigns for Mobile
     */
    public function getCampaigns()
    {
        $campaigns = Campaign::where('show_on_mobile', true)
            ->select('id', 'name', 'mobile_content', 'image_path', 'goal_amount', 'current_amount', 'end_date')
            ->get()
            ->map(function($campaign) {
                $campaign->image_url = $campaign->image_path ? $campaign->getFileUrl('image_path') : null;
                return $campaign;
            });

        return response()->json([
            'status' => 'success',
            'data' => $campaigns
        ]);
    }

    /**
     * Get App News
     */
    public function getNews()
    {
        $news = \App\Models\MobileNews::orderByDesc('created_at')
            ->get()
            ->map(function($item) {
                // Ensure image_url is returned for the mobile app dynamically based on host
                $item->image_url = $item->image_path ? url('/api/media?path=' . $item->image_path) : null;
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    /**
     * Store App News (Endpoint requested for 'Adding new news part')
     */
    public function storeNews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:10240',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title', 'content', 'category']);
        
        $news = \App\Models\MobileNews::create($data);

        if ($request->hasFile('image')) {
            $news->uploadImage($request->file('image'), 'mobile/news');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'App news created successfully',
            'data' => $news
        ], 201);
    }

    /**
     * Get Volunteer Requests (Admin/Requests Box)
     */
    public function getVolunteerRequests()
    {
        $requests = \App\Models\MobileVolunteerRequest::orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }

    /**
     * Submit Volunteer Request
     */
    public function submitVolunteerRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            // Allow other fields nullable string so they pass validation
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'current_address' => 'nullable|string',
            'national_id' => 'nullable|string',
            'gender' => 'nullable|string',
            'education_level' => 'nullable|string',
            'faculty' => 'nullable|string',
            'university' => 'nullable|string',
            'current_job' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'goal' => 'nullable|string',
            'expectations' => 'nullable|string',
            'volunteer_hours' => 'nullable|string',
            'interests' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['cv', 'id_card']);
        
        // Laravel db schema for web_volunteer_requests seems to require email. Set default.
        if (empty($data['email'])) {
            $data['email'] = 'no-email@example.com'; 
        }

        if (array_key_exists('previous_experience', $data)) {
            // The frontend might send "yes"/"no", map it to something the DB likes, likely a tinyint/boolean
            if (strtolower($data['previous_experience']) === 'yes') {
                $data['previous_experience'] = '1';
            } elseif (strtolower($data['previous_experience']) === 'no') {
                $data['previous_experience'] = '0';
            }
        }

        // Map mobile API 'interests' field to DB 'area_of_interest'
        if (isset($data['interests'])) {
            $data['area_of_interest'] = $data['interests'];
            unset($data['interests']);
        }

        $volunteerRequest = \App\Models\MobileVolunteerRequest::create($data);

        if ($request->hasFile('cv')) {
            $volunteerRequest->uploadImage($request->file('cv'), 'mobile/volunteers/cv', 'cv_path');
        }

        if ($request->hasFile('id_card')) {
            $volunteerRequest->uploadImage($request->file('id_card'), 'mobile/volunteers/ids', 'id_card_path');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Volunteer request submitted successfully',
            'data' => $volunteerRequest
        ], 201);
    }

    /**
     * Submit Needy Case Application (Zad, Hope, etc.)
     */
    public function submitCaseApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:20',
            'case_type' => 'required|string|in:zad,hope,medical,financial,education',
            'description' => 'required|string',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'id_image' => 'nullable|image|max:10240',
            'medical_report' => 'nullable|file|max:15360'
        ], [
            'case_type.in' => 'The selected case type is invalid. Allowed types are: zad, hope, medical, financial, education.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except(['id_image', 'medical_report']);
            $application = MobileCaseApplication::create($data);

            if ($request->hasFile('id_image')) {
                try {
                    $application->uploadImage($request->file('id_image'), 'mobile/cases/ids', 'id_image_path');
                } catch (\Exception $e) {
                    \Log::error('ID Image upload failed: ' . $e->getMessage());
                }
            }

            if ($request->hasFile('medical_report')) {
                try {
                    $application->uploadImage($request->file('medical_report'), 'mobile/cases/reports', 'medical_report_path');
                } catch (\Exception $e) {
                    \Log::error('Medical report upload failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Case application submitted successfully',
                'data' => $application->fresh()
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Case application submission failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An internal server error occurred while processing your application. Please try again later.',
                'error_detail' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Submit Guest House Booking (Dar Al-Diyafa)
     */
    public function submitGuestHouseBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string',
            'arrival_date' => 'required|date',
            'expected_duration' => 'required|string',
            'medical_center' => 'nullable|string',
            'notes' => 'nullable|string',
            'patient_id_file' => 'nullable|file|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except(['patient_id_file']);
            $data['status'] = 'pending';
            $data['source'] = 'mobile';
            
            // Critical: Satisfaction of web_room_bookings table constraints
            // We use arrival_date as check_in and set check_out to the same for now, 
            // as the mobile app doesn't provide a range yet.
            $data['check_in'] = $request->arrival_date;
            $data['check_out'] = $request->arrival_date;
            
            // Redirect to the system-integrated model
            $booking = \App\Models\WebRoomBooking::create($data);

            if ($request->hasFile('patient_id_file')) {
                try {
                    // Note: WebRoomBooking uses 'patient_id_path' as column name
                    $booking->uploadImage($request->file('patient_id_file'), 'mobile/bookings', 'patient_id_path');
                } catch (\Exception $e) {
                    \Log::error('Patient ID File upload failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Guest house booking submitted successfully',
                'data' => $booking->fresh()
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Guest house booking submission failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An internal server error occurred while processing your booking. Please try again later.',
                'error_detail' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get Mobile Notifications
     */
    public function getNotifications()
    {
        $notifications = MobileNotification::where('is_sent', true)
            ->orderByDesc('sent_at')
            ->get()
            ->map(function($notif) {
                $notif->image_url = $notif->image_path ? $notif->getFileUrl('image_path') : null;
                return $notif;
            });

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }


    /**
     * Submit Donation (Mobile Unit)
     */
    public function submitDonation(Request $request)
    {
        // 🛠️ ذكاء اصطناعي لتحويل البيانات القادمة من الموبايل (لتجنب الـ 422)
        // إذا أرسل المبرمج حقول بأسماء مختلفة، نقوم بتوحيدها قبل الـ Validation
        $input = $request->all();
        
        // التحويلات الشائعة (Mapping)
        if (!isset($input['donor_name']) && isset($input['name'])) $input['donor_name'] = $input['name'];
        if (!isset($input['donor_phone']) && isset($input['phone'])) $input['donor_phone'] = $input['phone'];
        if (!isset($input['donor_phone']) && isset($input['phoneNumber'])) $input['donor_phone'] = $input['phoneNumber'];
        
        if (!isset($input['donation_amount']) && isset($input['amount'])) $input['donation_amount'] = $input['amount'];
        
        if (!isset($input['donation_for']) && isset($input['project_id'])) $input['donation_for'] = "Project ID: " . $input['project_id'];
        if (!isset($input['donation_for']) && isset($input['campaign_id'])) $input['donation_for'] = "Campaign ID: " . $input['campaign_id'];
        
        if (!isset($input['payment_method']) && isset($input['method'])) $input['payment_method'] = $input['method'];

        // دمج الحقول المحولة في طلب جديد لتطبيق الـ Validation عليها
        $request->merge($input);

        $validator = Validator::make($request->all(), [
            'donor_name' => 'required|string|max:255',
            'donor_phone' => 'required|string|max:20',
            'donor_address' => 'nullable|string',
            'donation_amount' => 'required|numeric|min:1',
            'donation_for' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'proof' => 'nullable|image|max:10240',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($validator->fails()) {
            \Log::warning('Mobile Donation Validation Failed:', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return response()->json([
                'status' => 'error', 
                'message' => 'بيانات التبرع غير مكتملة أو غير صالحة',
                'errors' => $validator->errors(),
                'received_data' => $request->all() // مفيد جداً للمبرمج لتصحيح الخطأ
            ], 422);
        }

        \Log::info('Donation Request Data:', $request->all());
        $data = $request->all();
        
        // Fallback for Flutter apps sending alternative keys for digital payments (Instapay / Vodafone Cash)
        if (!isset($data['account_number']) || empty($data['account_number'])) {
            $data['account_number'] = $request->input('accountNumber') ?? $request->input('sender_number') ?? $request->input('senderNumber') ?? $request->input('from_account');
        }
        if (!isset($data['account_name']) || empty($data['account_name'])) {
            $data['account_name'] = $request->input('accountName') ?? $request->input('sender_name') ?? $request->input('senderName');
        }

        $donation = \App\Models\MobileDonation::create($data);

        // Handle File Upload (Receipt/Proof)
        $file = $request->file('proof') ?: $request->file('image');
        if ($file) {
            try {
                $donation->uploadImage($file, 'mobile/donations/receipts');
            } catch (\Exception $e) {
                \Log::error('Donation receipt upload failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Donation submitted successfully',
            'data' => $donation->fresh()
        ], 201);
    }

    /**
     * Get Contact Info for Mobile App
     */
    public function getContactInfo()
    {
        $contacts = \App\Models\MobileContactInfo::with('phones')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $contacts
        ]);
    }

    /**
     * Get Profile Info for Mobile App (Currenty Guest)
     */
    public function getProfile()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => 0,
                'name' => 'Guest User',
                'email' => 'guest@ensan.app',
                'phone' => '',
                'is_verified' => false,
                'joined_at' => now()->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Get Donation Records for a specific phone number
     */
    public function getDonations(Request $request)
    {
        $phone = $request->query('phone');
        
        if (!$phone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number is required'
            ], 400);
        }

        $donations = \App\Models\MobileDonation::where('donor_phone', $phone)
            ->orWhere('phone', $phone)
            ->orderByDesc('created_at')
            ->get()
            ->map(function($donation) {
                // Add receipt URL if exists
                $donation->receipt_url = $donation->receipt_path ? $donation->getFileUrl('receipt_path') : null;
                return $donation;
            });

        return response()->json([
            'status' => 'success',
            'data' => $donations,
            'stats' => [
                'total_amount' => $donations->sum('donation_amount'),
                'total_count' => $donations->count(),
            ]
        ]);
    }

    /**
     * Get Single Donation Details
     */
    public function showDonation(\App\Models\MobileDonation $donation)
    {
        $donation->receipt_url = $donation->receipt_path ? $donation->getFileUrl('receipt_path') : null;
        
        return response()->json([
            'status' => 'success',
            'data' => $donation
        ]);
    }
}
