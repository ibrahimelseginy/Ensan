<?php

/**
 * =========================================================
 *  Mobile App API Routes
 *  Base Prefix: /api/v1/mobile
 * =========================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\EnsanPillarController;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('check-phone', [AuthController::class, 'checkPhone']);
    Route::post('login-phone', [AuthController::class, 'loginByPhone']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

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
            'volunteer_requests' => url('/api/v1/mobile/volunteer-requests'),
            'volunteer_submit' => ['method' => 'POST', 'url' => url('/api/v1/mobile/volunteer')],
            'case_application' => ['method' => 'POST', 'url' => url('/api/v1/mobile/case-application')],
            'guest_house' => ['method' => 'POST', 'url' => url('/api/v1/mobile/guest-house')],
            'notifications' => url('/api/v1/mobile/notifications'),
            'donation_submit' => ['method' => 'POST', 'url' => url('/api/v1/mobile/donation')],
            'donation_records' => url('/api/v1/mobile/donation-records'),
            'donation_details' => url('/api/v1/mobile/donation/{id}'),
        ],
    ]);
});

Route::get('/home', [MobileApiController::class , 'getHomeContent']);
Route::get('/about-us', [MobileApiController::class , 'getAboutUs']);
Route::get('/projects', [MobileApiController::class , 'getProjects']);
Route::get('/campaigns', [MobileApiController::class , 'getCampaigns']);
Route::get('/news/categories', [MobileApiController::class, 'getNewsCategories']);
Route::get('/news', [MobileApiController::class , 'getNews']);
Route::post('/news', [MobileApiController::class , 'storeNews']);
Route::get('/volunteer-requests', [MobileApiController::class , 'getVolunteerRequests']);
Route::get('/volunteer', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'This endpoint only accepts POST requests for submitting volunteer applications.',
        'required_fields' => ['name', 'phone'],
    ], 405);
});
Route::post('/volunteer', [MobileApiController::class , 'submitVolunteerRequest']);
Route::post('/case-application', [MobileApiController::class , 'submitCaseApplication']);
Route::post('/guest-house', [MobileApiController::class , 'submitGuestHouseBooking']);
Route::get('/notifications/categories', [MobileApiController::class, 'getNotificationCategories']);
Route::get('/notifications', [MobileApiController::class , 'getNotifications']);
Route::post('/donation', [MobileApiController::class , 'submitDonation']);
Route::get('/contact-info', [MobileApiController::class, 'getContactInfo']);

// Integrated Service Pillars (Zad, Midrar, etc.)
Route::get('/integrated-services', [EnsanPillarController::class, 'index']);
Route::get('/integrated-services/{slug}', [EnsanPillarController::class, 'show']);

Route::middleware([\App\Http\Middleware\TokenAuth::class])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [MobileApiController::class, 'getProfile']);
        Route::post('/', [MobileApiController::class, 'updateProfile']);
        Route::delete('/', [MobileApiController::class, 'deleteProfile']);
        Route::post('/photo', [MobileApiController::class, 'uploadProfilePhoto']);
    });
    
    Route::prefix('auth')->group(function () {
        Route::post('/change-password', [MobileApiController::class, 'changePassword']);
    });
    
    Route::get('/donation-records', [MobileApiController::class, 'getDonations']);
    Route::get('/donation/{donation}', [MobileApiController::class, 'showDonation']);
});