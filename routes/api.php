<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DelegateController;
use App\Http\Controllers\TravelRouteController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialClosureController;
use App\Http\Controllers\VolunteerHourController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Api\WebsiteApiController;
use App\Http\Controllers\Api\WebsiteContentController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\PublicWebsiteDonationController;
use App\Http\Controllers\Api\AdminWebsiteDonationController;
use App\Http\Controllers\Api\AdminWebsiteAccountApiController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\DonationController as ApiDonationController;
use App\Http\Controllers\Api\AdminDonationController as ApiAdminDonationController;
// Mobile API routes are in routes/mobile_api.php (loaded via bootstrap/app.php)

use App\Http\Controllers\MediaController;
use App\Http\Controllers\ImageUploadController;

Route::get('/media', [MediaController::class, 'serve'])->name('media.serve');

// === Image Upload Routes (Authenticated) ===
Route::middleware([\App\Http\Middleware\TokenAuth::class])->prefix('v1/upload')->group(function () {
    Route::post('image',   [ImageUploadController::class, 'upload'])->name('api.upload.image');
    Route::post('images',  [ImageUploadController::class, 'uploadMultiple'])->name('api.upload.images');
    Route::delete('image', [ImageUploadController::class, 'destroy'])->name('api.upload.image.destroy');
});
Route::prefix('v1')->group(function () {
    Route::get('/', function () {
            return response()->json(['status' => 'ok']);
        }
        );

        Route::post('auth/register', [AuthController::class , 'register']);
        Route::post('auth/login', [AuthController::class , 'login']);
        Route::post('auth/login-phone', [AuthController::class , 'loginByPhone']);
        Route::post('auth/verify-otp', [AuthController::class , 'verifyOtp']);
        Route::post('auth/logout', [AuthController::class , 'logout'])->middleware(\App\Http\Middleware\TokenAuth::class);

        // v1 Public Content (Anasen)
        Route::get('public/campaigns', [PublicContentController::class, 'campaigns']);
        Route::get('public/campaigns/{id}', [PublicContentController::class, 'campaignShow']);
        Route::get('public/projects', [PublicContentController::class, 'projects']);
        Route::get('public/projects/{id}', [PublicContentController::class, 'projectShow']);
        Route::get('public/categories', [PublicContentController::class, 'categories']);

        // Public Website Routes
        Route::get('website', [WebsiteApiController::class, 'landingPage']);
        Route::get('website/landing', [WebsiteApiController::class, 'landingPage']);
        Route::get('website/stats', [WebsiteApiController::class, 'getStats']);
        Route::get('website/notification', [WebsiteApiController::class, 'breakingNews']);
        Route::get('website/donation-page', [WebsiteApiController::class, 'donationPage']);
        Route::post('website/donate', [PublicWebsiteDonationController::class, 'submit']);
        Route::get('website/projects', [WebsiteApiController::class, 'projects']);
        Route::get('website/projects/{project}', [WebsiteApiController::class, 'projectShow']);
        Route::get('website/campaigns', [WebsiteApiController::class, 'campaigns']);
        Route::get('website/campaigns/{campaign}', [WebsiteApiController::class, 'campaignShow']);
        Route::get('website/news', [WebsiteApiController::class, 'news']);
        Route::get('website/news/{news}', [WebsiteApiController::class, 'newsShow']);
        Route::get('website/events', [WebsiteApiController::class, 'events']);
        Route::get('website/events/{event}', [WebsiteApiController::class, 'eventShow']);
        Route::get('website/branches', [WebsiteApiController::class, 'branches']);
        Route::get('website/coverage', [WebsiteApiController::class, 'coverage']);
        Route::get('website/volunteer-wall', [WebsiteApiController::class, 'getVolunteerWall']);
        Route::get('website/board-members', [WebsiteApiController::class, 'boardMembers']);
        Route::get('website/partners', [WebsiteApiController::class, 'partners']);
        Route::get('website/testimonials', [WebsiteApiController::class, 'testimonials']);
        Route::get('website/faqs', [WebsiteApiController::class, 'getFaqs']);
        Route::get('website/features', [WebsiteApiController::class, 'features']);
        Route::get('website/sectors', [WebsiteApiController::class, 'sectors']);
        Route::get('website/volunteer', [WebsiteApiController::class, 'volunteerPage']);
        Route::get('website/guest-house', [WebsiteApiController::class, 'guestHousePage']);
        Route::get('website/contact', [WebsiteApiController::class, 'contactPage']);

        Route::post('website/volunteer', [WebsiteApiController::class, 'submitVolunteer']);
        Route::post('website/contact', [WebsiteApiController::class, 'submitContact']);
        Route::post('website/room-booking', [WebsiteApiController::class, 'submitRoomBooking']);
        Route::post('website/subscribe', [WebsiteApiController::class, 'submitSubscription']);
        Route::post('website/testimonials', [WebsiteApiController::class, 'submitTestimonial']);
        Route::post('website/opinions', [WebsiteApiController::class, 'shareOpinion']);

        // Complaints — Public (no auth)
        Route::post('website/complaints', [WebsiteApiController::class, 'submitWebsiteComplaint']);
        Route::get('website/complaints/track/{code}', [WebsiteApiController::class, 'trackWebsiteComplaint']);
        Route::post('website/register', [WebsiteApiController::class, 'publicRegister']);
        Route::post('website/login', [WebsiteApiController::class, 'publicLogin']);
        Route::post('website/verify-otp', [WebsiteApiController::class, 'publicVerifyOtp']);
        Route::post('website/resend-otp', [WebsiteApiController::class, 'publicResendOtp']);
        Route::post('website/check-phone', [WebsiteApiController::class, 'publicCheckPhone']);


        // Admin Website Management
        Route::prefix('admin/website')->group(function () {
            // Complaints Admin
            Route::get('complaints', [WebsiteApiController::class, 'adminListComplaints']);
            Route::patch('complaints/{complaint}', [WebsiteApiController::class, 'adminUpdateComplaint']);

            Route::get('donation-accounts', [AdminWebsiteDonationController::class, 'index']);
            Route::get('donation-accounts/{donor}', [AdminWebsiteDonationController::class, 'donorHistory']);
            Route::put('donation-accounts/{donor}', [AdminWebsiteDonationController::class, 'updateDonor']);
            Route::get('donations', [AdminWebsiteDonationController::class, 'allDonations']);
            Route::post('donations/{donation}/verify', [AdminWebsiteDonationController::class, 'verifyDonation']);
            Route::post('donations/{donation}/reject', [AdminWebsiteDonationController::class, 'rejectDonation']);
        });

        // Authenticated Routes (Legacy/Others)
        Route::middleware([\App\Http\Middleware\TokenAuth::class , \App\Http\Middleware\RoleAccess::class , \App\Http\Middleware\AuditLogger::class])->group(function () {

            // v1 Admin Actions (Anasen)
            Route::prefix('admin')->group(function () {
                Route::post('donations/verify', [ApiAdminDonationController::class, 'verify']);
                Route::post('donations/reject', [ApiAdminDonationController::class, 'reject']);
                Route::get('donations', [ApiAdminDonationController::class, 'index']); // Review pending donations
            });

            Route::apiResource('donors', DonorController::class);
            Route::post('donations/upload-proof', [ApiDonationController::class, 'uploadProof']);
            Route::apiResource('donations', DonationController::class);
            Route::apiResource('beneficiaries', BeneficiaryController::class);
            Route::apiResource('projects', ProjectController::class);
            Route::apiResource('campaigns', CampaignController::class);
            Route::apiResource('warehouses', WarehouseController::class);
            Route::apiResource('items', ItemController::class);
            Route::apiResource('inventory-transactions', InventoryTransactionController::class);
            Route::apiResource('tasks', TaskController::class);
            Route::apiResource('accounts', AccountController::class);
            Route::apiResource('journal-entries', JournalEntryController::class);
            Route::apiResource('complaints', ComplaintController::class);
            Route::apiResource('roles', RoleController::class);
            Route::get('users/mobile-donors', [UserController::class, 'mobileDonors']);
            Route::apiResource('users', UserController::class);
            Route::apiResource('delegates', DelegateController::class);
            Route::apiResource('travel-routes', TravelRouteController::class);
            Route::apiResource('expenses', ExpenseController::class);
            Route::apiResource('volunteer-hours', VolunteerHourController::class);
            Route::apiResource('payrolls', PayrollController::class);
            Route::get('finance/closures', [FinancialClosureController::class , 'index']);
            Route::post('finance/close', [FinancialClosureController::class , 'store']);
            Route::post('finance/closures/{closure}/approve', [FinancialClosureController::class , 'approve']);
            Route::post('users/{user}/roles/{role}', [UserController::class , 'attachRole'])->name('users.attachRole');
            Route::delete('users/{user}/roles/{role}', [UserController::class , 'detachRole'])->name('users.detachRole');
            Route::get('reports/donors', [\App\Http\Controllers\ReportsController::class , 'donors'])->name('reports.donors');
            Route::get('reports/donations', [\App\Http\Controllers\ReportsController::class , 'donations'])->name('reports.donations');
            Route::get('reports/inventory', [\App\Http\Controllers\ReportsController::class , 'inventory'])->name('reports.inventory');
            Route::get('reports/beneficiaries', [\App\Http\Controllers\ReportsController::class , 'beneficiaries'])->name('reports.beneficiaries');
            Route::get('reports/finance', [\App\Http\Controllers\ReportsController::class , 'finance'])->name('reports.finance');
            Route::post('attachments', [AttachmentController::class , 'store']);
            Route::delete('attachments/{attachment}', [AttachmentController::class , 'destroy']);

            // --- Website Management (Admin) ---
            Route::prefix('admin/website')->group(function () {
                    Route::get('/settings', [WebsiteApiController::class , 'getSettings']);
                    Route::post('/settings', [WebsiteApiController::class , 'updateSettings']);
                    Route::post('/donation-page', [WebsiteApiController::class, 'updateDonationPageSettings']);

                    Route::get('/projects', [WebsiteApiController::class , 'projects']);
                    Route::post('/projects/{project}', [WebsiteApiController::class , 'updateProjectWebsite']);

                    Route::get('/campaigns', [WebsiteApiController::class , 'campaigns']);
                    Route::post('/campaigns/{campaign}', [WebsiteApiController::class , 'updateCampaignWebsite']);

                    // Section CRUD
                    Route::get('/board-members', [WebsiteApiController::class , 'boardMembers']);
                    Route::post('/board-members', [WebsiteApiController::class , 'storeBoardMember']);
                    Route::post('/board-members/{member}', [WebsiteApiController::class , 'updateBoardMember']);
                    Route::delete('/board-members/{member}', [WebsiteApiController::class , 'destroyBoardMember']);

                    Route::get('/testimonials', [WebsiteApiController::class , 'getTestimonials']);
                    Route::post('/testimonials', [WebsiteApiController::class , 'storeTestimonial']);
                    Route::post('/testimonials/{testimonial}', [WebsiteApiController::class , 'updateTestimonial']);
                    Route::delete('/testimonials/{testimonial}', [WebsiteApiController::class , 'destroyTestimonial']);

                    Route::get('/features', [WebsiteApiController::class , 'getFeatures']);
                    Route::post('/features', [WebsiteApiController::class , 'storeFeature']);
                    Route::post('/features/{feature}', [WebsiteApiController::class , 'updateFeature']);
                    Route::delete('/features/{feature}', [WebsiteApiController::class , 'destroyFeature']);

                    Route::get('/sectors', [WebsiteApiController::class , 'getSectors']);
                    Route::post('/sectors', [WebsiteApiController::class , 'storeSector']);
                    Route::post('/sectors/{sector}', [WebsiteApiController::class , 'updateSector']);
                    Route::delete('/sectors/{sector}', [WebsiteApiController::class , 'destroySector']);

                    Route::get('/branches', [WebsiteApiController::class , 'getBranches']);
                    Route::post('/branches', [WebsiteApiController::class , 'storeBranch']);
                    Route::post('/branches/{branch}', [WebsiteApiController::class , 'updateBranch']);
                    Route::delete('/branches/{branch}', [WebsiteApiController::class , 'destroyBranch']);

                    Route::get('/volunteer-wall', [WebsiteApiController::class , 'getVolunteerWall']);
                    Route::post('/volunteer-wall', [WebsiteApiController::class , 'storeVolunteerWall']);
                    Route::post('/volunteer-wall/{item}', [WebsiteApiController::class , 'updateVolunteerWall']);
                    Route::delete('/volunteer-wall/{item}', [WebsiteApiController::class , 'destroyVolunteerWall']);

                    Route::get('/partners', [WebsiteApiController::class , 'partners']);
                    Route::post('/partners', [WebsiteApiController::class , 'storePartner']);
                    Route::post('/partners/{partner}', [WebsiteApiController::class , 'updatePartner']);
                    Route::delete('/partners/{partner}', [WebsiteApiController::class , 'destroyPartner']);

                    Route::get('/news', [WebsiteApiController::class , 'news']);
                    Route::post('/news', [WebsiteApiController::class , 'storeNews']);
                    Route::post('/news/{news}', [WebsiteApiController::class , 'updateNews']);
                    Route::delete('/news/{news}', [WebsiteApiController::class , 'destroyNews']);

                    Route::get('/events', [WebsiteApiController::class , 'adminEvents']);
                    Route::post('/events', [WebsiteApiController::class , 'storeEvent']);
                    Route::post('/events/{event}', [WebsiteApiController::class , 'updateEvent']);
                    Route::delete('/events/{event}', [WebsiteApiController::class , 'destroyEvent']);

                    Route::get('/faqs', [WebsiteApiController::class , 'getFaqs']);
                    Route::post('/faqs', [WebsiteApiController::class , 'storeFaq']);
                    Route::post('/faqs/{faq}', [WebsiteApiController::class , 'updateFaq']);
                    Route::delete('/faqs/{faq}', [WebsiteApiController::class , 'destroyFaq']);

                    // Operations
                    Route::get('/contact-messages', [WebsiteApiController::class , 'getContactMessages']);
                    Route::patch('/contact-messages/{message}/read', [WebsiteApiController::class , 'markContactMessageRead']);
                    Route::delete('/contact-messages/{message}', [WebsiteApiController::class , 'destroyContactMessage']);

                    Route::get('/subscriptions', [WebsiteApiController::class , 'getSubscriptions']);
                    Route::delete('/subscriptions/{subscription}', [WebsiteApiController::class , 'destroySubscription']);

                    Route::get('/volunteer-requests', [WebsiteApiController::class , 'getVolunteerRequests']);
                    Route::delete('/volunteer-requests/{request}', [WebsiteApiController::class , 'destroyVolunteerRequest']);

                    Route::get('/room-bookings', [WebsiteApiController::class , 'getRoomBookings']);
                    Route::patch('/room-bookings/{booking}/status', [WebsiteApiController::class , 'updateRoomBookingStatus']);
                    Route::delete('/room-bookings/{booking}', [WebsiteApiController::class , 'destroyRoomBooking']);

                    // Website Content Management (Projects, Campaigns, Pages)
                    Route::controller(WebsiteContentController::class)->prefix('content')->group(function () {
                            Route::get('/projects', 'indexProjects');
                            Route::post('/projects', 'storeProject');
                            Route::get('/projects/{project}', 'showProject');
                            Route::post('/projects/{project}', 'updateProject');
                            Route::delete('/projects/{project}', 'destroyProject');

                            Route::get('/campaigns', 'indexCampaigns');
                            Route::post('/campaigns', 'storeCampaign');
                            Route::get('/campaigns/{campaign}', 'showCampaign');
                            Route::post('/campaigns/{campaign}', 'updateCampaign');
                            Route::delete('/campaigns/{campaign}', 'destroyCampaign');

                            Route::get('/pages', 'indexPages');
                            Route::post('/pages', 'storePage');
                            Route::get('/pages/{page}', 'showPage');
                            Route::post('/pages/{page}', 'updatePage');
                            Route::delete('/pages/{page}', 'destroyPage');
                });

                // Website Donation Accounts Management
                Route::prefix('donation-accounts')->group(function () {
                    Route::get('/', [AdminWebsiteDonationController::class, 'index']);
                    Route::get('/{donor}', [AdminWebsiteDonationController::class, 'donorHistory']);
                    Route::put('/{donor}', [AdminWebsiteDonationController::class, 'updateDonor']);
                    Route::post('/donations/{donation}/verify', [AdminWebsiteDonationController::class, 'verifyDonation']);
                    Route::post('/donations/{donation}/reject', [AdminWebsiteDonationController::class, 'rejectDonation']);
                });

                // Website Login Account Management (Admin)
                Route::prefix('login-accounts')->group(function () {
                    Route::get('/', [AdminWebsiteAccountApiController::class, 'index']);
                    Route::post('/', [AdminWebsiteAccountApiController::class, 'store']);
                    Route::delete('/{user}', [AdminWebsiteAccountApiController::class, 'destroy']);
                });
            });
        });
        Route::prefix('website')->group(function () {
            // --- Anasen REST API ---
            Route::prefix('auth')->group(function () {
                Route::post('login', [\App\Http\Controllers\Api\Anasen\AuthController::class, 'login']);
                Route::post('verify-otp', [\App\Http\Controllers\Api\Anasen\AuthController::class, 'verifyOtp']);
                Route::post('logout', [\App\Http\Controllers\Api\Anasen\AuthController::class, 'logout'])
                    ->middleware(\App\Http\Middleware\AnasenApiAuth::class);
            });

            // Public Donation Routes & Categories Shortcut
            Route::get('categories', [PublicContentController::class, 'categories']);
            Route::prefix('donations')->middleware('throttle:10,1')->group(function () {
                Route::get('/', [ApiDonationController::class, 'publicIndex']); // Public list
                Route::get('my', [ApiDonationController::class, 'myDonations']) // User's own donations
                    ->middleware(\App\Http\Middleware\AnasenApiAuth::class);
                
                Route::post('/', [\App\Http\Controllers\Api\Anasen\DonationController::class, 'store'])
                    ->middleware(\App\Http\Middleware\AnasenApiAuth::class);
                
                Route::post('upload-proof', [\App\Http\Controllers\Api\Anasen\DonationController::class, 'uploadProof'])
                    ->middleware(\App\Http\Middleware\AnasenApiAuth::class);
            });

            Route::middleware(\App\Http\Middleware\AnasenApiAuth::class)->group(function () {
                // Other authenticated routes
                // Profile & Dashboard
                Route::prefix('profile')->group(function () {
                    Route::get('dashboard', [WebsiteApiController::class, 'publicDonorDashboard']);
                    Route::post('update', [WebsiteApiController::class, 'publicUpdateProfile']);
                });

                // Admin
                Route::prefix('admin')->middleware(\App\Http\Middleware\AnasenAdminOnly::class)->group(function () {
                    Route::get('users', [\App\Http\Controllers\Api\Anasen\UserController::class, 'index']);
                    Route::get('users/{id}', [\App\Http\Controllers\Api\Anasen\UserController::class, 'show']);
                    Route::delete('users/{id}', [\App\Http\Controllers\Api\Anasen\UserController::class, 'destroy']);
                    Route::get('donations', [\App\Http\Controllers\Api\Anasen\AdminDonationController::class, 'index']);
                    Route::post('donations/verify', [\App\Http\Controllers\Api\Anasen\AdminDonationController::class, 'verify']);
                    Route::post('donations/reject', [\App\Http\Controllers\Api\Anasen\AdminDonationController::class, 'reject']);
                });
            });
        });
    });

// === Public: Donation Categories ===
Route::get('/donation-categories', function () {
    $categories = \App\Models\DonationCategory::with(['activeItems'])
        ->active()
        ->orderBy('sort_order')
        ->get()
        ->map(function ($cat) {
            return [
                'id'    => $cat->id,
                'name'  => $cat->name,
                'slug'  => $cat->slug,
                'items' => $cat->activeItems->map(fn($item) => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->description,
                    'icon'        => $item->icon_url,
                    'image'       => $item->image_url,
                    'sort_order'  => $item->sort_order,
                    'bg_style'    => $item->bg_style,
                ]),
            ];
        });

    return response()->json($categories);
})->name('api.donation-categories');
