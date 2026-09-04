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
use App\Models\EnsanPillar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

final class MobileApiController extends Controller
{
    /**
     * Get Home Page Content for Mobile App
     */
    public function getHomeContent()
    {
        $heroes = MobileHomeItem::with('cards')->where('type', 'hero')->orderBy('sort_order')->get();
        $gallery = MobileHomeItem::where('type', 'gallery')->orderBy('sort_order')->get();
        $services = MobileHomeItem::where('type', 'service')->orderBy('sort_order')->get();
        $shareItems = MobileHomeItem::where('type', 'share')->orderBy('sort_order')->get();
        $campaigns = MobileHomeItem::where('type', 'campaign')->orderBy('sort_order')->get();
        $finalSection = MobileHomeItem::where('type', 'final')->first();
        $aboutUs = MobileHomeItem::where('type', 'about_us')->first();

        // --- NEW: Integrated Services (Ensan Pillars) ---
        $pillars = EnsanPillar::with(['projects', 'services', 'cards'])->where('is_active', true)->orderBy('sort_order')->get();

        // 🛠️ Robust Formatting Helper
        $formatItem = function($item) {
            if (!$item) return null;

            // Ensure URLs are absolute and consistent
            $item->image_url = $item->image_path ? $item->getFileUrl('image_path') : null;
            $item->icon_url = $item->icon ? $item->getFileUrl('icon') : null;

            // If it's a hero, also format its cards
            if ($item->type === 'hero' && $item->relationLoaded('cards')) {
                $item->cards->map(function($card) {
                    $card->image_url = $card->image_path ? $card->getFileUrl('image_path') : null;
                    return $card;
                });
            }

            return $item;
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                'integrated_services' => $pillars->map(function($p) {
                    return [
                        'id' => $p->id,
                        'title' => $p->title,
                        'slug' => $p->slug,
                        'description' => $p->description,
                        'icon_url' => $p->icon_url,
                        'cover_url' => $p->cover_url,
                        'cards' => $p->cards->map(function($card) {
                            return [
                                'id' => $card->id,
                                'title' => $card->title,
                                'description' => $card->description,
                                'price' => $card->price,
                                'image_url' => $card->image_url,
                            ];
                        }),
                        'related_projects' => $p->projects->map(function($proj) {
                            return [
                                'id' => $proj->id,
                                'name' => $proj->name,
                                'image_url' => $proj->image_url,
                                'goal_amount' => $proj->goal_amount,
                                'current_amount' => $proj->current_amount,
                                'progress_percentage' => $proj->progress_percentage,
                            ];
                        }),
                        'related_services' => $p->services->map(function($serv) {
                            return [
                                'id' => $serv->id,
                                'title' => $serv->title,
                                'description' => $serv->description,
                                'image_url' => $serv->image_url,
                                'share_price' => $serv->share_price,
                                'cards' => $serv->cards->map(function($card) {
                                    return [
                                        'id' => $card->id,
                                        'title' => $card->title,
                                        'description' => $card->description,
                                        'image_url' => $card->image_url,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
                'gallery' => $gallery->map($formatItem),
                'services' => $services->map($formatItem),
                'share_what_you_dont_need' => $shareItems->map($formatItem),
                'seasonal_campaigns' => $campaigns->map($formatItem),
                'final_section' => $formatItem($finalSection),
                'about_us' => $aboutUs ? [
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
            ->select('id', 'name', 'mobile_content', 'image_path', 'description', 'short_description', 'goal_amount', 'current_amount')
            ->get()
            ->map(function($project) {
                $project->image_url = $project->image_path ? $project->getFileUrl('image_path') : null;
                $project->description = $project->short_description ?? $project->description;
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
    public function getNews(Request $request)
    {
        $query = \App\Models\MobileNews::orderByDesc('created_at');

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $news = $query->get()
            ->map(function($item) {
                $item->image_url = $item->getFileUrl('image_path');
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    /**
     * Get News Categories for Mobile App
     */
    public function getNewsCategories()
    {
        return response()->json([
            'status' => 'success',
            'data' => \App\Models\MobileNews::getCategories()
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
            'image' => 'nullable|any_image|max:10240',
            'category' => 'nullable|string|in:عام,حملات,تبرعات,عاجل'
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
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'id_image' => 'nullable|any_image|max:10240',
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
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'treatment_type' => 'nullable|in:chemotherapy,radiation,other',
            'sessions_count' => 'nullable|integer|min:1|max:1000',
            'notes' => 'nullable|string',
            'patient_id_file' => 'nullable|file|max:10240',
            'followup_card' => 'nullable|file|max:15360',
            'referral_letter' => 'nullable|file|max:15360'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except(['patient_id_file', 'followup_card', 'referral_letter']);
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
            if ($request->hasFile('followup_card')) {
                $booking->uploadImage($request->file('followup_card'), 'mobile/bookings', 'followup_card_path');
            }
            if ($request->hasFile('referral_letter')) {
                $booking->uploadImage($request->file('referral_letter'), 'mobile/bookings', 'medical_transfer_path');
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
    public function getNotifications(Request $request)
    {
        $query = MobileNotification::where('is_sent', true);

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $notifications = $query->orderByDesc('sent_at')
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
     * Get Notification Categories for Mobile App
     */
    public function getNotificationCategories()
    {
        return response()->json([
            'status' => 'success',
            'data' => MobileNotification::getCategories()
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
            'proof' => 'nullable|any_image|max:10240',
            'image' => 'nullable|any_image|max:10240',
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

        $data['status'] = 'pending';
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
     * Get Profile Info for Mobile App
     */
    public function getProfile(Request $request)
    {
        $user = $request->auth_user;

        if (!$user) {
            \Log::warning('Mobile API: getProfile called without auth_user in request');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        \Log::info('Mobile API: Fetching profile for user', ['id' => $user->id, 'phone' => $user->phone]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'avatar_url'  => $user->getFileUrl('profile_photo_path'),
                'is_active'   => (bool) $user->active,
                'is_employee' => (bool) $user->is_employee,
                'is_volunteer'=> (bool) $user->is_volunteer,
                'job_title'   => $user->job_title,
                'joined_at'   => $user->created_at->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Update Profile Info for Mobile App
     */
    public function updateProfile(Request $request)
    {
        \Log::info('Mobile API Profile Update Raw Input:', [
            'method' => $request->method(),
            'input' => $request->except(['avatar', 'photo']),
            'files' => array_keys($request->allFiles())
        ]);

        $user = $request->auth_user;

        if (!$user) {
            \Log::warning('Mobile API: updateProfile called without auth_user');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'nullable|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'email'  => 'nullable|email',
            'avatar' => 'nullable|file|max:10240', // Changed image to file for Flutter compatibility
            'photo'  => 'nullable|file|max:10240', // Changed image to file for Flutter compatibility
        ]);

        if ($validator->fails()) {
            \Log::warning('Mobile API Profile Update Validation Failed', [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['avatar', 'photo', 'auth_user'])
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->filled('name')) {
            $user->name = trim($request->name);
        }
        if ($request->filled('phone')) {
            $user->phone = trim($request->phone);
        }
        if ($request->filled('email')) {
            $user->email = trim($request->email);
        }

        // Handle avatar image upload (Check both avatar and photo keys)
        $avatarFile = $request->file('avatar') ?: $request->file('photo');
        if ($avatarFile) {
            try {
                // uploadImage uses getImageColumn() which returns 'profile_photo_path'
                $user->uploadImage($avatarFile, 'profiles');
            } catch (\Exception $e) {
                \Log::error('Profile avatar upload failed: ' . $e->getMessage());
            }
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'avatar_url' => $user->getFileUrl('profile_photo_path'),
            ]
        ]);
    }

    /**
     * Change Password for authenticated mobile user
     */
    public function changePassword(Request $request)
    {
        $user = $request->auth_user;

        // Handle alias: if 'password' is provided and 'new_password' is not, use 'password'
        if ($request->has('password') && !$request->filled('new_password')) {
            $request->merge(['new_password' => $request->password]);
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $currentPassword = trim($request->current_password);
        $newPassword = trim($request->new_password);

        if (!\Hash::check($currentPassword, $user->password)) {
            \Log::warning('Mobile API: Password check failed for user', [
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'فشل تغيير كلمة المرور، يرجى التأكد من كلمة المرور الحالية'
            ], 422);
        }

        $user->password = \Hash::make($newPassword);
        $user->save();

        \Log::info('Mobile API: Password changed for user', ['id' => $user->id]);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

    /**
     * Delete account for authenticated mobile user
     */
    public function deleteProfile(Request $request)
    {
        $user = $request->auth_user;

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        try {
            \Log::info('Mobile API: Deleting user account', ['id' => $user->id, 'phone' => $user->phone]);

            // Revoke all tokens
            $user->tokens()->delete();

            // Hard delete the user to allow re-registration and complete removal from login system
            $user->forceDelete();

            return response()->json([
                'status'  => 'success',
                'message' => 'تم حذف الحساب بنجاح'
            ]);
        } catch (\Exception $e) {
            \Log::error('Mobile API: Account deletion failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء حذف الحساب'
            ], 500);
        }
    }

    /**
     * Upload/Update profile photo independently
     */
    public function uploadProfilePhoto(Request $request)
    {
        $user = $request->auth_user;

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|file|max:10240',
            'photo'  => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $photoFile = $request->file('avatar') ?: $request->file('photo');

            if (!$photoFile) {
                if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'PHP does not support file uploads via PUT/PATCH directly. Please use POST with _method=PUT in the request body instead.',
                        'debug_info' => 'If you are sending multipart/form-data, PHP only populates $_FILES for POST requests.'
                    ], 400);
                }
                return response()->json(['status' => 'error', 'message' => 'No file provided'], 400);
            }

            if ($photoFile) {
                $user->uploadImage($photoFile, 'profiles');
                $user->save();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'تم تحديث الصورة الشخصية بنجاح',
                    'data'    => [
                        'avatar_url' => $user->getFileUrl('profile_photo_path')
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Mobile API: Profile photo upload failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'فشل تحميل الصورة'
        ], 500);
    }

    /**
     * Get Donation Records for a specific phone number
     */
    public function getDonations(Request $request)
    {
        $phone = $request->query('phone');
        \Log::info('Mobile API: Fetching donations for phone', ['phone' => $phone]);

        if (!$phone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number is required'
            ], 400);
        }

        $donations = \App\Models\MobileDonation::where('donor_phone', $phone)
            ->orderByDesc('created_at')
            ->get()
            ->map(function($donation) {
                // Add receipt URL if exists
                $donation->receipt_url = $donation->receipt_path ? $donation->getFileUrl('receipt_path') : null;

                // Link project or campaign image if available
                $projectName = trim((string) $donation->donation_for);
                $project = \App\Models\Project::withoutGlobalScopes()->where('name', $projectName)->first();

                if ($project && $project->image_path) {
                    $donation->donation_image_url = $project->image_url;
                } else {
                    $campaign = \App\Models\Campaign::where('name', $projectName)->first();
                    if ($campaign && $campaign->image_path) {
                        $donation->donation_image_url = $campaign->image_url;
                    } else {
                        $donation->donation_image_url = null;
                    }
                }

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

    /**
     * Get Employee Dashboard details
     */
    public function getEmployeeDashboard(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $attendance = $user->employeeAttendances()->where('date', $today)->orderBy('id', 'desc')->first();

        // Get recent payroll info
        $recentPayroll = \App\Models\Payroll::where('user_id', $user->id)->orderBy('month', 'desc')->first();

        // Current month deductions
        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
        $monthlyDeductions = \App\Models\Payroll::where('user_id', $user->id)
            ->where('month', $currentMonth)
            ->sum('deductions') ?: 0.0;

        // Calculate attendance and absence days for the current month
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $todayCarbon = \Carbon\Carbon::today();

        // Get all attendance records for the current month
        $attendances = $user->employeeAttendances()
            ->whereBetween('date', [$startOfMonth->toDateString(), $todayCarbon->toDateString()])
            ->get();

        // Group by date and sum hours worked
        $hoursPerDate = [];
        $attendanceDates = [];
        $presentDates = []; // Track dates where the user is considered present (e.g. checked in today but not yet checked out)
        foreach ($attendances as $att) {
            $dateStr = \Carbon\Carbon::parse($att->date)->toDateString();
            if (!isset($hoursPerDate[$dateStr])) {
                $hoursPerDate[$dateStr] = 0.0;
            }
            if ($att->check_in_at && $att->check_out_at) {
                $inTimeStr = \Carbon\Carbon::parse($att->check_in_at)->format('H:i');
                $outTimeStr = \Carbon\Carbon::parse($att->check_out_at)->format('H:i');
                $in = \Carbon\Carbon::parse($dateStr . ' ' . $inTimeStr);
                $out = \Carbon\Carbon::parse($dateStr . ' ' . $outTimeStr);
                $hours = $in->diffInMinutes($out) / 60.0;
                $hoursPerDate[$dateStr] += $hours;
            }

            // If they checked in today and haven't checked out yet, mark today as present
            if ($dateStr === $todayCarbon->toDateString() && $att->check_in_at && !$att->check_out_at) {
                $presentDates[$dateStr] = true;
            }
            $attendanceDates[] = $dateStr;
        }

        // Determine the start date for the calculation
        $startCalculation = $startOfMonth->copy();
        $joinDateStr = $user->join_date ?? $user->contract_start_date;
        if ($joinDateStr) {
            $joinDate = \Carbon\Carbon::parse($joinDateStr);
            if ($joinDate->isCurrentMonth()) {
                $startCalculation = $joinDate;
            }
        } else {
            // Fallback: If no join date, check if they have any attendance records this month
            if (!empty($attendanceDates)) {
                $oldestDateStr = min($attendanceDates);
                $startCalculation = \Carbon\Carbon::parse($oldestDateStr);
            } else {
                // If they have no attendance records at all, assume they start today
                $startCalculation = $todayCarbon->copy();
            }
        }

        // Get all approved leaves for the current month
        $approvedLeaves = \App\Models\Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $todayCarbon) {
                $query->whereBetween('start_date', [$startOfMonth->toDateString(), $todayCarbon->toDateString()])
                      ->orWhereBetween('end_date', [$startOfMonth->toDateString(), $todayCarbon->toDateString()])
                      ->orWhere(function ($q) use ($startOfMonth, $todayCarbon) {
                          $q->where('start_date', '<=', $startOfMonth->toDateString())
                            ->where('end_date', '>=', $todayCarbon->toDateString());
                      });
            })
            ->get();

        $fullAttendanceDaysCount = 0;
        $accumulatedPartialHours = 0.0;
        $approvedLeaveDaysCount = 0;
        $totalWorkingDaysSoFar = 0;

        $temp = $startCalculation->copy();
        while ($temp->lte($todayCarbon)) {
            $dateStr = $temp->toDateString();

            // Skip weekends (Friday and Saturday)
            if ($temp->dayOfWeek === \Carbon\Carbon::FRIDAY || $temp->dayOfWeek === \Carbon\Carbon::SATURDAY) {
                $temp->addDay();
                continue;
            }

            $totalWorkingDaysSoFar++;

            // Check if user attended on this day
            if (isset($hoursPerDate[$dateStr]) && $hoursPerDate[$dateStr] > 0) {
                $hours = $hoursPerDate[$dateStr];
                if ($hours >= 8) {
                    $fullAttendanceDaysCount++;
                } else {
                    $accumulatedPartialHours += $hours;
                }
                $temp->addDay();
                continue;
            }

            // Check if user is present today (currently working)
            if (isset($presentDates[$dateStr])) {
                $fullAttendanceDaysCount++;
                $temp->addDay();
                continue;
            }

            // Check if user had an approved leave on this day
            $onLeave = false;
            foreach ($approvedLeaves as $leave) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                if ($temp->between($start, $end)) {
                    $onLeave = true;
                    break;
                }
            }

            if ($onLeave) {
                $approvedLeaveDaysCount++;
                $temp->addDay();
                continue;
            }

            $temp->addDay();
        }

        $additionalAttendanceDays = (int) ($accumulatedPartialHours / 8.0);
        $attendanceDaysCount = $fullAttendanceDaysCount + $additionalAttendanceDays;

        // Calculate absence days: only count working days with absolutely no check-in records and no approved leaves
        $absenceDaysCount = 0;
        $tempAbs = $startCalculation->copy();
        while ($tempAbs->lte($todayCarbon)) {
            // Skip weekends (Friday and Saturday)
            if ($tempAbs->dayOfWeek === \Carbon\Carbon::FRIDAY || $tempAbs->dayOfWeek === \Carbon\Carbon::SATURDAY) {
                $tempAbs->addDay();
                continue;
            }

            $dateStr = $tempAbs->toDateString();

            // Check if there is any attendance record on this day
            $hasAttendance = $attendances->contains(function ($att) use ($dateStr) {
                return \Carbon\Carbon::parse($att->date)->toDateString() === $dateStr;
            });

            if (!$hasAttendance) {
                // Check if they were on approved leave
                $onLeave = false;
                foreach ($approvedLeaves as $leave) {
                    $start = \Carbon\Carbon::parse($leave->start_date);
                    $end = \Carbon\Carbon::parse($leave->end_date);
                    if ($tempAbs->between($start, $end)) {
                        $onLeave = true;
                        break;
                    }
                }
                if (!$onLeave) {
                    $absenceDaysCount++;
                }
            }
            $tempAbs->addDay();
        }

        // Calculate total working days in the entire current month
        $startOfMonthCarbon = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonthCarbon = \Carbon\Carbon::now()->endOfMonth();
        $totalWorkingDaysInMonth = 0;
        $monthTemp = $startOfMonthCarbon->copy();
        while ($monthTemp->lte($endOfMonthCarbon)) {
            if ($monthTemp->dayOfWeek !== \Carbon\Carbon::FRIDAY && $monthTemp->dayOfWeek !== \Carbon\Carbon::SATURDAY) {
                $totalWorkingDaysInMonth++;
            }
            $monthTemp->addDay();
        }

        // Calculate net salary: base salary minus current month deductions
        $baseSalary = (float)($user->salary ?? 0.0);
        $dynamicNetSalary = max(0.0, $baseSalary - $monthlyDeductions);

        // Calculate total hours worked today
        $todayAttendances = $user->employeeAttendances()->where('date', $today)->get();
        $todayTotalMinutes = 0;
        foreach ($todayAttendances as $att) {
            if ($att->check_in_at && $att->check_out_at) {
                $inTimeStr = \Carbon\Carbon::parse($att->check_in_at)->format('H:i');
                $outTimeStr = \Carbon\Carbon::parse($att->check_out_at)->format('H:i');
                $in = \Carbon\Carbon::parse($today . ' ' . $inTimeStr);
                $out = \Carbon\Carbon::parse($today . ' ' . $outTimeStr);
                $todayTotalMinutes += $in->diffInMinutes($out);
            }
        }

        $todayTotalMinutes = (int) $todayTotalMinutes;
        $todayHours = (int) ($todayTotalMinutes / 60);
        $todayMins = $todayTotalMinutes % 60;

        $todayHoursWorkedStr = '—';
        if ($todayTotalMinutes > 0) {
            if ($todayHours > 0 && $todayMins > 0) {
                $todayHoursWorkedStr = "$todayHours ساعة و $todayMins دقيقة";
            } else if ($todayHours > 0) {
                $todayHoursWorkedStr = "$todayHours ساعة";
            } else {
                $todayHoursWorkedStr = "$todayMins دقيقة";
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'today_attendance' => $attendance ? [
                    'checked_in' => true,
                    'check_in_at' => $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->translatedFormat('g:i a') : null,
                    'checked_out' => $attendance->check_out_at !== null,
                    'check_out_at' => $attendance->check_out_at ? \Carbon\Carbon::parse($attendance->check_out_at)->translatedFormat('g:i a') : null,
                ] : [
                    'checked_in' => false,
                    'check_in_at' => null,
                    'checked_out' => false,
                    'check_out_at' => null,
                ],
                'today_hours_worked' => $todayHoursWorkedStr,
                'avatar_url' => $user->getFileUrl('profile_photo_path'),
                'leave_balance' => (int) ($user->leave_balance ?? 0),
                'annual_leave_quota' => (int) ($user->annual_leave_quota ?? 21),
                'deductions_this_month' => (float) $monthlyDeductions,
                'net_salary_this_month' => (float) $dynamicNetSalary,
                'base_salary' => (float) ($user->salary ?? 0.0),
                'job_title' => $user->job_title ?? 'موظف',
                'department' => $user->department ?? 'إداري',
                'attendance_days' => $attendanceDaysCount,
                'absence_days' => $absenceDaysCount,
            ]
        ]);
    }

    /**
     * Register Employee Check-In
     */
    public function employeeCheckIn(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        $today = \Carbon\Carbon::today()->toDateString();

        // Find if there is an active check-in (without check-out)
        $activeAttendance = \App\Models\EmployeeAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNull('check_out_at')
            ->first();

        if ($activeAttendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'لديك تسجيل حضور نشط بالفعل اليوم. يرجى تسجيل الانصراف أولاً.'
            ], 400);
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        $photoStr = $photoPath ? "[الصورة: " . asset('storage/' . $photoPath) . "]" : "";
        $requestNotes = $request->input('notes') ? trim($request->notes) : '';
        $notes = trim(($photoStr ? $photoStr . " | " : "") . ($requestNotes ?: 'حضور من الموبايل'));

        $attendance = \App\Models\EmployeeAttendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in_at' => \Carbon\Carbon::now()->toTimeString(),
            'notes' => $notes,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الحضور بنجاح',
            'data' => [
                'checked_in' => true,
                'check_in_at' => $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->translatedFormat('g:i a') : null,
                'checked_out' => false,
                'check_out_at' => null,
            ]
        ]);
    }

    /**
     * Register Employee Check-Out
     */
    public function employeeCheckOut(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        $today = \Carbon\Carbon::today()->toDateString();

        // Find latest active check-in (without check-out)
        $attendance = \App\Models\EmployeeAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNull('check_out_at')
            ->orderBy('id', 'desc')
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم تسجيل الحضور أو لديك انصراف مسجل بالفعل لجميع الفترات النشطة اليوم.'
            ], 400);
        }

        $notes = trim($attendance->notes . " | انصراف من الموبايل");

        $attendance->update([
            'check_out_at' => \Carbon\Carbon::now()->toTimeString(),
            'notes' => $notes,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الانصراف بنجاح',
            'data' => [
                'checked_in' => true,
                'check_in_at' => $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->translatedFormat('g:i a') : null,
                'checked_out' => true,
                'check_out_at' => $attendance->check_out_at ? \Carbon\Carbon::parse($attendance->check_out_at)->translatedFormat('g:i a') : null,
            ]
        ]);
    }

    /**
     * Get Employee Attendance list
     */
    public function getEmployeeAttendance(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        // Fetch real attendance records
        $realAttendances = \App\Models\EmployeeAttendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate range limits for dynamic absence generation
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $joinDateStr = $user->join_date ?? $user->contract_start_date;
        if ($joinDateStr) {
            $joinDate = \Carbon\Carbon::parse($joinDateStr);
            if ($joinDate->isCurrentMonth()) {
                $startOfMonth = $joinDate;
            }
        }
        $today = \Carbon\Carbon::today();

        // Fetch approved leaves in this range to avoid marking leaves as absences
        $approvedLeaves = \App\Models\Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $today) {
                $query->whereBetween('start_date', [$startOfMonth->toDateString(), $today->toDateString()])
                      ->orWhereBetween('end_date', [$startOfMonth->toDateString(), $today->toDateString()])
                      ->orWhere(function ($q) use ($startOfMonth, $today) {
                          $q->where('start_date', '<=', $startOfMonth->toDateString())
                            ->where('end_date', '>=', $today->toDateString());
                      });
            })
            ->get();

        // Map real attendances
        $mappedReal = $realAttendances->map(function ($att) {
            $formattedDate = \Carbon\Carbon::parse($att->date)->translatedFormat('l، j F Y');
            $formattedCheckIn = $att->check_in_at ? \Carbon\Carbon::parse($att->check_in_at)->format('H:i') : null;
            $formattedCheckOut = $att->check_out_at ? \Carbon\Carbon::parse($att->check_out_at)->format('H:i') : null;

            return [
                'id' => $att->id,
                'user_id' => $att->user_id,
                'date' => $formattedDate,
                'check_in_at' => $formattedCheckIn,
                'check_out_at' => $formattedCheckOut,
                'notes' => $this->cleanAttendanceNotes($att->notes),
                'check_in_photo_url' => $this->extractPhotoUrl($att->notes),
                'created_at' => $att->created_at ? $att->created_at->toDateTimeString() : null,
                'updated_at' => $att->updated_at ? $att->updated_at->toDateTimeString() : null,
            ];
        });

        // Generate pseudo-absence records for days with no attendance and no approved leaves
        $absenceRecords = [];
        $temp = $startOfMonth->copy();
        while ($temp->lte($today)) {
            // Skip weekends (Friday and Saturday)
            if ($temp->dayOfWeek !== \Carbon\Carbon::FRIDAY && $temp->dayOfWeek !== \Carbon\Carbon::SATURDAY) {
                $dateStr = $temp->toDateString();

                // Check if there is any attendance record on this day
                $hasAttendance = $realAttendances->contains(function ($att) use ($dateStr) {
                    return \Carbon\Carbon::parse($att->date)->toDateString() === $dateStr;
                });

                if (!$hasAttendance) {
                    // Check if they were on approved leave on this day
                    $onLeave = false;
                    foreach ($approvedLeaves as $leave) {
                        $start = \Carbon\Carbon::parse($leave->start_date);
                        $end = \Carbon\Carbon::parse($leave->end_date);
                        if ($temp->between($start, $end)) {
                            $onLeave = true;
                            break;
                        }
                    }

                    if (!$onLeave) {
                        $absenceRecords[] = [
                            'id' => null,
                            'user_id' => $user->id,
                            'date' => $temp->translatedFormat('l، j F Y'),
                            'check_in_at' => null,
                            'check_out_at' => null,
                            'notes' => 'غياب',
                            'check_in_photo_url' => null,
                            'created_at' => $temp->copy()->endOfDay()->toDateTimeString(),
                            'updated_at' => $temp->copy()->endOfDay()->toDateTimeString(),
                        ];
                    }
                }
            }
            $temp->addDay();
        }

        // Merge and sort all records descending by created_at (most recent first)
        $combined = collect($mappedReal)->merge($absenceRecords)->sortByDesc(function ($item) {
            return $item['created_at'];
        })->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $combined
        ]);
    }

    /**
     * Get Employee Leaves
     */
    public function getEmployeeLeaves(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        // 1. Fetch real leaves from leaves table (approved/rejected/etc.)
        $realLeaves = \App\Models\Leave::where('user_id', $user->id)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'user_id' => $leave->user_id,
                    'type' => $leave->type,
                    'start_date' => $leave->start_date instanceof \Carbon\Carbon ? $leave->start_date->toDateString() : $leave->start_date,
                    'end_date' => $leave->end_date instanceof \Carbon\Carbon ? $leave->end_date->toDateString() : $leave->end_date,
                    'reason' => $leave->reason,
                    'status' => $leave->status,
                    'rejection_reason' => $leave->rejection_reason,
                    'created_at' => $leave->created_at ? $leave->created_at->toDateTimeString() : null,
                ];
            });

        // 2. Fetch pending or rejected leave requests from change_requests table
        $changeRequests = \App\Models\ChangeRequest::where('user_id', $user->id)
            ->where('model_type', \App\Models\Leave::class)
            ->where('action', 'create')
            ->whereIn('status', ['pending', 'rejected'])
            ->get()
            ->map(function ($req) {
                $payload = $req->payload;
                return [
                    'id' => 0, // Pseudo ID for mobile app
                    'user_id' => $req->user_id,
                    'type' => $payload['type'] ?? 'annual',
                    'start_date' => $payload['start_date'] ?? null,
                    'end_date' => $payload['end_date'] ?? null,
                    'reason' => $payload['reason'] ?? '',
                    'status' => $req->status, // 'pending' or 'rejected'
                    'rejection_reason' => $req->rejection_reason,
                    'created_at' => $req->created_at ? $req->created_at->toDateTimeString() : null,
                ];
            });

        // 3. Merge and sort by created_at desc
        $allLeaves = $realLeaves->concat($changeRequests)->sortByDesc(function ($item) {
            return $item['created_at'] ?? '';
        })->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $allLeaves
        ]);
    }

    /**
     * Request Employee Leave
     */
    public function requestEmployeeLeave(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        // Map casual to emergency for database enum consistency
        if ($request->input('type') === 'casual') {
            $request->merge(['type' => 'emergency']);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:annual,casual,sick,unpaid,emergency,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        if ($request->type !== 'unpaid') {
            if (($user->leave_balance ?? 0) < $days) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'رصيد إجازاتك غير كافٍ. الرصيد المتبقي: ' . ($user->leave_balance ?? 0) . ' يوم/أيام، والمدة المطلوبة: ' . $days . ' يوم/أيام.'
                ], 400);
            }
        }

        $data = $request->only(['type', 'start_date', 'end_date', 'reason']);
        $result = app(\App\Services\LeaveService::class)->createLeave($data, $user->id);

        if ($result instanceof \App\Models\ChangeRequest) {
            $payload = $result->payload;
            return response()->json([
                'status' => 'success',
                'message' => 'تم تقديم طلب الإجازة بنجاح وهو قيد المراجعة',
                'data' => [
                    'id' => 0,
                    'user_id' => $user->id,
                    'type' => $payload['type'] ?? 'annual',
                    'start_date' => $payload['start_date'] ?? null,
                    'end_date' => $payload['end_date'] ?? null,
                    'reason' => $payload['reason'] ?? '',
                    'status' => 'pending',
                    'rejection_reason' => null,
                ]
            ], 201);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تقديم طلب الإجازة بنجاح وهو قيد المراجعة',
            'data' => $result
        ], 201);
    }

    /**
     * Get Employee Payroll Payslips
     */
    public function getEmployeePayrolls(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || !$user->is_employee) {
            return response()->json(['status' => 'error', 'message' => 'غير مصرح للوصول لهذه الصفحة'], 403);
        }

        $payrolls = \App\Models\Payroll::where('user_id', $user->id)
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payrolls
        ]);
    }

    /**
     * Clean location strings from attendance notes
     */
    private function cleanAttendanceNotes($notes)
    {
        if (!$notes) return $notes;

        // Remove [الموقع: ...] pattern
        $cleaned = preg_replace('/\[الموقع:\s*[^\]]*\]/u', '', $notes);

        // Remove [الصورة: ...] pattern
        $cleaned = preg_replace('/\[الصورة:\s*[^\]]*\]/u', '', $cleaned);

        // Clean up multiple pipes and spaces
        $cleaned = preg_replace('/\s*\|\s*\|\s*/', ' | ', $cleaned);

        // Trim leading/trailing spaces and pipes
        $cleaned = trim($cleaned, " \t\n\r\0\x0B|");
        $cleaned = trim($cleaned);

        return $cleaned ?: '';
    }

    private function extractPhotoUrl($notes)
    {
        if (empty($notes)) {
            return null;
        }
        if (preg_match('/\[الصورة:\s*(https?:\/\/[^\]]+)\]/u', $notes, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
