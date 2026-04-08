<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\LoginWebController;

// مؤقت: تصفير كلمة المرور للمدير
Route::get('/reset-admin-pass', function () {
    $user = User::where('email', 'IbrahimElfil@gmail.com')->first();
    if ($user) {
        $user->password = Hash::make('IbrahimElfil');
        $user->save();
        return 'Password reset to IbrahimElfil successfully for ' . $user->email;
    }
    return 'User not found';
});

use App\Http\Controllers\HrEvaluationWebController;
use App\Http\Controllers\GuestHouseWebController;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\DonorWebController;
use App\Http\Controllers\DonationWebController;
use App\Http\Controllers\WarehouseWebController;
use App\Http\Controllers\ItemWebController;
use App\Http\Controllers\InventoryTransactionWebController;
use App\Http\Controllers\BeneficiaryWebController;
use App\Http\Controllers\AccountWebController;
use App\Http\Controllers\JournalEntryWebController;
use App\Http\Controllers\ReportsWebController;
use App\Http\Controllers\UserWebController;
use App\Http\Controllers\RoleWebController;
use App\Http\Controllers\ComplaintWebController;
use App\Http\Controllers\VolunteerHourWebController;
use App\Http\Controllers\PayrollWebController;
use App\Http\Controllers\VolunteerAttendanceWebController;
use App\Http\Controllers\EmployeeAttendanceWebController;
use App\Http\Controllers\TaskWebController;
use App\Http\Controllers\VolunteerTaskWebController;
use App\Http\Controllers\EmployeeTaskWebController;
use App\Http\Controllers\VolunteerWebController;
use App\Http\Controllers\ProjectWebController;
use App\Http\Controllers\CampaignWebController;
use App\Http\Controllers\ExpenseWebController;
use App\Http\Controllers\AuditWebController;
use App\Http\Controllers\NotificationWebController;
use App\Http\Controllers\FinancialClosureWebController;
use App\Http\Controllers\AttachmentWebController;
use App\Http\Controllers\DelegateWebController;
use App\Http\Controllers\TravelRouteWebController;
use App\Http\Controllers\TripWebController;
use App\Http\Controllers\TreasuryController;
use App\Http\Controllers\WebsiteWebController;
use App\Http\Controllers\MobileContentController;
use App\Http\Controllers\LogisticsDashboardController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [LoginWebController::class , 'show'])->name('login');
Route::post('login', [LoginWebController::class , 'login'])->name('login.post');
Route::post('logout', [LoginWebController::class , 'logout'])->name('logout');


// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {

    // --- Core Dashboard ---
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('dashboard', [DashboardWebController::class, 'index'])->name('dashboard.index');
        Route::get('notifications', [NotificationWebController::class, 'index'])->name('notifications.index');
    });

    Route::middleware('permission:audits.view')->group(function () {
        Route::get('audits', [AuditWebController::class, 'index'])->name('audits.index');
    });

    // --- Donors & Donations ---
    Route::middleware('permission:donors.view')->group(function () {
        Route::resource('donors', DonorWebController::class);
        Route::post('donors/bulk-destroy', [DonorWebController::class, 'bulkDestroy'])->name('donors.bulk-destroy');
    });

    Route::middleware('permission:donations.view')->group(function () {
        Route::get('donations/export', [DonationWebController::class, 'export'])->name('donations.export');
        Route::resource('donations', DonationWebController::class);
        Route::post('donations/bulk-destroy', [DonationWebController::class, 'bulkDestroy'])->name('donations.bulk-destroy');
    });

    // --- Inventory & Warehouses ---
    Route::middleware('permission:warehouses.view')->group(function () {
        Route::resource('warehouses', WarehouseWebController::class);
        Route::resource('items', ItemWebController::class);
        Route::resource('suppliers', SupplierWebController::class);
        Route::get('inventory-transactions/transfer', [InventoryTransactionWebController::class, 'createTransfer'])->name('inventory-transactions.create-transfer');
        Route::post('inventory-transactions/transfer', [InventoryTransactionWebController::class, 'storeTransfer'])->name('inventory-transactions.store-transfer');
        Route::get('inventory-transactions/reconcile', [InventoryTransactionWebController::class, 'createReconcile'])->name('inventory-transactions.create-reconcile');
        Route::post('inventory-transactions/reconcile', [InventoryTransactionWebController::class, 'storeReconcile'])->name('inventory-transactions.store-reconcile');
        Route::resource('inventory-transactions', InventoryTransactionWebController::class);
    });

    // --- Beneficiaries ---
    Route::middleware('permission:beneficiaries.view')->group(function () {
        Route::get('beneficiaries/export', [BeneficiaryWebController::class, 'export'])->name('beneficiaries.export');
        Route::post('beneficiaries/bulk', [BeneficiaryWebController::class, 'bulkUpdate'])->name('beneficiaries.bulk');
        Route::resource('beneficiaries', BeneficiaryWebController::class);
    });

    // --- Finance & Accounting ---
    Route::middleware('permission:manage_finance')->group(function () {
        Route::get('accounts/dashboard/overview', [AccountWebController::class, 'dashboard'])->name('accounts.dashboard');
        Route::resource('accounts', AccountWebController::class);
        Route::get('treasuries/sync-accounts', [TreasuryController::class, 'syncAccounts'])->name('treasuries.syncAccounts');
        Route::get('treasuries/dashboard', [TreasuryController::class, 'dashboard'])->name('treasuries.dashboard');
        Route::get('treasuries/export', [TreasuryController::class, 'export'])->name('treasuries.export');
        Route::post('treasuries/{treasury}/transactions', [TreasuryController::class, 'addTransaction'])->name('treasuries.addTransaction');
        Route::resource('treasuries', TreasuryController::class);
        Route::resource('journal-entries', JournalEntryWebController::class);
        Route::get('expenses/export', [ExpenseWebController::class, 'export'])->name('expenses.export');
        Route::resource('expenses', ExpenseWebController::class);
        Route::get('revenues', [\App\Http\Controllers\RevenueWebController::class, 'index'])->name('revenues.index');
        Route::get('finance/closures', [FinancialClosureWebController::class, 'index'])->name('closures.index');
        Route::get('finance/closures/create', [FinancialClosureWebController::class, 'create'])->name('closures.create');
        Route::post('finance/closures', [FinancialClosureWebController::class, 'store'])->name('closures.store');
        Route::get('finance/closures/{closure}', [FinancialClosureWebController::class, 'show'])->name('closures.show');
        Route::get('finance/closures/{closure}/edit', [FinancialClosureWebController::class, 'edit'])->name('closures.edit');
        Route::put('finance/closures/{closure}', [FinancialClosureWebController::class, 'update'])->name('closures.update');
        Route::delete('finance/closures/{closure}', [FinancialClosureWebController::class, 'destroy'])->name('closures.destroy');
        Route::post('finance/closures/{closure}/approve', [FinancialClosureWebController::class, 'approve'])->name('closures.approve');
    });

    // --- HR & Volunteers ---
    Route::middleware('permission:manage_volunteers_hr')->group(function () {
        Route::get('hr/dashboard', [\App\Http\Controllers\HrDashboardWebController::class, 'index'])->name('hr.dashboard');
        Route::post('attachments', [AttachmentWebController::class, 'store'])->name('attachments.store');
        Route::delete('attachments/{attachment}', [AttachmentWebController::class, 'destroy'])->name('attachments.destroy');
        
        Route::resource('volunteers', VolunteerWebController::class);
        Route::get('volunteers/reports', [VolunteerWebController::class, 'reports'])->name('volunteers.reports');
        Route::resource('volunteer-hours', VolunteerHourWebController::class);
        Route::resource('volunteer-attendance', VolunteerAttendanceWebController::class);
        Route::resource('volunteer-tasks', VolunteerTaskWebController::class);

        Route::get('hr/evaluations', [HrEvaluationWebController::class, 'index'])->name('hr.evaluations');
        Route::post('employee-attendance/check-in', [EmployeeAttendanceWebController::class, 'checkIn'])->name('employee-attendance.checkIn');
        Route::post('employee-attendance/check-out', [EmployeeAttendanceWebController::class, 'checkOut'])->name('employee-attendance.checkOut');
        Route::post('employee-attendance/bulk-destroy', [EmployeeAttendanceWebController::class, 'bulkDestroy'])->name('employee-attendance.bulk-destroy');
        Route::resource('employee-attendance', EmployeeAttendanceWebController::class);
        Route::resource('employee-tasks', EmployeeTaskWebController::class);
        
        Route::post('leaves/bulk-destroy', [\App\Http\Controllers\LeaveWebController::class, 'bulkDestroy'])->name('leaves.bulk-destroy');
        Route::resource('leaves', \App\Http\Controllers\LeaveWebController::class)->parameters(['leaves' => 'leave']);
    });

    // --- Payrolls (Specific Permission) ---
    Route::middleware('permission:payrolls.view')->group(function () {
        Route::get('payrolls/dashboard/overview', [PayrollWebController::class, 'dashboard'])->name('payrolls.dashboard');
        Route::post('payrolls/{payroll}/create-journal-entry', [PayrollWebController::class, 'createJournalEntry'])->name('payrolls.createJournalEntry');
        Route::resource('payrolls', PayrollWebController::class);
    });

    // --- Logistics ---
    Route::middleware('permission:manage_logistics')->group(function () {
        Route::get('delegates/export', [DelegateWebController::class, 'export'])->name('delegates.export');
        Route::post('delegates/bulk', [DelegateWebController::class, 'bulkUpdate'])->name('delegates.bulk');
        Route::post('delegates/{delegate}/trips', [DelegateWebController::class, 'storeTrip'])->name('delegates.storeTrip');
        Route::delete('delegates/{delegate}/trips/{trip}', [DelegateWebController::class, 'destroyTrip'])->name('delegates.destroyTrip');
        Route::patch('delegates/{delegate}/trips/{trip}', [DelegateWebController::class, 'updateTripStatus'])->name('delegates.updateTripStatus');
        Route::get('logistics/dashboard', [LogisticsDashboardController::class, 'index'])->name('logistics.dashboard');
        Route::get('logistics/delegate/{delegate}/performance', [LogisticsDashboardController::class, 'delegatePerformance'])->name('logistics.delegatePerformance');
        Route::resource('delegates', DelegateWebController::class);
        Route::post('travel-routes/{travel_route}/cities', [TravelRouteWebController::class, 'addCity'])->name('travel-routes.addCity');
        Route::post('travel-routes/{travel_route}/trips', [TravelRouteWebController::class, 'addTrip'])->name('travel-routes.addTrip');
        Route::get('travel-routes/export', [TravelRouteWebController::class, 'export'])->name('travel-routes.export');
        Route::post('travel-routes/{travel_route}/duplicate', [TravelRouteWebController::class, 'duplicate'])->name('travel-routes.duplicate');
        Route::resource('travel-routes', TravelRouteWebController::class);
        Route::get('trips', [TripWebController::class, 'index'])->name('trips.index');
        Route::post('trips', [TripWebController::class, 'store'])->name('trips.store');
    });

    Route::get('reports', [ReportsWebController::class, 'index'])->name('reports.index');

    // --- Admin & RBAC ---
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserWebController::class);
        Route::post('users/{user}/roles/{role}', [UserWebController::class, 'attachRole'])->name('users.attachRole');
        Route::delete('users/{user}/roles/{role}', [UserWebController::class, 'detachRole'])->name('users.detachRole');
    });

    Route::middleware('permission:roles.view')->group(function () {
        Route::resource('roles', RoleWebController::class);
    });

    Route::middleware('permission:complaints.view')->group(function () {
        Route::resource('complaints', ComplaintWebController::class);
    });

    // --- Projects & Campaigns ---
    Route::middleware('permission:projects.view')->group(function () {
        Route::resource('projects', ProjectWebController::class);
        Route::post('projects/bulk-destroy', [ProjectWebController::class, 'bulkDestroy'])->name('projects.bulk-destroy');
        Route::post('projects/{project}/manager', [ProjectWebController::class, 'setManager'])->name('projects.setManager');
        Route::post('projects/{project}/deputy', [ProjectWebController::class, 'setDeputy'])->name('projects.setDeputy');
        Route::post('projects/{project}/volunteers', [ProjectWebController::class, 'attachVolunteer'])->name('projects.attachVolunteer');
        Route::delete('projects/{project}/volunteers/{user}', [ProjectWebController::class, 'detachVolunteer'])->name('projects.detachVolunteer');
        
        // Zad Management
        Route::post('projects/{project}/zad-families', [ProjectWebController::class, 'storeZadFamily'])->name('projects.storeZadFamily');
        Route::put('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class, 'updateZadFamily'])->name('projects.updateZadFamily');
        Route::delete('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class, 'destroyZadFamily'])->name('projects.destroyZadFamily');
    });

    Route::middleware('permission:campaigns.view')->group(function () {
        Route::resource('campaigns', CampaignWebController::class);
        Route::post('campaigns/bulk-destroy', [CampaignWebController::class, 'bulkDestroy'])->name('campaigns.bulk-destroy');
        Route::post('campaigns/{campaign}/manager', [CampaignWebController::class, 'setManager'])->name('campaigns.setManager');
    });

    // --- Website Management ---
    Route::middleware('permission:manage_website')->group(function () {
        Route::get('admin/website', [WebsiteWebController::class, 'showSettings'])->name('website.settings.index');
        Route::post('admin/website/settings', [WebsiteWebController::class, 'updateSettings'])->name('website.settings.update');
        
        Route::get('admin/website/headquarters', [WebsiteWebController::class, 'headquarters'])->name('website.headquarters.index');
        Route::post('admin/website/headquarters', [WebsiteWebController::class, 'updateHeadquarters'])->name('website.headquarters.update');
        
        Route::resource('admin/website/branches', WebsiteWebController::class, [
            'names' => 'website.branches',
            'only' => ['store', 'update', 'destroy']
        ]);

        Route::get('admin/website/partners', [WebsiteWebController::class, 'partners'])->name('website.partners.index');
        Route::post('admin/website/partners', [WebsiteWebController::class, 'partnerStore'])->name('website.partners.store');
        Route::put('admin/website/partners/{partner}', [WebsiteWebController::class, 'partnerUpdate'])->name('website.partners.update');
        Route::delete('admin/website/partners/{partner}', [WebsiteWebController::class, 'partnerDestroy'])->name('website.partners.destroy');

        Route::get('admin/website/board', [WebsiteWebController::class, 'boardMembers'])->name('website.board.index');
        Route::post('admin/website/board', [WebsiteWebController::class, 'boardMemberStore'])->name('website.board.store');
        Route::put('admin/website/board/{member}', [WebsiteWebController::class, 'boardMemberUpdate'])->name('website.board.update');
        Route::delete('admin/website/board/{member}', [WebsiteWebController::class, 'boardMemberDestroy'])->name('website.board.destroy');

        Route::get('admin/website/pages', [WebsiteWebController::class, 'pages'])->name('website.pages.index');
        Route::post('admin/website/pages', [WebsiteWebController::class, 'pageStore'])->name('website.pages.store');
        Route::put('admin/website/pages/{page}', [WebsiteWebController::class, 'pageUpdate'])->name('website.pages.update');
        Route::delete('admin/website/pages/{page}', [WebsiteWebController::class, 'pageDestroy'])->name('website.pages.destroy');

        Route::get('admin/website/news', [WebsiteWebController::class, 'news'])->name('website.news.index');
        Route::post('admin/website/news', [WebsiteWebController::class, 'newsStore'])->name('website.news.store');
        Route::put('admin/website/news/{news}', [WebsiteWebController::class, 'newsUpdate'])->name('website.news.update');
        Route::delete('admin/website/news/{news}', [WebsiteWebController::class, 'newsDestroy'])->name('website.news.destroy');

        Route::get('admin/website/contact-messages', [WebsiteWebController::class, 'contactMessages'])->name('website.contact-messages.index');
        Route::post('admin/website/contact-messages/{message}/read', [WebsiteWebController::class, 'contactMessageMarkRead'])->name('website.contact-messages.mark-read');
        Route::delete('admin/website/contact-messages/{message}', [WebsiteWebController::class, 'contactMessageDestroy'])->name('website.contact-messages.destroy');

        Route::get('admin/website/subscriptions', [WebsiteWebController::class, 'subscriptions'])->name('website.subscriptions.index');
        Route::delete('admin/website/subscriptions/{subscription}', [WebsiteWebController::class, 'destroySubscription'])->name('website.subscriptions.destroy');

        Route::get('admin/website/volunteer-requests', [WebsiteWebController::class, 'volunteerRequests'])->name('website.volunteer-requests.index');
        Route::post('admin/website/volunteer-requests/content', [WebsiteWebController::class, 'updateVolunteerContent'])->name('website.volunteer-requests.update-content');
        Route::post('admin/website/volunteer-requests/{volunteerRequest}/status', [WebsiteWebController::class, 'updateVolunteerRequestStatus'])->name('website.volunteer-requests.update-status');
        Route::delete('admin/website/volunteer-requests/{volunteerRequest}', [WebsiteWebController::class, 'destroyVolunteerRequest'])->name('website.volunteer-requests.destroy');
        Route::get('admin/website/volunteer-requests/{volunteerRequest}/download-cv', [WebsiteWebController::class, 'downloadCV'])->name('website.volunteer-requests.download-cv');

        Route::get('admin/website/content', [WebsiteWebController::class, 'content'])->name('website.content');
        Route::post('admin/website/projects', [WebsiteWebController::class, 'storeProject'])->name('website.projects.store');
        Route::post('admin/website/projects/{project}', [WebsiteWebController::class, 'updateProjectContent'])->name('website.projects.update');
        Route::delete('admin/website/projects/{project}', [WebsiteWebController::class, 'destroyProject'])->name('website.projects.destroy');

        Route::get('admin/website/campaigns-content', [WebsiteWebController::class, 'campaignsContent'])->name('website.campaigns.content');
        Route::post('admin/website/campaigns', [WebsiteWebController::class, 'storeCampaign'])->name('website.campaigns.store');
        Route::post('admin/website/campaigns/{campaign}', [WebsiteWebController::class, 'updateCampaignContent'])->name('website.campaigns.update');
        Route::delete('admin/website/campaigns/{campaign}', [WebsiteWebController::class, 'destroyCampaign'])->name('website.campaigns.destroy');

        Route::get('admin/website/guest-house', [WebsiteWebController::class, 'guestHouseContent'])->name('website.guest-house.content');
        Route::post('admin/website/guest-house/update', [WebsiteWebController::class, 'guestHouseContentUpdate'])->name('website.guest-house.update');
        Route::post('admin/website/guest-house/stats', [WebsiteWebController::class, 'updateGuestHouseStats'])->name('website.guest-house.update-stats');
        Route::post('admin/website/guest-house/booking-status/{booking}', [WebsiteWebController::class, 'bookingUpdateStatus'])->name('website.guest-house.update-booking-status');

        Route::get('admin/website/donation-page', [WebsiteWebController::class, 'donationPage'])->name('website.donation-page.index');
        Route::post('admin/website/donation-page', [WebsiteWebController::class, 'updateDonationPage'])->name('website.donation-page.update');

        Route::get('admin/website/donation-settings/unified', [DonationCategoryWebController::class, 'unified'])->name('website.donation-settings.unified');
        Route::resource('admin/website/donation-settings/categories', DonationCategoryWebController::class, ['as' => 'website.donation-settings']);
        Route::resource('admin/website/donation-settings/items', DonationItemWebController::class, ['as' => 'website.donation-settings']);

        Route::get('admin/website/donation-accounts', [AdminWebsiteDonationWebController::class, 'index'])->name('website.donation-accounts.index');
        Route::get('admin/website/donation-accounts/{donor}', [AdminWebsiteDonationWebController::class, 'show'])->name('website.donation-accounts.show');
        Route::post('admin/website/donation-accounts/verify/{web_donation}', [AdminWebsiteDonationWebController::class, 'verifyDonation'])->name('website.donation-accounts.verify');
        Route::post('admin/website/donation-accounts/reject/{web_donation}', [AdminWebsiteDonationWebController::class, 'rejectDonation'])->name('website.donation-accounts.reject');

        Route::get('admin/website/accounts', [WebsiteWebController::class, 'accounts'])->name('website.accounts.index');
        Route::post('admin/website/accounts', [WebsiteWebController::class, 'accountStore'])->name('website.accounts.store');
        Route::put('admin/website/accounts/{user}', [WebsiteWebController::class, 'accountUpdate'])->name('website.accounts.update');
        Route::delete('admin/website/accounts/{user}', [WebsiteWebController::class, 'accountDestroy'])->name('website.accounts.destroy');
    });

    // --- Mobile App Management ---
    Route::middleware('permission:manage_mobile')->group(function () {
        Route::group(['prefix' => 'admin/mobile', 'as' => 'mobile.'], function () {
            Route::get('/', [MobileContentController::class, 'index'])->name('dashboard');
            
            Route::get('/home-content', [MobileContentController::class, 'homeContentIndex'])->name('home_content.index');
            Route::post('/home-content', [MobileContentController::class, 'homeContentStore'])->name('home_content.store');
            Route::post('/home-content/{item}', [MobileContentController::class, 'homeContentUpdate'])->name('home_content.update');
            Route::delete('/home-content/{item}', [MobileContentController::class, 'homeContentDestroy'])->name('home_content.destroy');

            Route::get('/banners', [MobileContentController::class, 'bannersIndex'])->name('banners.index');
            Route::post('/banners', [MobileContentController::class, 'bannerStore'])->name('banners.store');
            Route::delete('/banners/{banner}', [MobileContentController::class, 'bannerDestroy'])->name('banners.destroy');

            Route::get('/news', [MobileContentController::class, 'newsIndex'])->name('news.index');
            Route::post('/news', [MobileContentController::class, 'newsStore'])->name('news.store');
            Route::post('/news/{news}', [MobileContentController::class, 'newsUpdate'])->name('news.update');
            Route::delete('/news/{news}', [MobileContentController::class, 'newsDestroy'])->name('news.destroy');

            Route::get('/notifications', [MobileContentController::class, 'notificationsIndex'])->name('notifications.index');
            Route::post('/notifications', [MobileContentController::class, 'notificationStore'])->name('notifications.store');
            Route::delete('/notifications/{notification}', [MobileContentController::class, 'notificationDestroy'])->name('notifications.destroy');

            Route::get('/volunteer-requests', [MobileContentController::class, 'volunteerRequestsIndex'])->name('volunteer-requests.index');
            Route::post('/volunteer-requests/{volunteerRequest}/status', [MobileContentController::class, 'updateVolunteerRequestStatus'])->name('volunteer-requests.update-status');
            Route::delete('/volunteer-requests/{volunteerRequest}', [MobileContentController::class, 'destroyVolunteerRequest'])->name('volunteer-requests.destroy');
            Route::get('/volunteer-requests/{volunteerRequest}/download-cv', [MobileContentController::class, 'downloadVolunteerCV'])->name('volunteer-requests.download-cv');

            Route::get('/case-applications', [MobileContentController::class, 'caseApplicationsIndex'])->name('case-applications.index');
            Route::post('/case-applications/{application}/status', [MobileContentController::class, 'updateCaseApplicationStatus'])->name('case-applications.update-status');
            Route::delete('/case-applications/{application}', [MobileContentController::class, 'destroyCaseApplication'])->name('case-applications.destroy');
            Route::post('/case-applications/bulk-destroy', [MobileContentController::class, 'bulkDestroyCaseApplications'])->name('case-applications.bulk-destroy');

            Route::get('/donations', [MobileContentController::class, 'donationsIndex'])->name('donations.index');
            Route::post('/donations/{donation}/status', [MobileContentController::class, 'updateDonationStatus'])->name('donations.update-status');
            Route::delete('/donations/{donation}', [MobileContentController::class, 'destroyDonation'])->name('donations.destroy');

            Route::get('/donors-auth', [MobileContentController::class, 'mobileDonorsIndex'])->name('donors_auth.index');
            Route::post('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorUpdate'])->name('donors_auth.update');
            Route::delete('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorDestroy'])->name('donors_auth.destroy');

            Route::get('/contact-info', [MobileContentController::class, 'contactInfoIndex'])->name('contact_info.index');
            Route::post('/contact-info', [MobileContentController::class, 'contactInfoStore'])->name('contact_info.store');
            Route::post('/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoUpdate'])->name('contact_info.update');
            Route::delete('/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoDestroy'])->name('contact_info.destroy');

            Route::get('/bookings', [MobileContentController::class, 'bookingsIndex'])->name('bookings.index');
            Route::post('/bookings/{booking}/status', [MobileContentController::class, 'updateBookingStatus'])->name('bookings.update-status');
            Route::delete('/bookings/{booking}', [MobileContentController::class, 'destroyBooking'])->name('bookings.destroy');

            Route::resource('/pillars', MobileContentController::class, [
                'names' => 'pillars',
                'only' => ['store', 'update', 'destroy']
            ]);
        });
    });

    Route::middleware('permission:manage_change_requests')->group(function () {
        Route::get('admin/change-requests', [\App\Http\Controllers\ChangeRequestWebController::class, 'index'])->name('change-requests.index');
        Route::post('admin/change-requests/{changeRequest}/approve', [\App\Http\Controllers\ChangeRequestWebController::class, 'approve'])->name('change-requests.approve');
        Route::post('admin/change-requests/{changeRequest}/reject', [\App\Http\Controllers\ChangeRequestWebController::class, 'reject'])->name('change-requests.reject');
        Route::delete('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class, 'destroy'])->name('change-requests.destroy');
        Route::post('admin/change-requests/bulk-destroy', [\App\Http\Controllers\ChangeRequestWebController::class, 'bulkDestroy'])->name('change-requests.bulk-destroy');
        Route::post('admin/change-requests/bulk-revert', [\App\Http\Controllers\ChangeRequestWebController::class, 'bulkRevert'])->name('change-requests.bulk-revert');
        Route::post('admin/change-requests/{changeRequest}/revert', [\App\Http\Controllers\ChangeRequestWebController::class, 'revert'])->name('change-requests.revert');
        Route::post('admin/change-requests/{changeRequest}/cancel', [\App\Http\Controllers\ChangeRequestWebController::class, 'cancel'])->name('change-requests.cancel');
        Route::put('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class, 'update'])->name('change-requests.update');
    });

    // --- Specialized Services ---
    Route::middleware('permission:manage_specialized_services')->group(function () {
        Route::resource('workspaces', \App\Http\Controllers\WorkspaceWebController::class);
        Route::resource('guest-houses', GuestHouseWebController::class);
        Route::resource('school-collaborations', \App\Http\Controllers\SchoolCollaborationWebController::class);
        Route::resource('memberships', \App\Http\Controllers\MembershipWebController::class);
        Route::resource('oncology-medicine-reps', \App\Http\Controllers\OncologyMedicineRepWebController::class);
        Route::resource('kafr-el-sheikh-brokers', \App\Http\Controllers\KafrElSheikhBrokerWebController::class);
        Route::resource('kafr-el-sheikh-deliveries', \App\Http\Controllers\KafrElSheikhDeliveryWebController::class);
        Route::resource('kafr-el-sheikh-services', \App\Http\Controllers\KafrElSheikhServiceWebController::class);
        Route::resource('tanta-workers', \App\Http\Controllers\TantaWorkerWebController::class);
    });

    // Ramadan Campaign
    Route::middleware('permission:manage_ramadan')->group(function () {
        Route::resource('ramadan-bags', \App\Http\Controllers\RamadanBagWebController::class)->except(['destroy']);
        Route::delete('ramadan-bags/{ramadan_bag}', [\App\Http\Controllers\RamadanBagWebController::class, 'destroy'])->name('ramadan-bags.destroy');
        Route::resource('ramadan-iftars', \App\Http\Controllers\RamadanIftarWebController::class)->except(['destroy']);
        Route::delete('ramadan-iftars/{ramadan_iftar}', [\App\Http\Controllers\RamadanIftarWebController::class, 'destroy'])->name('ramadan-iftars.destroy');
    });

    // Reception & Visits (Shared/General)
    Route::resource('visits', \App\Http\Controllers\VisitWebController::class);
    Route::resource('reception', \App\Http\Controllers\ReceptionWebController::class);
    Route::resource('tasks', TaskWebController::class);

});

// Storage Link Fallback (Clear fix for 404 images on Windows)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!File::exists($fullPath)) abort(404);
    $file = File::get($fullPath);
    $type = File::mimeType($fullPath);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->where('path', '.*');
