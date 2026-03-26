<?php

/**
 * =========================================================
 *  Mobile App API Routes
 *  Base Prefix: /api/v1/mobile
 * =========================================================
 *
 * This file contains all API endpoints dedicated to the
 * Ensan Mobile Application (تطبيق إنسان).
 *
 * All routes here are PUBLIC (no auth required) unless
 * explicitly wrapped in a middleware group.
 *
 * Controller: App\Http\Controllers\Api\MobileApiController
 * =========================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileApiController;

/* |-------------------------------------------------------------------------- | INDEX — API Discovery |-------------------------------------------------------------------------- | GET /api/v1/mobile | Returns a list of all available mobile endpoints. */
Route::get('/', function () {
    return response()->json([
    'status' => 'ok',
    'name' => 'Ensan Mobile API',
    'version' => 'v1',
    'endpoints' => [
    'home' => url('/api/v1/mobile/home'),
    'about_us' => url('/api/v1/mobile/about-us'),
    'projects' => url('/api/v1/mobile/projects'),
    'campaigns' => url('/api/v1/mobile/campaigns'),
    'news_list' => url('/api/v1/mobile/news'),
    'news_create' => ['method' => 'POST', 'url' => url('/api/v1/mobile/news')],
    'volunteer_requests' => url('/api/v1/mobile/volunteer-requests'),
    'volunteer_submit' => ['method' => 'POST', 'url' => url('/api/v1/mobile/volunteer')],
    'case_application' => ['method' => 'POST', 'url' => url('/api/v1/mobile/case-application')],
    'guest_house' => ['method' => 'POST', 'url' => url('/api/v1/mobile/guest-house')],
    'notifications' => url('/api/v1/mobile/notifications'),
    'donation_submit' => ['method' => 'POST', 'url' => url('/api/v1/mobile/donation')],
    ],
    ]);
});

/* |-------------------------------------------------------------------------- | 1. Home Page Content |-------------------------------------------------------------------------- | GET /api/v1/mobile/home | Returns all home sections: Heroes, Gallery, Services (قسم خدماتنا), | Share What You Don't Need (شارك بما لا تحتاجه), | Seasonal Campaigns (حملات موسمية), Final Section, About Us (معلومات عنا) */
Route::get('/home', [MobileApiController::class , 'getHomeContent']);

/* |-------------------------------------------------------------------------- | 1.5 About Us (معلومات عنا) |-------------------------------------------------------------------------- | GET /api/v1/mobile/about-us */
Route::get('/about-us', [MobileApiController::class , 'getAboutUs']);

/* |-------------------------------------------------------------------------- | 2. Projects & Campaigns (المشاريع والحملات) |-------------------------------------------------------------------------- | GET /api/v1/mobile/projects  → List all projects enabled for mobile | GET /api/v1/mobile/campaigns → List all campaigns enabled for mobile */
Route::get('/projects', [MobileApiController::class , 'getProjects']);
Route::get('/campaigns', [MobileApiController::class , 'getCampaigns']);

/* |-------------------------------------------------------------------------- | 3. App News (أخبار التطبيق) |-------------------------------------------------------------------------- | GET  /api/v1/mobile/news → Retrieve all published news articles | POST /api/v1/mobile/news → Add a new news article | | POST Request Body: |   - title    (required|string) |   - content  (required|string) |   - image    (optional|file|image) |   - category (optional|string) */
Route::get('/news', [MobileApiController::class , 'getNews']);
Route::post('/news', [MobileApiController::class , 'storeNews']);

/* |-------------------------------------------------------------------------- | 4. Volunteer Requests (طلبات التطوع) |-------------------------------------------------------------------------- | GET  /api/v1/mobile/volunteer-requests → View all volunteer requests (inbox) | POST /api/v1/mobile/volunteer          → Submit a new volunteer request | | POST Request Body: |   - name      (required|string) |   - phone     (required|string) |   - email     (optional|email) |   - interests (optional|string) |   - message   (optional|string) */
Route::get('/volunteer-requests', [MobileApiController::class , 'getVolunteerRequests']);
Route::get('/volunteer', function () {
    return response()->json([
    'status' => 'error',
    'message' => 'This endpoint only accepts POST requests for submitting volunteer applications.',
    'required_fields' => ['name', 'phone'],
    'optional_fields' => ['email', 'interests', 'message']
    ], 405);
});
Route::post('/volunteer', [MobileApiController::class , 'submitVolunteerRequest']);


/* |-------------------------------------------------------------------------- | 5. Case Applications (تقديم لحالة مستحقة) |-------------------------------------------------------------------------- | POST /api/v1/mobile/case-application | | Supports: |   - مشروع زاد الأيتام  → case_type: "zad" |   - مشروع بعثاء الأمل  → case_type: "hope" |   - Other types         → case_type: "medical" | "financial" | etc. | | POST Request Body: |   - applicant_name    (required|string) |   - applicant_phone   (required|string) |   - case_type         (required|string)  → "zad" | "hope" | "medical" | ... |   - description       (required|string) |   - governorate       (optional|string) |   - city              (optional|string) |   - address           (optional|string) |   - id_image          (optional|file|image) |   - medical_report    (optional|file) */
Route::get('/case-application', function () {
    return response()->json([
    'status' => 'error',
    'message' => 'This endpoint only accepts POST requests for submitting case applications (Zad, Hope, etc.).',
    'required_fields' => ['applicant_name', 'applicant_phone', 'case_type', 'description'],
    'allowed_case_types' => ['zad', 'hope', 'medical', 'financial', 'education']
    ], 405);
});
Route::post('/case-application', [MobileApiController::class , 'submitCaseApplication']);

/* |-------------------------------------------------------------------------- | 6. Guest House Booking - Dar Al-Diyafa (دار الضيافة) |-------------------------------------------------------------------------- | POST /api/v1/mobile/guest-house | | POST Request Body: |   - name              (required|string) |   - phone             (required|string) |   - national_id       (required|string) |   - arrival_date      (required|date) |   - expected_duration (required|string) → "less_than_week" | "one_week" | "two_weeks" | ... |   - medical_center    (optional|string) |   - notes             (optional|string) |   - patient_id_file   (optional|file) */
Route::get('/guest-house', function () {
    return response()->json([
    'status' => 'error',
    'message' => 'This endpoint only accepts POST requests for guest house bookings.',
    'required_fields' => ['name', 'phone', 'national_id', 'arrival_date', 'expected_duration']
    ], 405);
});
Route::post('/guest-house', [MobileApiController::class , 'submitGuestHouseBooking']);

/* |-------------------------------------------------------------------------- | 7. Push Notifications (إشعارات التطبيق) |-------------------------------------------------------------------------- | GET /api/v1/mobile/notifications | Returns all sent push notifications ordered by most recent. | Each notification includes title, body, image_url, and sent_at timestamp. */
Route::get('/notifications', [MobileApiController::class , 'getNotifications']);

/* |-------------------------------------------------------------------------- | 8. Donation Submission (طلب تبرع - موبايل) |-------------------------------------------------------------------------- | POST /api/v1/mobile/donation | | POST Request Body: |   - donor_name      (required|string) |   - donor_phone     (required|string) |   - donor_address   (optional|string) |   - donation_amount (required|numeric) |   - donation_for    (required|string)  → "General", "Campaign #12", "Project #5" |   - payment_method  (required|string)  → "Cash", "Card", "Fawry", "Wallet" |   - notes           (optional|string) */
Route::get('/donation', function () {
    return response()->json([
    'status' => 'error',
    'message' => 'This endpoint only accepts POST requests for submitting donations.',
    'required_fields' => ['donor_name', 'donor_phone', 'donation_amount', 'donation_for', 'payment_method']
    ], 405);
});
Route::post('/donation', [MobileApiController::class , 'submitDonation']);

/*
|--------------------------------------------------------------------------
| 9. Contact Info (بيانات التواصل)
|--------------------------------------------------------------------------
| GET /api/v1/mobile/contact-info
*/
Route::get('/contact-info', [MobileApiController::class, 'getContactInfo']);

/*
|--------------------------------------------------------------------------
| 10. Profile Info (بيانات الملف الشخصي)
|--------------------------------------------------------------------------
| GET /api/v1/mobile/profile
*/
Route::get('/profile', [MobileApiController::class, 'getProfile']);