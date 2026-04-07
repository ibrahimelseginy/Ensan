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


// Route groups and protected routes start below








Route::middleware([\App\Http\Middleware\WebAuth::class , \App\Http\Middleware\RoleAccess::class])->group(function () {
    Route::get('dashboard', [DashboardWebController::class , 'index'])->name('dashboard.index');
    Route::get('notifications', [NotificationWebController::class , 'index'])->name('notifications.index');
    Route::get('audits', [AuditWebController::class , 'index'])->name('audits.index');
    Route::resource('donors', DonorWebController::class);
    Route::get('donations/export', [DonationWebController::class , 'export'])->name('donations.export');
    Route::resource('donations', DonationWebController::class);
    Route::resource('warehouses', WarehouseWebController::class);
    Route::resource('items', ItemWebController::class);
    Route::get('inventory-transactions/transfer', [InventoryTransactionWebController::class , 'createTransfer'])->name('inventory-transactions.create-transfer');
    Route::post('inventory-transactions/transfer', [InventoryTransactionWebController::class , 'storeTransfer'])->name('inventory-transactions.store-transfer');
    Route::get('inventory-transactions/reconcile', [InventoryTransactionWebController::class , 'createReconcile'])->name('inventory-transactions.create-reconcile');
    Route::post('inventory-transactions/reconcile', [InventoryTransactionWebController::class , 'storeReconcile'])->name('inventory-transactions.store-reconcile');
    Route::resource('inventory-transactions', InventoryTransactionWebController::class);
    Route::get('beneficiaries/export', [BeneficiaryWebController::class , 'export'])->name('beneficiaries.export');
    Route::post('beneficiaries/bulk', [BeneficiaryWebController::class , 'bulkUpdate'])->name('beneficiaries.bulk');
    Route::resource('beneficiaries', BeneficiaryWebController::class);
    Route::get('accounts/dashboard/overview', [AccountWebController::class , 'dashboard'])->name('accounts.dashboard');
    Route::resource('accounts', AccountWebController::class);

    // Treasuries
    Route::get('treasuries/sync-accounts', [TreasuryController::class , 'syncAccounts'])->name('treasuries.syncAccounts');
    Route::get('treasuries/dashboard', [TreasuryController::class , 'dashboard'])->name('treasuries.dashboard');
    Route::get('treasuries/export', [TreasuryController::class , 'export'])->name('treasuries.export');
    Route::post('treasuries/{treasury}/transactions', [TreasuryController::class , 'addTransaction'])->name('treasuries.addTransaction');
    Route::resource('treasuries', TreasuryController::class);

    Route::resource('journal-entries', JournalEntryWebController::class);

    Route::get('expenses/export', [ExpenseWebController::class , 'export'])->name('expenses.export');
    Route::resource('expenses', ExpenseWebController::class);

    // Revenue & Income Analytics
    Route::get('revenues', [\App\Http\Controllers\RevenueWebController::class , 'index'])->name('revenues.index');

    // New HR Dashboard
    Route::get('hr/dashboard', [\App\Http\Controllers\HrDashboardWebController::class , 'index'])->name('hr.dashboard');

    Route::get('finance/closures', [FinancialClosureWebController::class , 'index'])->name('closures.index');
    Route::get('finance/closures/create', [FinancialClosureWebController::class , 'create'])->name('closures.create');
    Route::post('finance/closures', [FinancialClosureWebController::class , 'store'])->name('closures.store');
    Route::get('finance/closures/{closure}', [FinancialClosureWebController::class , 'show'])->name('closures.show');
    Route::get('finance/closures/{closure}/edit', [FinancialClosureWebController::class , 'edit'])->name('closures.edit');
    Route::put('finance/closures/{closure}', [FinancialClosureWebController::class , 'update'])->name('closures.update');
    Route::delete('finance/closures/{closure}', [FinancialClosureWebController::class , 'destroy'])->name('closures.destroy');
    Route::post('finance/closures/{closure}/approve', [FinancialClosureWebController::class , 'approve'])->name('closures.approve');
    Route::post('attachments', [AttachmentWebController::class , 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [AttachmentWebController::class , 'destroy'])->name('attachments.destroy');
    Route::get('delegates/export', [DelegateWebController::class , 'export'])->name('delegates.export');
    Route::post('delegates/bulk', [DelegateWebController::class , 'bulkUpdate'])->name('delegates.bulk');
    Route::post('delegates/{delegate}/trips', [DelegateWebController::class , 'storeTrip'])->name('delegates.storeTrip');
    Route::delete('delegates/{delegate}/trips/{trip}', [DelegateWebController::class , 'destroyTrip'])->name('delegates.destroyTrip');
    Route::patch('delegates/{delegate}/trips/{trip}', [DelegateWebController::class , 'updateTripStatus'])->name('delegates.updateTripStatus');

    // Logistics Dashboard & Advanced Features
    Route::get('logistics/dashboard', [LogisticsDashboardController::class , 'index'])->name('logistics.dashboard');
    Route::get('logistics/delegate/{delegate}/performance', [LogisticsDashboardController::class , 'delegatePerformance'])->name('logistics.delegatePerformance');

    Route::resource('delegates', DelegateWebController::class);
    Route::post('travel-routes/{travel_route}/cities', [TravelRouteWebController::class , 'addCity'])->name('travel-routes.addCity');
    Route::post('travel-routes/{travel_route}/trips', [TravelRouteWebController::class , 'addTrip'])->name('travel-routes.addTrip');
    Route::get('travel-routes/export', [TravelRouteWebController::class , 'export'])->name('travel-routes.export');
    Route::post('travel-routes/{travel_route}/duplicate', [TravelRouteWebController::class , 'duplicate'])->name('travel-routes.duplicate');
    Route::resource('travel-routes', TravelRouteWebController::class);
    Route::get('trips', [TripWebController::class , 'index'])->name('trips.index');
    Route::post('trips', [TripWebController::class , 'store'])->name('trips.store');
    Route::get('reports', [ReportsWebController::class , 'index'])->name('reports.index');
    Route::resource('users', UserWebController::class);
    Route::resource('roles', RoleWebController::class);
    Route::post('users/{user}/roles/{role}', [UserWebController::class , 'attachRole'])->name('users.attachRole');
    Route::delete('users/{user}/roles/{role}', [UserWebController::class , 'detachRole'])->name('users.detachRole');
    Route::resource('complaints', ComplaintWebController::class);
    Route::resource('volunteer-hours', VolunteerHourWebController::class);
    Route::get('payrolls/dashboard/overview', [PayrollWebController::class , 'dashboard'])->name('payrolls.dashboard');
    Route::post('payrolls/{payroll}/create-journal-entry', [PayrollWebController::class , 'createJournalEntry'])->name('payrolls.createJournalEntry');
    Route::resource('payrolls', PayrollWebController::class);
    Route::get('hr/evaluations', [HrEvaluationWebController::class , 'index'])->name('hr.evaluations');
    Route::resource('volunteer-attendance', VolunteerAttendanceWebController::class);
    Route::post('employee-attendance/check-in', [EmployeeAttendanceWebController::class , 'checkIn'])->name('employee-attendance.checkIn');
    Route::post('employee-attendance/check-out', [EmployeeAttendanceWebController::class , 'checkOut'])->name('employee-attendance.checkOut');

    // Bulk Actions
    Route::post('donations/bulk-destroy', [\App\Http\Controllers\DonationWebController::class , 'bulkDestroy'])->name('donations.bulk-destroy');
    Route::post('donors/bulk-destroy', [\App\Http\Controllers\DonorWebController::class , 'bulkDestroy'])->name('donors.bulk-destroy');
    Route::post('projects/bulk-destroy', [\App\Http\Controllers\ProjectWebController::class , 'bulkDestroy'])->name('projects.bulk-destroy');
    Route::post('campaigns/bulk-destroy', [\App\Http\Controllers\CampaignWebController::class , 'bulkDestroy'])->name('campaigns.bulk-destroy');
    Route::post('employee-attendance/bulk-destroy', [\App\Http\Controllers\EmployeeAttendanceWebController::class , 'bulkDestroy'])->name('employee-attendance.bulk-destroy');
    Route::post('leaves/bulk-destroy', [\App\Http\Controllers\LeaveWebController::class , 'bulkDestroy'])->name('leaves.bulk-destroy');

    Route::resource('employee-attendance', EmployeeAttendanceWebController::class);
    Route::resource('leaves', \App\Http\Controllers\LeaveWebController::class)->parameters(['leaves' => 'leave']);
    Route::resource('suppliers', \App\Http\Controllers\SupplierWebController::class);
    Route::post('suppliers/{supplier}/purchases', [\App\Http\Controllers\PurchaseWebController::class , 'store'])->name('suppliers.purchases.store');
    Route::delete('suppliers/{supplier}/purchases/{purchase}', [\App\Http\Controllers\PurchaseWebController::class , 'destroy'])->name('suppliers.purchases.destroy');

    Route::resource('visits', \App\Http\Controllers\VisitWebController::class);
    Route::resource('reception', \App\Http\Controllers\ReceptionWebController::class);

    Route::resource('tasks', TaskWebController::class);
    Route::resource('volunteer-tasks', VolunteerTaskWebController::class);
    Route::resource('employee-tasks', EmployeeTaskWebController::class);
    Route::resource('volunteers', VolunteerWebController::class);
    Route::get('volunteers/reports', [VolunteerWebController::class , 'reports'])->name('volunteers.reports');
    Route::resource('projects', ProjectWebController::class);
    Route::post('projects/{project}/manager', [ProjectWebController::class , 'setManager'])->name('projects.setManager');
    Route::post('projects/{project}/deputy', [ProjectWebController::class , 'setDeputy'])->name('projects.setDeputy');
    Route::post('projects/{project}/volunteers', [ProjectWebController::class , 'attachVolunteer'])->name('projects.attachVolunteer');
    Route::delete('projects/{project}/volunteers/{user}', [ProjectWebController::class , 'detachVolunteer'])->name('projects.detachVolunteer');
    Route::post('projects/{project}/monthly-volunteers', [ProjectWebController::class , 'storeMonthlyVolunteer'])->name('projects.storeMonthlyVolunteer');
    Route::delete('projects/{project}/monthly-volunteers/{monthlyVolunteer}', [ProjectWebController::class , 'destroyMonthlyVolunteer'])->name('projects.destroyMonthlyVolunteer');
    Route::post('projects/{project}/activities', [ProjectWebController::class , 'storeActivity'])->name('projects.storeActivity');
    Route::delete('projects/{project}/activities/{activity}', [ProjectWebController::class , 'destroyActivity'])->name('projects.destroyActivity');

    // Zad Management
    Route::post('projects/{project}/zad-families', [ProjectWebController::class , 'storeZadFamily'])->name('projects.storeZadFamily');
    Route::put('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class , 'updateZadFamily'])->name('projects.updateZadFamily');
    Route::delete('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class , 'destroyZadFamily'])->name('projects.destroyZadFamily');
    Route::post('projects/{project}/zad-resources', [ProjectWebController::class , 'storeZadResource'])->name('projects.storeZadResource');
    Route::delete('projects/{project}/zad-resources/{supplier}', [ProjectWebController::class , 'destroyZadResource'])->name('projects.destroyZadResource');
    Route::post('projects/{project}/beneficiaries-file', [ProjectWebController::class , 'storeBeneficiaryFile'])->name('projects.storeBeneficiaryFile');
    Route::put('projects/{project}/beneficiaries-file/{beneficiary}', [ProjectWebController::class , 'updateBeneficiaryFile'])->name('projects.updateBeneficiaryFile');
    Route::delete('projects/{project}/beneficiaries-file/{beneficiary}', [ProjectWebController::class , 'destroyBeneficiaryFile'])->name('projects.destroyBeneficiaryFile');
    Route::resource('campaigns', CampaignWebController::class);
    Route::post('campaigns/{campaign}/manager', [CampaignWebController::class , 'setManager'])->name('campaigns.setManager');
    Route::post('campaigns/{campaign}/volunteers', [CampaignWebController::class , 'attachVolunteer'])->name('campaigns.attachVolunteer');
    Route::delete('campaigns/{campaign}/volunteers/{user}', [CampaignWebController::class , 'detachVolunteer'])->name('campaigns.detachVolunteer');
    Route::post('campaigns/{campaign}/daily-menus', [CampaignWebController::class , 'storeDailyMenu'])->name('campaigns.storeDailyMenu');
    Route::delete('campaigns/{campaign}/daily-menus/{dailyMenu}', [CampaignWebController::class , 'destroyDailyMenu'])->name('campaigns.destroyDailyMenu');
    Route::post('campaigns/{campaign}/monthly-volunteers', [CampaignWebController::class , 'storeMonthlyVolunteer'])->name('campaigns.storeMonthlyVolunteer');
    Route::delete('campaigns/{campaign}/monthly-volunteers/{monthlyVolunteer}', [CampaignWebController::class , 'destroyMonthlyVolunteer'])->name('campaigns.destroyMonthlyVolunteer');
    Route::post('campaigns/{campaign}/beneficiaries-file', [CampaignWebController::class , 'storeBeneficiaryFile'])->name('campaigns.storeBeneficiaryFile');
    Route::put('campaigns/{campaign}/beneficiaries-file/{beneficiary}', [CampaignWebController::class , 'updateBeneficiaryFile'])->name('campaigns.updateBeneficiaryFile');
    Route::delete('campaigns/{campaign}/beneficiaries-file/{beneficiary}', [CampaignWebController::class , 'destroyBeneficiaryFile'])->name('campaigns.destroyBeneficiaryFile');
    Route::resource('workspaces', \App\Http\Controllers\WorkspaceWebController::class);
    Route::post('workspaces/{workspace}/rentals', [\App\Http\Controllers\WorkspaceWebController::class , 'storeRental'])->name('workspaces.storeRental');
    Route::patch('workspaces/{workspace}/rentals/{rental}/status', [\App\Http\Controllers\WorkspaceWebController::class , 'updateRentalStatus'])->name('workspaces.updateRentalStatus');
    Route::delete('workspaces/{workspace}/rentals/{rental}', [\App\Http\Controllers\WorkspaceWebController::class , 'destroyRental'])->name('workspaces.destroyRental');
    Route::resource('guest-houses', GuestHouseWebController::class);
    Route::post('guest-houses/{guest_house}/manager', [GuestHouseWebController::class , 'setManager'])->name('guest-houses.setManager');
    Route::post('guest-houses/{guest_house}/volunteers', [GuestHouseWebController::class , 'attachVolunteer'])->name('guest-houses.attachVolunteer');
    Route::delete('guest-houses/{guest_house}/volunteers/{user}', [GuestHouseWebController::class , 'detachVolunteer'])->name('guest-houses.detachVolunteer');
    Route::post('guest-houses/{guest_house}/monthly-volunteers', [GuestHouseWebController::class , 'storeMonthlyVolunteer'])->name('guest-houses.storeMonthlyVolunteer');
    Route::delete('guest-houses/{guest_house}/monthly-volunteers/{monthlyVolunteer}', [GuestHouseWebController::class , 'destroyMonthlyVolunteer'])->name('guest-houses.destroyMonthlyVolunteer');
    Route::get('admin/change-requests', [\App\Http\Controllers\ChangeRequestWebController::class , 'index'])->name('change-requests.index');
    // --- Website Management Unit ---
    Route::group(['prefix' => 'admin/website', 'as' => 'website.'], function () {
            Route::get('/', [WebsiteWebController::class , 'content'])->name('content');

            // Board Members
            Route::get('/board', [WebsiteWebController::class , 'boardMembers'])->name('board.index');
            Route::post('/board', [WebsiteWebController::class , 'boardMemberStore'])->name('board.store');
            Route::match (['post', 'put'], '/board/{member}', [WebsiteWebController::class , 'boardMemberUpdate'])->name('board.update');
            Route::delete('/board/{member}', [WebsiteWebController::class , 'boardMemberDestroy'])->name('board.destroy');

            // Partners
            Route::get('/partners', [WebsiteWebController::class , 'partners'])->name('partners.index');
            Route::post('/partners', [WebsiteWebController::class , 'partnerStore'])->name('partners.store');
            Route::match (['post', 'put'], '/partners/{partner}', [WebsiteWebController::class , 'partnerUpdate'])->name('partners.update');
            Route::delete('/partners/{partner}', [WebsiteWebController::class , 'partnerDestroy'])->name('partners.destroy');

            // News
            Route::get('/news', [WebsiteWebController::class , 'news'])->name('news.index');
            Route::post('/news', [WebsiteWebController::class , 'newsStore'])->name('news.store');
            Route::match (['post', 'put'], '/news/{news}', [WebsiteWebController::class , 'newsUpdate'])->name('news.update');
            Route::delete('/news/{news}', [WebsiteWebController::class , 'newsDestroy'])->name('news.destroy');

            // Volunteer Wall (Leaders of Giving)
            Route::get('/volunteer-wall', [WebsiteWebController::class , 'volunteerWall'])->name('volunteer-wall.index');
            Route::post('/volunteer-wall', [WebsiteWebController::class , 'volunteerWallStore'])->name('volunteer-wall.store');
            Route::match (['post', 'put'], '/volunteer-wall/{leader}', [WebsiteWebController::class , 'volunteerWallUpdate'])->name('volunteer-wall.update');
            Route::delete('/volunteer-wall/{leader}', [WebsiteWebController::class , 'volunteerWallDestroy'])->name('volunteer-wall.destroy');

            // Bookings
            Route::get('/bookings', [WebsiteWebController::class , 'bookings'])->name('bookings.index');
            Route::patch('/bookings/{booking}', [WebsiteWebController::class , 'bookingUpdateStatus'])->name('bookings.update');

            // Volunteer Requests & Content
            Route::get('/volunteer-requests', [WebsiteWebController::class , 'volunteerRequests'])->name('volunteer-requests.index');
            Route::patch('/volunteer-requests/{request}/status', [WebsiteWebController::class , 'updateVolunteerRequestStatus'])->name('volunteer-requests.status');
            Route::delete('/volunteer-requests/{volunteerRequest}', [WebsiteWebController::class , 'destroyVolunteerRequest'])->name('volunteer-requests.destroy');
            Route::get('/volunteer-requests/{volunteerRequest}/cv', [WebsiteWebController::class , 'downloadCV'])->name('volunteer-requests.cv');
            Route::post('/volunteer-content', [WebsiteWebController::class , 'updateVolunteerContent'])->name('volunteer-content.update');

            // Contact Messages
            Route::get('/contact-messages', [WebsiteWebController::class , 'contactMessages'])->name('contact-messages.index');
            Route::patch('/contact-messages/{message}/read', [WebsiteWebController::class , 'contactMessageMarkRead'])->name('contact-messages.read');
            Route::delete('/contact-messages/{message}', [WebsiteWebController::class , 'contactMessageDestroy'])->name('contact-messages.destroy');
            Route::post('/contact-settings', [WebsiteWebController::class , 'updateContactSettings'])->name('contact-settings.update');

            // Newsletter Subscriptions
            Route::get('/subscriptions', [WebsiteWebController::class , 'subscriptions'])->name('subscriptions.index');
            Route::delete('/subscriptions/{subscription}', [WebsiteWebController::class , 'destroySubscription'])->name('subscriptions.destroy');

            // Dynamic Cards
            Route::get('/cards', [WebsiteWebController::class , 'cards'])->name('cards.index');
            Route::post('/cards', [WebsiteWebController::class , 'cardStore'])->name('cards.store');
            Route::match (['post', 'put'], '/cards/{card}', [WebsiteWebController::class , 'cardUpdate'])->name('cards.update');
            Route::delete('/cards/{card}', [WebsiteWebController::class , 'cardDestroy'])->name('cards.destroy');

            // Project/Campaign Content Update
            Route::post('/projects', [WebsiteWebController::class , 'storeProject'])->name('projects.store');
            Route::match (['post', 'put'], '/projects/{project}', [WebsiteWebController::class , 'updateProjectContent'])->name('projects.update');
            Route::delete('/projects/{project}', [WebsiteWebController::class , 'destroyProject'])->name('projects.destroy');
            Route::post('/projects-stats', [WebsiteWebController::class , 'updateProjectStats'])->name('projects.stats.update');
            Route::get('/campaigns-content', [WebsiteWebController::class , 'campaignsContent'])->name('campaigns.content');
            Route::post('/campaigns', [WebsiteWebController::class , 'storeCampaign'])->name('campaigns.store');
            Route::match (['post', 'put'], '/campaigns/{campaign}', [WebsiteWebController::class , 'updateCampaignContent'])->name('campaigns.update');
            Route::delete('/campaigns/{campaign}', [WebsiteWebController::class , 'destroyCampaign'])->name('campaigns.destroy');
            Route::post('/campaigns-stats', [WebsiteWebController::class , 'updateCampaignStats'])->name('campaigns.stats.update');

            // Guest House Content
            Route::get('/guest-house-content', [WebsiteWebController::class , 'guestHouseContent'])->name('guest-house.content');
            Route::post('/guest-house-content', [WebsiteWebController::class , 'guestHouseContentUpdate'])->name('guest-house.update');
            Route::post('/guest-house-stats', [WebsiteWebController::class , 'updateGuestHouseStats'])->name('guest-house.stats.update');
            Route::get('/guest-house-slider', [WebsiteWebController::class , 'guestHouseSlider'])->name('guest-house.slider');
            Route::get('/dummy-bookings', [WebsiteWebController::class , 'createDummyBookings'])->name('guest-house.dummy');

            // Headquarters/Branches
            Route::get('/headquarters', [WebsiteWebController::class , 'headquarters'])->name('headquarters.index');
            Route::post('/headquarters', [WebsiteWebController::class , 'updateHeadquarters'])->name('headquarters.update');
            Route::post('/headquarters/branches', [WebsiteWebController::class , 'branchStore'])->name('headquarters.branches.store');
            Route::put('/headquarters/branches/{branch}', [WebsiteWebController::class , 'branchUpdate'])->name('headquarters.branches.update');
            Route::delete('/headquarters/branches/{branch}', [WebsiteWebController::class , 'branchDestroy'])->name('headquarters.branches.destroy');

            // General Settings & Stats
            Route::get('/settings', [WebsiteWebController::class , 'showSettings'])->name('settings.index');
            Route::post('/settings', [WebsiteWebController::class , 'updateSettings'])->name('settings.update');

            // Donation Page
            Route::get('/donation-page', [WebsiteWebController::class, 'donationPage'])->name('donation-page.index');
            Route::post('/donation-page', [WebsiteWebController::class, 'updateDonationPage'])->name('donation-page.update');

            // Website Accounts (Donors)
            Route::get('/accounts', [WebsiteWebController::class, 'accounts'])->name('accounts.index');
            Route::post('/accounts', [WebsiteWebController::class, 'accountStore'])->name('accounts.store');
            Route::put('/accounts/{user}', [WebsiteWebController::class, 'accountUpdate'])->name('accounts.update');
            Route::delete('/accounts/{user}', [WebsiteWebController::class, 'accountDestroy'])->name('accounts.destroy');

            // Website Donation Accounts
            Route::get('/donation-accounts', [\App\Http\Controllers\AdminWebsiteDonationWebController::class, 'index'])->name('donation-accounts.index');
            Route::get('/donation-accounts/{donor}', [\App\Http\Controllers\AdminWebsiteDonationWebController::class, 'show'])->name('donation-accounts.show');
            Route::post('/donation-accounts/{web_donation}/verify', [\App\Http\Controllers\AdminWebsiteDonationWebController::class, 'verifyDonation'])->name('donation-accounts.verify');
            Route::post('/donation-accounts/{web_donation}/reject', [\App\Http\Controllers\AdminWebsiteDonationWebController::class, 'rejectDonation'])->name('donation-accounts.reject');

            // Donation Settings - Categories & Items
            Route::prefix('donation-settings')->name('donation-settings.')->group(function () {
                Route::get('unified', [\App\Http\Controllers\DonationCategoryWebController::class, 'unified'])->name('unified');
                // Categories
                Route::get('/categories',                                                        [\App\Http\Controllers\DonationCategoryWebController::class, 'index'])  ->name('categories.index');
                Route::post('/categories',                                                       [\App\Http\Controllers\DonationCategoryWebController::class, 'store'])  ->name('categories.store');
                Route::put('/categories/{donationCategory}',                                    [\App\Http\Controllers\DonationCategoryWebController::class, 'update']) ->name('categories.update');
                Route::delete('/categories/{donationCategory}',                                 [\App\Http\Controllers\DonationCategoryWebController::class, 'destroy'])->name('categories.destroy');
                Route::patch('/categories/{donationCategory}/toggle',                           [\App\Http\Controllers\DonationCategoryWebController::class, 'toggleStatus'])->name('categories.toggle');
                // Items
                Route::get('/items',                                                             [\App\Http\Controllers\DonationItemWebController::class, 'index'])  ->name('items.index');
                Route::post('/items',                                                            [\App\Http\Controllers\DonationItemWebController::class, 'store'])  ->name('items.store');
                Route::put('/items/{donationItem}',                                             [\App\Http\Controllers\DonationItemWebController::class, 'update']) ->name('items.update');
                Route::delete('/items/{donationItem}',                                          [\App\Http\Controllers\DonationItemWebController::class, 'destroy'])->name('items.destroy');
                Route::patch('/items/{donationItem}/toggle',                                    [\App\Http\Controllers\DonationItemWebController::class, 'toggleStatus'])->name('items.toggle');
            });

            // Testimonials
            Route::get('/testimonials', [WebsiteWebController::class , 'testimonials'])->name('testimonials.index');
            Route::post('/testimonials', [WebsiteWebController::class , 'testimonialStore'])->name('testimonials.store');
            Route::match (['post', 'put'], '/testimonials/{testimonial}', [WebsiteWebController::class , 'testimonialUpdate'])->name('testimonials.update');
            Route::delete('/testimonials/{testimonial}', [WebsiteWebController::class , 'testimonialDestroy'])->name('testimonials.destroy');

            Route::get('/share-opinion', [WebsiteWebController::class , 'shareOpinion'])->name('share-opinion.index');

            // Dynamic Pages (Management)
            Route::get('/pages', [WebsiteWebController::class , 'pages'])->name('pages.index');
            Route::post('/pages', [WebsiteWebController::class , 'pageStore'])->name('pages.store');
            Route::match (['post', 'put'], '/pages/{page}', [WebsiteWebController::class , 'pageUpdate'])->name('pages.update');
            Route::delete('/pages/{page}', [WebsiteWebController::class , 'pageDestroy'])->name('pages.destroy');
        }
        );

        // --- Mobile App Management Unit ---
        Route::group(['prefix' => 'admin/mobile', 'as' => 'mobile.'], function () {
            Route::get('/', [MobileContentController::class , 'index'])->name('dashboard');

            // Banners
            Route::get('/banners', [MobileContentController::class , 'bannersIndex'])->name('banners.index');
            Route::post('/banners', [MobileContentController::class , 'bannerStore'])->name('banners.store');
            Route::delete('/banners/{banner}', [MobileContentController::class , 'bannerDestroy'])->name('banners.destroy');

            // Notifications
            Route::get('/notifications', [MobileContentController::class , 'notificationsIndex'])->name('notifications.index');
            Route::post('/notifications', [MobileContentController::class , 'notificationStore'])->name('notifications.store');
            Route::put('/notifications/{notification}', [MobileContentController::class , 'notificationUpdate'])->name('notifications.update');
            Route::delete('/notifications/{notification}', [MobileContentController::class , 'notificationDestroy'])->name('notifications.destroy');

            // Case Applications (from App)
            Route::get('/cases', [MobileContentController::class , 'casesIndex'])->name('cases.index');
            Route::patch('/cases/{application}', [MobileContentController::class , 'caseUpdateStatus'])->name('cases.update');

            // In-Kind Donations
            Route::get('/inkind', [MobileContentController::class , 'inKindDonationsIndex'])->name('inkind.index');
            Route::patch('/inkind/{donation}', [MobileContentController::class , 'inKindDonationUpdateStatus'])->name('inkind.update');

            // Project/Campaign Updates Mobile
            Route::put('/projects/{project}/mobile', [MobileContentController::class , 'updateProjectMobileContent'])->name('projects.update.mobile');
            Route::put('/campaigns/{campaign}/mobile', [MobileContentController::class , 'updateCampaignMobileContent'])->name('campaigns.update.mobile');

            // Home Content
            Route::get('/home-content', [MobileContentController::class, 'homeContentIndex'])->name('home_content.index');
            Route::post('/home-content', [MobileContentController::class, 'homeContentStore'])->name('home_content.store');
            Route::match(['put', 'post'], '/home-content/{item}', [MobileContentController::class, 'homeContentUpdate'])->name('home_content.update');
            Route::delete('/home-content/{item}', [MobileContentController::class, 'homeContentDestroy'])->name('home_content.destroy');

            // News
            Route::get('/news', [MobileContentController::class, 'newsIndex'])->name('news.index');
            Route::post('/news', [MobileContentController::class, 'newsStore'])->name('news.store');
            Route::match(['put', 'post'], '/news/{news}', [MobileContentController::class, 'newsUpdate'])->name('news.update');
            Route::delete('/news/{news}', [MobileContentController::class, 'newsDestroy'])->name('news.destroy');

            // Contact Messages
            Route::get('/contact-messages', [MobileContentController::class, 'contactMessagesIndex'])->name('contact-messages.index');
            Route::patch('/contact-messages/{message}', [MobileContentController::class, 'contactMessageUpdate'])->name('contact-messages.update');
            Route::delete('/contact-messages/{message}', [MobileContentController::class, 'contactMessageDestroy'])->name('contact-messages.destroy');

            // Volunteer Requests (Mobile Unit)
            Route::get('/volunteer-requests', [MobileContentController::class, 'volunteerRequestsIndex'])->name('volunteer-requests.index');
            Route::patch('/volunteer-requests/{volunteerRequest}', [MobileContentController::class, 'updateVolunteerRequestStatus'])->name('volunteer-requests.update');
            Route::delete('/volunteer-requests/{volunteerRequest}', [MobileContentController::class, 'destroyVolunteerRequest'])->name('volunteer-requests.destroy');
            Route::get('/volunteer-requests/{volunteerRequest}/cv', [MobileContentController::class, 'downloadVolunteerCV'])->name('volunteer-requests.cv');

            // Case Applications
            Route::get('/case-applications', [MobileContentController::class, 'caseApplicationsIndex'])->name('case-applications.index');
            Route::post('/case-applications/bulk-destroy', [MobileContentController::class, 'bulkDestroyCaseApplications'])->name('case-applications.bulk-destroy');
            Route::patch('/case-applications/{application}', [MobileContentController::class, 'updateCaseApplicationStatus'])->name('case-applications.update');
            Route::delete('/case-applications/{application}', [MobileContentController::class, 'destroyCaseApplication'])->name('case-applications.destroy');

            // Mobile Donations
            Route::get('/donations', [MobileContentController::class, 'donationsIndex'])->name('donations.index');
            Route::patch('/donations/{donation}', [MobileContentController::class, 'updateDonationStatus'])->name('donations.update');
            Route::delete('/donations/{donation}', [MobileContentController::class, 'destroyDonation'])->name('donations.destroy');

            // Contact Info Management (Separate from contact messages)
            Route::get('/contact-info', [MobileContentController::class, 'contactInfoIndex'])->name('contact_info.index');
            Route::post('/contact-info', [MobileContentController::class, 'contactInfoStore'])->name('contact_info.store');
            Route::match(['put', 'post'], '/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoUpdate'])->name('contact_info.update');
            Route::delete('/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoDestroy'])->name('contact_info.destroy');

            // Guest House Bookings (Mobile Unit - Unified)
            Route::get('/bookings', [MobileContentController::class, 'bookingsIndex'])->name('bookings.index');
            Route::patch('/bookings/{booking}', [MobileContentController::class, 'updateBookingStatus'])->name('bookings.update');
            Route::delete('/bookings/{booking}', [MobileContentController::class, 'destroyBooking'])->name('bookings.destroy');
            
            // Web Management inside Mobile Unit
            Route::patch('/web-bookings/{booking}', [MobileContentController::class, 'updateWebBookingStatus'])->name('web_bookings.update');
            Route::delete('/web-bookings/{booking}', [MobileContentController::class, 'destroyWebBooking'])->name('web_bookings.destroy');

            Route::get('/donors-auth', [MobileContentController::class, 'mobileDonorsIndex'])->name('donors_auth.index');
            Route::put('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorUpdate'])->name('donors_auth.update');
            Route::delete('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorDestroy'])->name('donors_auth.destroy');

            // Ensan Pillars (Integrated Services)
            Route::post('/pillars', [MobileContentController::class, 'pillarStore'])->name('pillars.store');
            Route::put('/pillars/{pillar}', [MobileContentController::class, 'pillarUpdate'])->name('pillars.update');
            Route::delete('/pillars/{pillar}', [MobileContentController::class, 'pillarDestroy'])->name('pillars.destroy');
        }
        );

        Route::post('admin/change-requests/{changeRequest}/approve', [\App\Http\Controllers\ChangeRequestWebController::class , 'approve'])->name('change-requests.approve');
        Route::post('admin/change-requests/{changeRequest}/reject', [\App\Http\Controllers\ChangeRequestWebController::class , 'reject'])->name('change-requests.reject');
        Route::delete('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class , 'destroy'])->name('change-requests.destroy');
        Route::post('admin/change-requests/bulk-destroy', [\App\Http\Controllers\ChangeRequestWebController::class , 'bulkDestroy'])->name('change-requests.bulk-destroy');
        Route::post('admin/change-requests/bulk-revert', [\App\Http\Controllers\ChangeRequestWebController::class , 'bulkRevert'])->name('change-requests.bulk-revert');
        Route::post('admin/change-requests/{changeRequest}/revert', [\App\Http\Controllers\ChangeRequestWebController::class , 'revert'])->name('change-requests.revert');
        Route::post('admin/change-requests/{changeRequest}/cancel', [\App\Http\Controllers\ChangeRequestWebController::class , 'cancel'])->name('change-requests.cancel');
        Route::put('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class , 'update'])->name('change-requests.update');

        // Ramadan Campaign
        Route::resource('ramadan-bags', \App\Http\Controllers\RamadanBagWebController::class)->except(['destroy']);
        Route::delete('ramadan-bags/{ramadan_bag}', [\App\Http\Controllers\RamadanBagWebController::class , 'destroy'])->name('ramadan-bags.destroy');
        Route::resource('ramadan-iftars', \App\Http\Controllers\RamadanIftarWebController::class)->except(['destroy']);
        Route::delete('ramadan-iftars/{ramadan_iftar}', [\App\Http\Controllers\RamadanIftarWebController::class , 'destroy'])->name('ramadan-iftars.destroy');

        // Collaborations & Memberships
        Route::resource('school-collaborations', \App\Http\Controllers\SchoolCollaborationWebController::class);
        Route::resource('memberships', \App\Http\Controllers\MembershipWebController::class);
        Route::resource('oncology-medicine-reps', \App\Http\Controllers\OncologyMedicineRepWebController::class);
        Route::resource('kafr-el-sheikh-brokers', \App\Http\Controllers\KafrElSheikhBrokerWebController::class);
        Route::resource('kafr-el-sheikh-deliveries', \App\Http\Controllers\KafrElSheikhDeliveryWebController::class);
        Route::resource('kafr-el-sheikh-services', \App\Http\Controllers\KafrElSheikhServiceWebController::class);
        Route::resource('tanta-workers', \App\Http\Controllers\TantaWorkerWebController::class);

        // سجلات النظام (Audits)
        Route::get('audits', [\App\Http\Controllers\AuditWebController::class , 'index'])->name('audits.index');
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
