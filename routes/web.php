<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\LoginWebController;

use App\Http\Controllers\HrEvaluationWebController;
use App\Http\Controllers\GuestHouseWebController;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\DonorWebController;
use App\Http\Controllers\DonationWebController;
use App\Http\Controllers\WarehouseWebController;
use App\Http\Controllers\ItemWebController;
use App\Http\Controllers\InventoryTransactionWebController;
use App\Http\Controllers\BeneficiaryWebController;
use App\Http\Controllers\BeneficiaryFamilyMemberWebController;
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
use App\Http\Controllers\SupplierWebController;
use App\Http\Controllers\PurchaseWebController;
use App\Http\Controllers\SalesTargetWebController;
use App\Http\Controllers\DonationCategoryWebController;
use App\Http\Controllers\DonationItemWebController;
use App\Http\Controllers\AdminWebsiteDonationWebController;
use App\Http\Controllers\ImageUploadController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

Route::get('/', function () {
    return redirect()->route('dashboard.index');
})->middleware('auth');

Route::get('login', [LoginWebController::class , 'show'])->name('login');
Route::post('login', [LoginWebController::class , 'login'])->middleware('throttle:auth-strict')->name('login.post');
Route::post('logout', [LoginWebController::class , 'logout'])->name('logout');

// تتبع الشكاوي - صفحة عامة بدون تسجيل دخول محمية بمعدل طلبات ومكافحة تخمين
Route::get('track-complaint', [\App\Http\Controllers\ComplaintTrackingController::class, 'show'])->middleware('throttle:heavy-ops')->name('complaint.track');
Route::post('track-complaint', [\App\Http\Controllers\ComplaintTrackingController::class, 'search'])->middleware('throttle:heavy-ops')->name('complaint.track.search');

// تقديم طلب مساعدة / بحث مبدئي - صفحة عامة للمواطنين
Route::get('apply-case', [MobileContentController::class, 'publicApplyShow'])->name('cases.public-apply');
Route::post('apply-case', [MobileContentController::class, 'publicApplyStore'])->middleware('throttle:heavy-ops')->name('cases.public-apply.store');

// إستمارة تقديم مشروع زاد (الأيتام) 2026 - صفحة عامة متطابقة مع نموذج جوجل
Route::get('apply-zad', [MobileContentController::class, 'applyZadShow'])->name('cases.apply-zad');
Route::post('apply-zad', [MobileContentController::class, 'applyZadStore'])->middleware('throttle:heavy-ops')->name('cases.apply-zad.store');

// إستمارة تقديم مشروع مدرار (وصلات مياه - أسقف - صدقات جارية) - صفحة عامة متطابقة مع نموذج جوجل
Route::get('apply-midrar', [MobileContentController::class, 'applyMidrarShow'])->name('cases.apply-midrar');
Route::post('apply-midrar', [MobileContentController::class, 'applyMidrarStore'])->middleware('throttle:heavy-ops')->name('cases.apply-midrar.store');

// إستمارة تقديم مشروع بعثاء الأمل (رعاية مرضى الأورام) - صفحة عامة متطابقة مع نموذج جوجل
Route::get('apply-hope', [MobileContentController::class, 'applyHopeShow'])->name('cases.apply-hope');
Route::post('apply-hope', [MobileContentController::class, 'applyHopeStore'])->middleware('throttle:heavy-ops')->name('cases.apply-hope.store');
// التحقق الفوري من تكرار الرقم القومي
Route::get('check-national-id', [MobileContentController::class, 'checkNationalId'])->name('check-national-id');

// استمارة الدعم النفسي العامة للمتطوعين (رابط مشاركة مباشر بدون تسجيل دخول)
Route::get('public/psychological-support/{type}/{id}/{token}', [\App\Http\Controllers\PsychologicalSupportController::class, 'publicForm'])
    ->name('public.psychological-support.form');
Route::post('public/psychological-support/{type}/{id}/{token}', [\App\Http\Controllers\PsychologicalSupportController::class, 'publicSubmit'])
    ->middleware('throttle:heavy-ops')
    ->name('public.psychological-support.submit');

// استمارة إعادة البحث الدورية كل 6 أشهر (رابط مشاركة مباشر للباحث/المتطوع)
Route::get('public/reassessment/{beneficiary}/{token}', [\App\Http\Controllers\BeneficiaryReassessmentController::class, 'publicForm'])
    ->name('public.reassessment.form');
Route::post('public/reassessment/{beneficiary}/{token}', [\App\Http\Controllers\BeneficiaryReassessmentController::class, 'publicSubmit'])
    ->middleware('throttle:heavy-ops')
    ->name('public.reassessment.submit');

// Public files only. Private documents must never use this fallback.
Route::get('storage/{path}', [\App\Http\Controllers\PublicStorageController::class, 'show'])
    ->where('path', '.*')->name('storage.fallback');

Route::get('documents/{path}', [\App\Http\Controllers\PrivateDocumentController::class, 'download'])
    ->where('path', '.*')->middleware('signed')->name('documents.download');

// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {

    // --- Internal Employee Chat (AI-Bot Style) ---
    Route::prefix('internal-chat')->name('internal-chat.')->group(function () {
        Route::get('contacts', [\App\Http\Controllers\InternalChatController::class, 'getContacts'])->name('contacts');
        Route::get('messages', [\App\Http\Controllers\InternalChatController::class, 'getMessages'])->name('messages');
        Route::post('send', [\App\Http\Controllers\InternalChatController::class, 'sendMessage'])->name('send');
        Route::get('unread-count', [\App\Http\Controllers\InternalChatController::class, 'unreadCount'])->name('unread-count');
    });

    // --- Privacy-Preserving Remote Search APIs (Min 2 chars required, no preloading) ---
    Route::get('api/search/beneficiaries', function (\Illuminate\Http\Request $request) {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $cleanQ = str_replace(
            ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            $q
        );

        $variants = \App\Helpers\ArabicSearchHelper::getVariants($q);
        if (empty($variants)) {
            $variants = [$q];
        }

        $words = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY);

        $results = \App\Models\Beneficiary::query()
            ->with([
                'patientProfile',
                'familyMembers' => fn ($query) => $query->where(fn($sq) => $sq->whereNull('active')->orWhere('active', true)->orWhere('active', 1))
            ])
            ->where(function ($query) use ($variants, $q, $cleanQ, $words) {
                // تصفية بسنة الكود إذا كان الإدخال رقمين
                if (preg_match('/^\d{2}$/', $cleanQ)) {
                    $query->where('code', 'like', $cleanQ . '%')
                          ->orWhere('code', 'like', 'G-' . $cleanQ . '%')
                          ->orWhere('patient_code', 'like', $cleanQ . '%')
                          ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', $cleanQ . '%'))
                          ->orWhere('full_name', 'like', "%{$q}%");
                    return;
                }

                // إذا كتب سنة كاملة مثل 2026
                if (preg_match('/^(?:19|20)(\d{2})$/', $cleanQ, $mYear)) {
                    $yr = $mYear[1];
                    $query->where('code', 'like', $yr . '%')
                          ->orWhere('code', 'like', 'G-' . $yr . '%')
                          ->orWhere('code', 'like', "%{$cleanQ}%")
                          ->orWhere('patient_code', 'like', $yr . '%')
                          ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', $yr . '%'))
                          ->orWhere('full_name', 'like', "%{$q}%");
                    return;
                }

                // البحث في حقول المستفيد الأساسي
                foreach ($variants as $var) {
                    $query->orWhere('full_name', 'like', "%{$var}%")
                          ->orWhere('patient_name', 'like', "%{$var}%")
                          ->orWhere('guardian_name', 'like', "%{$var}%");
                }

                if (count($words) > 1) {
                    $query->orWhere(function ($sub) use ($words) {
                        foreach ($words as $w) {
                            $sub->where(function ($wSub) use ($w) {
                                $wVariants = \App\Helpers\ArabicSearchHelper::getVariants($w);
                                foreach ($wVariants as $wv) {
                                    $wSub->orWhere('full_name', 'like', "%{$wv}%")
                                         ->orWhere('patient_name', 'like', "%{$wv}%")
                                         ->orWhere('guardian_name', 'like', "%{$wv}%");
                                }
                            });
                        }
                    });
                }

                $query->orWhere('code', 'like', "%{$cleanQ}%")
                    ->orWhere('patient_code', 'like', "%{$cleanQ}%")
                    ->orWhere('national_id', 'like', "%{$cleanQ}%")
                    ->orWhere('phone', 'like', "%{$cleanQ}%")
                    ->orWhere('backup_phone', 'like', "%{$cleanQ}%");

                // البحث الشامل في أفراد الأسرة (الأطفال، الأيتام، المرضى المكفولين)
                $query->orWhereHas('familyMembers', function ($fmQuery) use ($variants, $q, $cleanQ, $words) {
                    $fmQuery->where(function ($wSub) use ($variants, $q, $cleanQ, $words) {
                        foreach ($variants as $var) {
                            $wSub->orWhere('full_name', 'like', "%{$var}%");
                        }
                        if (count($words) > 1) {
                            $wSub->orWhere(function ($subWord) use ($words) {
                                foreach ($words as $w) {
                                    $subWord->where(function ($wInner) use ($w) {
                                        $wVars = \App\Helpers\ArabicSearchHelper::getVariants($w);
                                        foreach ($wVars as $wv) {
                                            $wInner->orWhere('full_name', 'like', "%{$wv}%");
                                        }
                                    });
                                }
                            });
                        }
                        $wSub->orWhere('code', 'like', "%{$cleanQ}%")
                             ->orWhere('national_id', 'like', "%{$cleanQ}%")
                             ->orWhere('phone', 'like', "%{$cleanQ}%");
                    });
                });
            })
            ->limit(35)
            ->get();

        $resultsList = [];
        foreach ($results as $b) {
            $matchedMembers = collect();
            if ($b->relationLoaded('familyMembers') && $b->familyMembers->isNotEmpty()) {
                foreach ($b->familyMembers as $m) {
                    $mName = (string) $m->full_name;
                    $mCode = (string) $m->code;
                    $mNat  = (string) $m->national_id;
                    $mPhone= (string) $m->phone;

                    $match = false;
                    if (!empty($mCode) && stripos($mCode, $cleanQ) !== false) {
                        $match = true;
                    } elseif (!empty($mNat) && stripos($mNat, $cleanQ) !== false) {
                        $match = true;
                    } elseif (!empty($mPhone) && stripos($mPhone, $cleanQ) !== false) {
                        $match = true;
                    } else {
                        foreach ($variants as $v) {
                            if (mb_stripos($mName, $v) !== false) {
                                $match = true;
                                break;
                            }
                        }
                    }

                    if ($match) {
                        $matchedMembers->push($m);
                    }
                }
            }

            if ($matchedMembers->isNotEmpty()) {
                foreach ($matchedMembers as $member) {
                    $relLabel = match($member->relationship) {
                        'child' => 'الطفل',
                        'patient' => 'المريض',
                        'spouse', 'husband', 'wife' => 'الزوج/الزوجة',
                        default => 'الطفل / المكفول'
                    };
                    $label = "{$relLabel}: {$member->full_name} (ملف الأسرة/الأم: {$b->full_name})"
                           . " — كود الأسرة: " . ($b->code ?: 'ID: '.$b->id)
                           . ($member->code ? " | كود الطفل: {$member->code}" : '');

                    $resultsList[] = [
                        'id'                    => $b->id,
                        'text'                  => $label,
                        'full_name'             => $member->full_name . ' (أسرة: ' . $b->full_name . ')',
                        'guardian_name'         => $b->full_name,
                        'patient_name'          => $member->full_name,
                        'code'                  => $b->code ?: ('BEN-' . $b->id),
                        'member_code'           => $member->code,
                        'national_id'           => $member->national_id ?: $b->national_id,
                        'phone'                 => $member->phone ?: $b->phone,
                        'address'               => $b->address,
                        'gender'                => $b->patientProfile?->gender ?: $b->gender,
                        'companion_name'        => $b->patientProfile?->companion_name,
                        'companion_relation'    => $b->patientProfile?->companion_relation,
                        'companion_national_id' => $b->patientProfile?->companion_national_id,
                        'companion_phone'       => $b->patientProfile?->companion_phone,
                        'companion_address'     => $b->patientProfile?->companion_address,
                        'medical_center'        => $b->patientProfile?->medical_center,
                        'treatment_type'        => $b->patientProfile?->treatment_type,
                        'sessions_count'        => $b->patientProfile?->sessions_count,
                        'medical_notes'         => $b->patientProfile?->medical_notes,
                        'matched_member_id'     => $member->id,
                        'matched_member_name'   => $member->full_name,
                        'matched_member_code'   => $member->code,
                        'notes'                 => "كفالة {$relLabel}: {$member->full_name}" . ($member->code ? " [{$member->code}]" : ''),
                    ];
                }
            } else {
                $displayName = !empty($b->patient_name) ? $b->patient_name : $b->full_name;
                $guardianText = (!empty($b->patient_name) && $b->patient_name !== $b->full_name) ? " (المستفيد: {$b->full_name})" : '';
                $text = $displayName . $guardianText . ' (' . ($b->code ?: 'ID: '.$b->id) . ($b->national_id ? ' | قومي: '.$b->national_id : '') . ($b->phone ? ' | هاتف: '.$b->phone : '') . ')';

                $resultsList[] = [
                    'id'                    => $b->id,
                    'text'                  => $text,
                    'full_name'             => $b->full_name,
                    'patient_name'          => $b->patient_name ?: $b->full_name,
                    'guardian_name'         => $b->guardian_name,
                    'code'                  => $b->code ?: ('BEN-' . $b->id),
                    'member_code'           => null,
                    'national_id'           => $b->national_id,
                    'phone'                 => $b->phone,
                    'address'               => $b->address,
                    'gender'                => $b->patientProfile?->gender ?: $b->gender,
                    'companion_name'        => $b->patientProfile?->companion_name,
                    'companion_relation'    => $b->patientProfile?->companion_relation,
                    'companion_national_id' => $b->patientProfile?->companion_national_id,
                    'companion_phone'       => $b->patientProfile?->companion_phone,
                    'companion_address'     => $b->patientProfile?->companion_address,
                    'medical_center'        => $b->patientProfile?->medical_center,
                    'treatment_type'        => $b->patientProfile?->treatment_type,
                    'sessions_count'        => $b->patientProfile?->sessions_count,
                    'medical_notes'         => $b->patientProfile?->medical_notes,
                    'matched_member_id'     => null,
                    'matched_member_name'   => null,
                    'matched_member_code'   => null,
                    'notes'                 => '',
                ];
            }
        }
        return response()->json($resultsList);
    })->name('api.search.beneficiaries');

    Route::get('api/search/donors', function (\Illuminate\Http\Request $request) {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }
        $donorQuery = \App\Models\Donor::query();
        \App\Helpers\ArabicSearchHelper::applySmartSearch(
            $donorQuery,
            $q,
            ['name'],
            ['code', 'phone', 'national_id']
        );
        $results = $donorQuery
            ->limit(25)
            ->get()
            ->map(function ($d) {
                return [
                    'id'                  => $d->id,
                    'text'                => $d->name . ' — ' . $d->code . ($d->phone ? ' — ' . $d->phone : '') . ($d->national_id ? ' — قومي: ' . $d->national_id : ''),
                    'name'                => $d->name,
                    'code'                => $d->code,
                    'phone'               => $d->phone,
                    'national_id'         => $d->national_id,
                    'sponsorship_profile' => $d->getSponsorshipProfile(),
                ];
            });
        return response()->json($results);
    })->name('api.search.donors');

    Route::get('api/donors/{donor}/sponsorship-profile', function (\App\Models\Donor $donor) {
        return response()->json($donor->getSponsorshipProfile());
    })->name('api.donors.sponsorship-profile');

    Route::get('api/global-search', [\App\Http\Controllers\GlobalSearchController::class, 'search'])->name('api.global.search');

    // --- Image Upload (AJAX / Fetch from Web Dashboard) ---
    Route::prefix('upload')->middleware('throttle:heavy-ops')->group(function () {
        Route::post('image',   [ImageUploadController::class, 'upload'])->name('upload.image');
        Route::post('images',  [ImageUploadController::class, 'uploadMultiple'])->name('upload.images');
        Route::delete('image', [ImageUploadController::class, 'destroy'])->name('upload.image.destroy');
    });

    // --- General Entity Attachments ---
    Route::get('attachments/{attachment}', [\App\Http\Controllers\AttachmentWebController::class, 'show'])->name('attachments.show');
    Route::post('attachments', [\App\Http\Controllers\AttachmentWebController::class, 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [\App\Http\Controllers\AttachmentWebController::class, 'destroy'])->name('attachments.destroy');

    // --- Core Dashboard ---
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('dashboard', [DashboardWebController::class, 'index'])->name('dashboard.index');
    });
    Route::middleware('permission:dashboard.analytics')->group(function () {
        Route::get('dashboard/analytics', [DashboardWebController::class, 'analytics'])->name('dashboard.analytics');
    });

    // --- Smart Data Import (Excel / CSV) ---
    Route::middleware('permission:data_import.view')->prefix('data-import')->name('data-import.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DataImportWebController::class, 'index'])->name('index');
        Route::get('template/{section}', [\App\Http\Controllers\DataImportWebController::class, 'downloadTemplate'])->name('template');
        Route::post('preview', [\App\Http\Controllers\DataImportWebController::class, 'preview'])->name('preview');
        Route::post('process', [\App\Http\Controllers\DataImportWebController::class, 'process'])->name('process');
        Route::post('batch-process', [\App\Http\Controllers\DataImportWebController::class, 'batchProcess'])->name('batch-process');
    });

    Route::middleware('permission:notifications.view')->group(function () {
        Route::get('notifications', [NotificationWebController::class, 'index'])->name('notifications.index');
        Route::post('notifications/store-targeted', [NotificationWebController::class, 'storeTargeted'])->name('notifications.store-targeted');
        Route::post('notifications/broadcast-update', [NotificationWebController::class, 'broadcastSystemUpdate'])->name('notifications.broadcast-update');
        Route::post('notifications/mark-read', [NotificationWebController::class, 'markRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationWebController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::post('notifications/dismiss', [NotificationWebController::class, 'dismiss'])->name('notifications.dismiss');
        Route::post('notifications/clear-read', [NotificationWebController::class, 'clearRead'])->name('notifications.clear-read');
    });

    Route::middleware('permission:audits.view')->group(function () {
        Route::get('audits', [AuditWebController::class, 'index'])->name('audits.index');
    });

    // --- Donors & Donations ---
    Route::middleware('permission:donors.view')->group(function () {
        Route::get('donors/print-report', [DonorWebController::class, 'printReport'])->middleware('throttle:heavy-ops')->name('donors.print-report');
        Route::post('donors/print-report', [DonorWebController::class, 'printReport'])->middleware('throttle:heavy-ops')->name('donors.print-report.post');
        Route::get('donors/export/pdf', [DonorWebController::class, 'exportPdf'])->middleware('throttle:heavy-ops')->name('donors.export-pdf');
        Route::post('donors/export/pdf', [DonorWebController::class, 'exportPdf'])->middleware('throttle:heavy-ops')->name('donors.export-pdf.post');
        Route::resource('donors', DonorWebController::class);
        Route::post('donors/bulk-destroy', [DonorWebController::class, 'bulkDestroy'])->name('donors.bulk-destroy');
    });

    Route::middleware('permission:donations.view')->group(function () {
        Route::get('donations/cash', [DonationWebController::class, 'cashIndex'])->name('donations.cash');
        Route::get('donations/in-kind', [DonationWebController::class, 'inKindIndex'])->name('donations.in-kind');
        Route::get('donations/export', [DonationWebController::class, 'export'])->middleware('throttle:heavy-ops')->name('donations.export');
        Route::resource('donations', DonationWebController::class);
        Route::post('donations/bulk-destroy', [DonationWebController::class, 'bulkDestroy'])->name('donations.bulk-destroy');
    });

    // --- Inventory & Warehouses ---
    Route::middleware('permission:warehouses.view|inventory.view')->group(function () {
        Route::resource('warehouses', WarehouseWebController::class);
    });
    Route::middleware('permission:items.view|warehouses.view|inventory.view')->group(function () {
        Route::resource('items', ItemWebController::class);
    });
    Route::middleware('permission:inventory_transactions.view|warehouses.view|inventory.view')->group(function () {
        Route::get('inventory-transactions/transfer', [InventoryTransactionWebController::class, 'createTransfer'])->name('inventory-transactions.create-transfer');
        Route::post('inventory-transactions/transfer', [InventoryTransactionWebController::class, 'storeTransfer'])->name('inventory-transactions.store-transfer');
        Route::get('inventory-transactions/reconcile', [InventoryTransactionWebController::class, 'createReconcile'])->name('inventory-transactions.create-reconcile');
        Route::post('inventory-transactions/reconcile', [InventoryTransactionWebController::class, 'storeReconcile'])->name('inventory-transactions.store-reconcile');
        Route::resource('inventory-transactions', InventoryTransactionWebController::class);
    });

    Route::middleware('permission:suppliers.view')->group(function () {
        Route::post('suppliers/{supplier}/purchases', [PurchaseWebController::class, 'store'])->name('suppliers.purchases.store');
        Route::delete('suppliers/{supplier}/purchases/{purchase}', [PurchaseWebController::class, 'destroy'])->name('suppliers.purchases.destroy');
        Route::resource('suppliers', SupplierWebController::class);
    });

    // --- Beneficiaries ---
    Route::middleware('permission:beneficiaries.view')->group(function () {
        Route::get('beneficiaries/export', [BeneficiaryWebController::class, 'export'])->name('beneficiaries.export');
        Route::get('beneficiaries/export/pdf', [BeneficiaryWebController::class, 'exportPdf'])->name('beneficiaries.export-pdf');
        Route::get('beneficiaries/{beneficiary}/pdf', [BeneficiaryWebController::class, 'pdf'])->name('beneficiaries.pdf');
        Route::get('beneficiary-family-members/{familyMember}', [BeneficiaryFamilyMemberWebController::class, 'show'])
            ->name('beneficiary-family-members.show');
        Route::post('beneficiary-family-members/{familyMember}/sponsors', [BeneficiaryFamilyMemberWebController::class, 'attachSponsor'])
            ->name('beneficiary-family-members.attach-sponsor');
        Route::delete('beneficiary-family-members/{familyMember}/sponsors/{donor}', [BeneficiaryFamilyMemberWebController::class, 'detachSponsor'])
            ->name('beneficiary-family-members.detach-sponsor');
        Route::patch('beneficiary-family-members/{familyMember}/sponsorship', [BeneficiaryFamilyMemberWebController::class, 'updateSponsorship'])
            ->name('beneficiary-family-members.update-sponsorship');
        Route::post('beneficiary-family-members/{familyMember}/psychological-support', [\App\Http\Controllers\PsychologicalSupportController::class, 'storeMember'])
            ->name('beneficiary-family-members.psychological-support.store');
        Route::post('beneficiary-family-members/{familyMember}/assessments/entry', [\App\Http\Controllers\ChildAssessmentWebController::class, 'storeEntry'])
            ->name('child-assessments.store-entry');
        Route::post('beneficiary-family-members/{familyMember}/assessments/monthly', [\App\Http\Controllers\ChildAssessmentWebController::class, 'storeMonthly'])
            ->name('child-assessments.store-monthly');
        Route::post('beneficiary-family-members/{familyMember}/documents', [\App\Http\Controllers\ChildAssessmentWebController::class, 'uploadDocument'])
            ->name('child-assessments.upload-document');
        Route::get('beneficiary-family-members/{familyMember}/report', [\App\Http\Controllers\ChildAssessmentWebController::class, 'report'])
            ->name('child-assessments.report');
        Route::get('child-assessments/{assessment}', [\App\Http\Controllers\ChildAssessmentWebController::class, 'show'])
            ->name('child-assessments.show');
        Route::post('beneficiaries/bulk', [BeneficiaryWebController::class, 'bulkUpdate'])->name('beneficiaries.bulk');
        Route::post('beneficiaries/{beneficiary}/patient-documents', [BeneficiaryWebController::class, 'uploadPatientDocument'])->name('beneficiaries.upload-patient-doc');
        Route::delete('beneficiaries/{beneficiary}/patient-documents/{docType}', [BeneficiaryWebController::class, 'deletePatientDocument'])->name('beneficiaries.delete-patient-doc');
        Route::post('beneficiaries/{beneficiary}/sponsors', [BeneficiaryWebController::class, 'attachSponsor'])->name('beneficiaries.attach-sponsor');
        Route::delete('beneficiaries/{beneficiary}/sponsors/{donor}', [BeneficiaryWebController::class, 'detachSponsor'])->name('beneficiaries.detach-sponsor');
        Route::post('beneficiaries/{beneficiary}/direct-aid', [BeneficiaryWebController::class, 'recordDirectAid'])->name('beneficiaries.record-direct-aid');
        Route::post('beneficiaries/{beneficiary}/reassessments', [\App\Http\Controllers\BeneficiaryReassessmentController::class, 'store'])->name('beneficiaries.reassessments.store');
        Route::delete('beneficiary-reassessments/{reassessment}', [\App\Http\Controllers\BeneficiaryReassessmentController::class, 'destroy'])->name('beneficiaries.reassessments.destroy');
        Route::resource('beneficiaries', BeneficiaryWebController::class);
    });

    // --- Psychological Support Hub ---
    Route::middleware('permission:psychological_support.view|beneficiaries.view')->group(function () {
        Route::get('psychological-support', [\App\Http\Controllers\PsychologicalSupportController::class, 'index'])->name('psychological-support.index');
        Route::post('beneficiaries/{beneficiary}/psychological-support', [\App\Http\Controllers\PsychologicalSupportController::class, 'store'])->name('psychological-support.store');
        Route::get('beneficiaries/{beneficiary}/psychological-support/modal', [\App\Http\Controllers\PsychologicalSupportController::class, 'showModal'])->name('psychological-support.modal');
    });

    // --- Collaborations & Partnerships Directory ---
    Route::middleware('permission:collaborations.view|manage_specialized_services')->group(function () {
        Route::resource('collaborations', \App\Http\Controllers\CollaborationController::class);
    });

    // --- Finance & Accounting ---
    Route::middleware('permission:accounts.view|treasuries.view|manage_finance')->group(function () {
        Route::get('accounts/dashboard/overview', [AccountWebController::class, 'dashboard'])->name('accounts.dashboard');
        Route::resource('accounts', AccountWebController::class);
        Route::get('treasuries/dashboard', [TreasuryController::class, 'dashboard'])->name('treasuries.dashboard');
        Route::get('treasuries/sync-accounts', [TreasuryController::class, 'syncAccounts'])->name('treasuries.syncAccounts');
        Route::get('treasuries/export', [TreasuryController::class, 'export'])->name('treasuries.export');
        Route::post('treasuries/{treasury}/adjust-totals', [TreasuryController::class, 'adjustTotals'])->name('treasuries.adjustTotals');
        Route::post('treasuries/{treasury}/transactions', [TreasuryController::class, 'addTransaction'])->name('treasuries.addTransaction');
        Route::put('treasuries/{treasury}/transactions/{transaction}', [TreasuryController::class, 'updateTransaction'])->name('treasuries.updateTransaction');
        Route::delete('treasuries/{treasury}/transactions/{transaction}', [TreasuryController::class, 'deleteTransaction'])->name('treasuries.deleteTransaction');
        Route::resource('treasuries', TreasuryController::class);
    });

    Route::middleware('permission:revenues.view|accounts.view|manage_finance')->group(function () {
        Route::get('revenues', [\App\Http\Controllers\RevenueWebController::class, 'index'])->name('revenues.index');
    });

    Route::middleware('permission:journal_entries.view|manage_finance')->group(function () {
        Route::resource('journal-entries', JournalEntryWebController::class);
    });

    Route::middleware('permission:expenses.view|expenses.create|manage_finance')->group(function () {
        Route::get('expenses/export', [ExpenseWebController::class, 'export'])->name('expenses.export');
        Route::resource('expenses', ExpenseWebController::class);
    });

    Route::middleware('permission:financial_closures.view|closures.view|manage_finance')->group(function () {
        Route::get('finance/closures', [FinancialClosureWebController::class, 'index'])->name('closures.index');
        Route::get('finance/closures/create', [FinancialClosureWebController::class, 'create'])->name('closures.create');
        Route::post('finance/closures', [FinancialClosureWebController::class, 'store'])->name('closures.store');
        Route::get('finance/closures/{closure}', [FinancialClosureWebController::class, 'show'])->name('closures.show');
        Route::get('finance/closures/{closure}/edit', [FinancialClosureWebController::class, 'edit'])->name('closures.edit');
        Route::put('finance/closures/{closure}', [FinancialClosureWebController::class, 'update'])->name('closures.update');
        Route::delete('finance/closures/{closure}', [FinancialClosureWebController::class, 'destroy'])->name('closures.destroy');
        Route::post('finance/closures/{closure}/approve', [FinancialClosureWebController::class, 'approve'])->name('closures.approve');
    });

    // --- HR & Volunteers (Granular Permissions) ---
    Route::middleware('permission:hr.evaluations|manage_employees|manage_volunteers_hr')->group(function () {
        Route::get('hr/dashboard', [\App\Http\Controllers\HrDashboardWebController::class, 'index'])->name('hr.dashboard');
        Route::get('hr/evaluations', [HrEvaluationWebController::class, 'index'])->name('hr.evaluations');
    });

    Route::middleware('permission:volunteers.view|manage_volunteers_hr')->group(function () {
        Route::get('volunteers/reports', [VolunteerWebController::class, 'reports'])->name('volunteers.reports');
        Route::resource('volunteers', VolunteerWebController::class);
    });

    Route::middleware('permission:volunteer_hours.view|log_volunteer_hours|volunteer_hours.create')->group(function () {
        Route::resource('volunteer-hours', VolunteerHourWebController::class);
    });

    Route::middleware('permission:volunteer_attendance.view|volunteer_attendance.view_own|volunteer_attendance.create')->group(function () {
        Route::resource('volunteer-attendance', VolunteerAttendanceWebController::class);
    });

    Route::middleware('permission:volunteer_tasks.view|tasks.view|view_own_tasks')->group(function () {
        Route::resource('volunteer-tasks', VolunteerTaskWebController::class);
    });

    Route::middleware('permission:employee_attendance.view|employee_attendance.view_own|employee_attendance.create')->group(function () {
        Route::post('employee-attendance/check-in', [EmployeeAttendanceWebController::class, 'checkIn'])->name('employee-attendance.checkIn');
        Route::post('employee-attendance/check-out', [EmployeeAttendanceWebController::class, 'checkOut'])->name('employee-attendance.checkOut');
        Route::post('employee-attendance/bulk-destroy', [EmployeeAttendanceWebController::class, 'bulkDestroy'])->name('employee-attendance.bulk-destroy');
        Route::resource('employee-attendance', EmployeeAttendanceWebController::class);
    });

    Route::middleware('permission:employee_tasks.view|employee_tasks.view_own|employee_tasks.create')->group(function () {
        Route::post('employee-tasks/{employee_task}/status', [EmployeeTaskWebController::class, 'updateStatus'])->name('employee-tasks.update-status');
        Route::post('employee-tasks/{employee_task}/attachments', [EmployeeTaskWebController::class, 'uploadAttachment'])->name('employee-tasks.upload-attachment');
        Route::get('employee-tasks/{employee_task}/attachments/{attachment}', [EmployeeTaskWebController::class, 'downloadAttachment'])->name('employee-tasks.download-attachment');
        Route::resource('employee-tasks', EmployeeTaskWebController::class);
    });

    Route::middleware('permission:leaves.view|leaves.manage|leaves.create')->group(function () {
        Route::post('leaves/bulk-destroy', [\App\Http\Controllers\LeaveWebController::class, 'bulkDestroy'])->name('leaves.bulk-destroy');
        Route::resource('leaves', \App\Http\Controllers\LeaveWebController::class)->parameters(['leaves' => 'leave']);
    });

    // --- Payrolls (Specific Permission) ---
    Route::middleware('permission:payrolls.view')->group(function () {
        Route::get('payrolls/dashboard/overview', [PayrollWebController::class, 'dashboard'])->name('payrolls.dashboard');
        Route::get('sales/target', [SalesTargetWebController::class, 'index'])->name('sales.target');
        Route::post('sales/target/employee', [SalesTargetWebController::class, 'updateEmployee'])->name('sales.target.update-employee');
        Route::post('payrolls/{payroll}/create-journal-entry', [PayrollWebController::class, 'createJournalEntry'])->name('payrolls.createJournalEntry');
        Route::resource('payrolls', PayrollWebController::class);
    });

    // --- Logistics ---
    Route::middleware('permission:manage_logistics|delegates.view|travel_routes.view|trips.view')->group(function () {
        Route::get('logistics/dashboard', [LogisticsDashboardController::class, 'index'])->name('logistics.dashboard');
    });
    Route::middleware('permission:delegates.view|manage_logistics')->group(function () {
        Route::get('delegates/export', [DelegateWebController::class, 'export'])->name('delegates.export');
        Route::post('delegates/bulk', [DelegateWebController::class, 'bulkUpdate'])->name('delegates.bulk');
        Route::post('delegates/{delegate}/trips', [DelegateWebController::class, 'storeTrip'])->name('delegates.storeTrip');
        Route::delete('delegates/{delegate}/trips/{trip}', [DelegateWebController::class, 'destroyTrip'])->name('delegates.destroyTrip');
        Route::patch('delegates/{delegate}/trips/{trip}', [DelegateWebController::class, 'updateTripStatus'])->name('delegates.updateTripStatus');
        Route::resource('delegates', DelegateWebController::class);
    });
    Route::middleware('permission:travel_routes.view|manage_logistics')->group(function () {
        Route::post('travel-routes/{travel_route}/cities', [TravelRouteWebController::class, 'addCity'])->name('travel-routes.addCity');
        Route::get('travel-routes/export', [TravelRouteWebController::class, 'export'])->name('travel-routes.export');
        Route::post('travel-routes/{travel_route}/duplicate', [TravelRouteWebController::class, 'duplicate'])->name('travel-routes.duplicate');
        Route::resource('travel-routes', TravelRouteWebController::class);
    });
    Route::middleware('permission:trips.view|manage_logistics')->group(function () {
        Route::get('trips', [TripWebController::class, 'index'])->name('trips.index');
        Route::post('trips', [TripWebController::class, 'store'])->name('trips.store');
    });

    Route::middleware('permission:reports.view')->group(function () {
        Route::get('reports', [ReportsWebController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportsWebController::class, 'export'])->name('reports.export');
        Route::get('reports/print', [ReportsWebController::class, 'printReport'])->name('reports.print');
    });

    // --- Admin & RBAC ---
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserWebController::class, 'index'])->name('users.index');
    });
    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserWebController::class, 'create'])->name('users.create');
        Route::post('users', [UserWebController::class, 'store'])->name('users.store');
    });
    Route::middleware('own_user_or_permission:users.view')->group(function () {
        Route::get('users/{user}', [UserWebController::class, 'show'])->name('users.show');
    });
    Route::middleware('own_user_or_permission:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserWebController::class, 'edit'])->name('users.edit');
        Route::match(['PUT', 'PATCH'], 'users/{user}', [UserWebController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('users/{user}', [UserWebController::class, 'destroy'])->name('users.destroy');
    });
    Route::middleware('permission:users.edit')->group(function () {
        Route::post('users/{user}/roles/{role}', [UserWebController::class, 'attachRole'])->name('users.attachRole');
        Route::delete('users/{user}/roles/{role}', [UserWebController::class, 'detachRole'])->name('users.detachRole');
    });

    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleWebController::class, 'index'])->name('roles.index');
    });
    Route::middleware('permission:roles.create')->group(function () {
        Route::get('roles/create', [RoleWebController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleWebController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles/{role}', [RoleWebController::class, 'show'])->name('roles.show');
    });
    Route::middleware('permission:roles.edit')->group(function () {
        Route::get('roles/{role}/edit', [RoleWebController::class, 'edit'])->name('roles.edit');
        Route::match(['PUT', 'PATCH'], 'roles/{role}', [RoleWebController::class, 'update'])->name('roles.update');
        Route::post('roles/{role}/users', [RoleWebController::class, 'attachUser'])->name('roles.attachUser');
        Route::delete('roles/{role}/users/{user}', [RoleWebController::class, 'detachUser'])->name('roles.detachUser');
    });
    Route::middleware('permission:roles.delete')->group(function () {
        Route::delete('roles/{role}', [RoleWebController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('permission:complaints.view')->group(function () {
        Route::post('complaints/{complaint}/reply', [ComplaintWebController::class, 'reply'])->name('complaints.reply');
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

        Route::post('projects/{project}/monthly-volunteers', [ProjectWebController::class, 'storeMonthlyVolunteer'])->name('projects.storeMonthlyVolunteer');
        Route::delete('projects/{project}/monthly-volunteers/{monthlyVolunteer}', [ProjectWebController::class, 'destroyMonthlyVolunteer'])->name('projects.destroyMonthlyVolunteer');
        Route::post('projects/{project}/activities', [ProjectWebController::class, 'storeActivity'])->name('projects.storeActivity');
        Route::delete('projects/{project}/activities/{activity}', [ProjectWebController::class, 'destroyActivity'])->name('projects.destroyActivity');

        // Generic beneficiary file management
        Route::post('projects/{project}/beneficiaries', [ProjectWebController::class, 'storeBeneficiaryFile'])->name('projects.storeBeneficiaryFile');
        Route::put('projects/{project}/beneficiaries/{beneficiary}', [ProjectWebController::class, 'updateBeneficiaryFile'])->name('projects.updateBeneficiaryFile');
        Route::delete('projects/{project}/beneficiaries/{beneficiary}', [ProjectWebController::class, 'destroyBeneficiaryFile'])->name('projects.destroyBeneficiaryFile');
        Route::post('projects/{project}/donations', [ProjectWebController::class, 'storeDonation'])
            ->middleware('permission:donations.view')
            ->name('projects.storeDonation');
        Route::post('projects/{project}/expenses', [ProjectWebController::class, 'storeExpense'])
            ->middleware(['permission:manage_finance', 'permission:expenses.view'])
            ->name('projects.storeExpense');
        
        // Zad Management
        Route::post('projects/{project}/zad-families', [ProjectWebController::class, 'storeZadFamily'])->name('projects.storeZadFamily');
        Route::put('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class, 'updateZadFamily'])->name('projects.updateZadFamily');
        Route::delete('projects/{project}/zad-families/{beneficiary}', [ProjectWebController::class, 'destroyZadFamily'])->name('projects.destroyZadFamily');
    });

    Route::middleware('permission:campaigns.view')->group(function () {
        Route::resource('campaigns', CampaignWebController::class);
        Route::post('campaigns/bulk-destroy', [CampaignWebController::class, 'bulkDestroy'])->name('campaigns.bulk-destroy');
        Route::post('campaigns/{campaign}/manager', [CampaignWebController::class, 'setManager'])->name('campaigns.setManager');
        Route::post('campaigns/{campaign}/volunteers', [CampaignWebController::class, 'attachVolunteer'])->name('campaigns.attachVolunteer');
        Route::delete('campaigns/{campaign}/volunteers/{user}', [CampaignWebController::class, 'detachVolunteer'])->name('campaigns.detachVolunteer');
        Route::post('campaigns/{campaign}/monthly-volunteers', [CampaignWebController::class, 'storeMonthlyVolunteer'])->name('campaigns.storeMonthlyVolunteer');
        Route::delete('campaigns/{campaign}/monthly-volunteers/{monthlyVolunteer}', [CampaignWebController::class, 'destroyMonthlyVolunteer'])->name('campaigns.destroyMonthlyVolunteer');
        Route::post('campaigns/{campaign}/daily-menus', [CampaignWebController::class, 'storeDailyMenu'])->name('campaigns.storeDailyMenu');
        Route::delete('campaigns/{campaign}/daily-menus/{dailyMenu}', [CampaignWebController::class, 'destroyDailyMenu'])->name('campaigns.destroyDailyMenu');
        Route::post('campaigns/{campaign}/beneficiaries', [CampaignWebController::class, 'storeBeneficiaryFile'])->name('campaigns.storeBeneficiaryFile');
        Route::put('campaigns/{campaign}/beneficiaries/{beneficiary}', [CampaignWebController::class, 'updateBeneficiaryFile'])->name('campaigns.updateBeneficiaryFile');
        Route::delete('campaigns/{campaign}/beneficiaries/{beneficiary}', [CampaignWebController::class, 'destroyBeneficiaryFile'])->name('campaigns.destroyBeneficiaryFile');
        Route::post('campaigns/{campaign}/donations', [CampaignWebController::class, 'storeDonation'])
            ->middleware('permission:donations.view')
            ->name('campaigns.storeDonation');
        Route::post('campaigns/{campaign}/expenses', [CampaignWebController::class, 'storeExpense'])
            ->middleware(['permission:manage_finance', 'permission:expenses.view'])
            ->name('campaigns.storeExpense');
    });

    // --- Website & Mobile Volunteer Requests ---
    Route::middleware('permission:website.volunteer_requests.view|website.view|manage_website')->group(function () {
        Route::get('admin/website/volunteer-requests', [WebsiteWebController::class, 'volunteerRequests'])->name('website.volunteer-requests.index');
        Route::post('admin/website/volunteer-requests/content', [WebsiteWebController::class, 'updateVolunteerContent'])->name('website.volunteer-content.update');
        Route::post('admin/website/volunteer-requests/{volunteerRequest}/status', [WebsiteWebController::class, 'updateVolunteerRequestStatus'])->name('website.volunteer-requests.update-status');
        Route::delete('admin/website/volunteer-requests/{volunteerRequest}', [WebsiteWebController::class, 'destroyVolunteerRequest'])->name('website.volunteer-requests.destroy');
        Route::get('admin/website/volunteer-requests/{volunteerRequest}/download-cv', [WebsiteWebController::class, 'downloadCV'])->name('website.volunteer-requests.cv');
    });

    Route::middleware('permission:mobile.volunteer_requests.view|mobile.view|manage_mobile')->group(function () {
        Route::group(['prefix' => 'admin/mobile', 'as' => 'mobile.'], function () {
            Route::get('/volunteer-requests', [MobileContentController::class, 'volunteerRequestsIndex'])->name('volunteer-requests.index');
            Route::post('/volunteer-requests/{volunteerRequest}/status', [MobileContentController::class, 'updateVolunteerRequestStatus'])->name('volunteer-requests.update');
            Route::delete('/volunteer-requests/{volunteerRequest}', [MobileContentController::class, 'destroyVolunteerRequest'])->name('volunteer-requests.destroy');
            Route::get('/volunteer-requests/{volunteerRequest}/download-cv', [MobileContentController::class, 'downloadVolunteerCV'])->name('volunteer-requests.cv');
        });
    });

    // --- Website Management (Granular or Umbrella) ---
    Route::group([], function () {
        Route::middleware('permission:website.settings.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/settings', [WebsiteWebController::class, 'showSettings'])->name('website.settings.index');
            Route::post('admin/website/settings', [WebsiteWebController::class, 'updateSettings'])->name('website.settings.update');
            Route::post('admin/website/contact-settings', [WebsiteWebController::class, 'updateContactSettings'])->name('website.contact-settings.update');
        });
        
        Route::middleware('permission:website.headquarters.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/headquarters', [WebsiteWebController::class, 'headquarters'])->name('website.headquarters.index');
            Route::post('admin/website/headquarters', [WebsiteWebController::class, 'updateHeadquarters'])->name('website.headquarters.update');
            Route::post('admin/website/headquarters/branches', [WebsiteWebController::class, 'branchStore'])->name('website.headquarters.branches.store');
            Route::put('admin/website/headquarters/branches/{branch}', [WebsiteWebController::class, 'branchUpdate'])->name('website.headquarters.branches.update');
            Route::delete('admin/website/headquarters/branches/{branch}', [WebsiteWebController::class, 'branchDestroy'])->name('website.headquarters.branches.destroy');
        });
        
        Route::middleware('permission:website.partners.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/partners', [WebsiteWebController::class, 'partners'])->name('website.partners.index');
            Route::post('admin/website/partners', [WebsiteWebController::class, 'partnerStore'])->name('website.partners.store');
            Route::put('admin/website/partners/{partner}', [WebsiteWebController::class, 'partnerUpdate'])->name('website.partners.update');
            Route::delete('admin/website/partners/{partner}', [WebsiteWebController::class, 'partnerDestroy'])->name('website.partners.destroy');
            
            // Volunteer Wall (Honor Wall Leaders)
            Route::get('admin/website/volunteer-wall', [WebsiteWebController::class, 'volunteerWall'])->name('website.volunteer-wall.index');
            Route::post('admin/website/volunteer-wall', [WebsiteWebController::class, 'volunteerWallStore'])->name('website.volunteer-wall.store');
            Route::put('admin/website/volunteer-wall/{leader}', [WebsiteWebController::class, 'volunteerWallUpdate'])->name('website.volunteer-wall.update');
            Route::delete('admin/website/volunteer-wall/{leader}', [WebsiteWebController::class, 'volunteerWallDestroy'])->name('website.volunteer-wall.destroy');
        });

        Route::middleware('permission:website.board.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/board', [WebsiteWebController::class, 'boardMembers'])->name('website.board.index');
            Route::post('admin/website/board', [WebsiteWebController::class, 'boardMemberStore'])->name('website.board.store');
            Route::put('admin/website/board/{member}', [WebsiteWebController::class, 'boardMemberUpdate'])->name('website.board.update');
            Route::delete('admin/website/board/{member}', [WebsiteWebController::class, 'boardMemberDestroy'])->name('website.board.destroy');
        });

        Route::middleware('permission:website.content.view_edit|website.settings.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/pages', [WebsiteWebController::class, 'pages'])->name('website.pages.index');
            Route::post('admin/website/pages', [WebsiteWebController::class, 'pageStore'])->name('website.pages.store');
            Route::put('admin/website/pages/{page}', [WebsiteWebController::class, 'pageUpdate'])->name('website.pages.update');
            Route::delete('admin/website/pages/{page}', [WebsiteWebController::class, 'pageDestroy'])->name('website.pages.destroy');
            
            Route::get('admin/website/content', [WebsiteWebController::class, 'content'])->name('website.content');
            Route::post('admin/website/projects/stats', [WebsiteWebController::class, 'updateProjectStats'])->name('website.projects.stats.update');
            Route::post('admin/website/projects', [WebsiteWebController::class, 'storeProject'])->name('website.projects.store');
            Route::post('admin/website/projects/{project}', [WebsiteWebController::class, 'updateProjectContent'])->name('website.projects.update');
            Route::delete('admin/website/projects/{project}', [WebsiteWebController::class, 'destroyProject'])->name('website.projects.destroy');

            // Dynamic Cards
            Route::get('admin/website/cards', [WebsiteWebController::class, 'cards'])->name('website.cards.index');
            Route::post('admin/website/cards', [WebsiteWebController::class, 'cardStore'])->name('website.cards.store');
            Route::put('admin/website/cards/{card}', [WebsiteWebController::class, 'cardUpdate'])->name('website.cards.update');
            Route::delete('admin/website/cards/{card}', [WebsiteWebController::class, 'cardDestroy'])->name('website.cards.destroy');
        });

        Route::middleware('permission:website.campaigns_content.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/campaigns-content', [WebsiteWebController::class, 'campaignsContent'])->name('website.campaigns.content');
            Route::post('admin/website/campaigns/stats', [WebsiteWebController::class, 'updateCampaignStats'])->name('website.campaigns.stats.update');
            Route::post('admin/website/campaigns', [WebsiteWebController::class, 'storeCampaign'])->name('website.campaigns.store');
            Route::match(['POST', 'PUT'], 'admin/website/campaigns/{campaign}', [WebsiteWebController::class, 'updateCampaignContent'])->name('website.campaigns.update');
            Route::delete('admin/website/campaigns/{campaign}', [WebsiteWebController::class, 'destroyCampaign'])->name('website.campaigns.destroy');
        });

        Route::middleware('permission:website.news.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/news', [WebsiteWebController::class, 'news'])->name('website.news.index');
            Route::post('admin/website/news', [WebsiteWebController::class, 'newsStore'])->name('website.news.store');
            Route::put('admin/website/news/{news}', [WebsiteWebController::class, 'newsUpdate'])->name('website.news.update');
            Route::delete('admin/website/news/{news}', [WebsiteWebController::class, 'newsDestroy'])->name('website.news.destroy');
        });

        Route::middleware('permission:website.contact_messages.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/contact-messages', [WebsiteWebController::class, 'contactMessages'])->name('website.contact-messages.index');
            Route::patch('admin/website/contact-messages/{message}/read', [WebsiteWebController::class, 'contactMessageMarkRead'])->name('website.contact-messages.read');
            Route::delete('admin/website/contact-messages/{message}', [WebsiteWebController::class, 'contactMessageDestroy'])->name('website.contact-messages.destroy');
        });

        Route::middleware('permission:website.subscriptions.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/subscriptions', [WebsiteWebController::class, 'subscriptions'])->name('website.subscriptions.index');
            Route::delete('admin/website/subscriptions/{subscription}', [WebsiteWebController::class, 'destroySubscription'])->name('website.subscriptions.destroy');
        });

        Route::middleware('permission:website.guest_house_content.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/guest-house', [WebsiteWebController::class, 'guestHouseContent'])->name('website.guest-house.content');
            Route::get('admin/website/guest-house/slider', [WebsiteWebController::class, 'guestHouseSlider'])->name('website.guest-house.slider');
            Route::post('admin/website/guest-house/update', [WebsiteWebController::class, 'guestHouseContentUpdate'])->name('website.guest-house.update');
            Route::post('admin/website/guest-house/stats', [WebsiteWebController::class, 'updateGuestHouseStats'])->name('website.guest-house.update-stats');
            Route::post('admin/website/guest-house/booking-status/{booking}', [WebsiteWebController::class, 'bookingUpdateStatus'])->name('website.bookings.update');
        });

        Route::middleware('permission:website.donation_page.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/donation-page', [WebsiteWebController::class, 'donationPage'])->name('website.donation-page.index');
            Route::post('admin/website/donation-page', [WebsiteWebController::class, 'updateDonationPage'])->name('website.donation-page.update');
        });

        Route::middleware('permission:website.donation_settings.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/donation-settings/unified', [DonationCategoryWebController::class, 'unified'])->name('website.donation-settings.unified');
            Route::post('admin/website/donation-settings/categories/{donationCategory}/toggle', [DonationCategoryWebController::class, 'toggleStatus'])
                ->name('website.donation-settings.categories.toggle');
            Route::resource('admin/website/donation-settings/categories', DonationCategoryWebController::class, ['as' => 'website.donation-settings'])
                ->parameters(['categories' => 'donationCategory'])
                ->only(['index', 'store', 'update', 'destroy']);
            Route::post('admin/website/donation-settings/items/{donationItem}/toggle', [DonationItemWebController::class, 'toggleStatus'])
                ->name('website.donation-settings.items.toggle');
            Route::resource('admin/website/donation-settings/items', DonationItemWebController::class, ['as' => 'website.donation-settings'])
                ->parameters(['items' => 'donationItem'])
                ->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('permission:website.donation_accounts.view|website.view|manage_website')->group(function () {
            Route::get('admin/website/donation-accounts', [AdminWebsiteDonationWebController::class, 'index'])->name('website.donation-accounts.index');
            Route::get('admin/website/donation-accounts/{donor}', [AdminWebsiteDonationWebController::class, 'show'])->name('website.donation-accounts.show');
            Route::post('admin/website/donation-accounts/verify/{web_donation}', [AdminWebsiteDonationWebController::class, 'verifyDonation'])->name('website.donation-accounts.verify');
            Route::post('admin/website/donation-accounts/reject/{web_donation}', [AdminWebsiteDonationWebController::class, 'rejectDonation'])->name('website.donation-accounts.reject');
        });

        Route::middleware('permission:website.accounts.view|website.settings.view_edit|website.view|manage_website')->group(function () {
            Route::get('admin/website/accounts', [WebsiteWebController::class, 'accounts'])->name('website.accounts.index');
            Route::post('admin/website/accounts', [WebsiteWebController::class, 'accountStore'])->name('website.accounts.store');
            Route::put('admin/website/accounts/{user}', [WebsiteWebController::class, 'accountUpdate'])->name('website.accounts.update');
            Route::delete('admin/website/accounts/{user}', [WebsiteWebController::class, 'accountDestroy'])->name('website.accounts.destroy');
            
            // Testimonials & Opinions
            Route::get('admin/website/testimonials', [WebsiteWebController::class, 'testimonials'])->name('website.testimonials.index');
            Route::post('admin/website/testimonials', [WebsiteWebController::class, 'testimonialStore'])->name('website.testimonials.store');
            Route::put('admin/website/testimonials/{testimonial}', [WebsiteWebController::class, 'testimonialUpdate'])->name('website.testimonials.update');
            Route::delete('admin/website/testimonials/{testimonial}', [WebsiteWebController::class, 'testimonialDestroy'])->name('website.testimonials.destroy');
            Route::get('admin/website/share-opinion', [WebsiteWebController::class, 'shareOpinion'])->name('website.share-opinion');
        });
    });

    // --- Mobile App Management ---
    Route::group(['prefix' => 'admin/mobile', 'as' => 'mobile.'], function () {
        Route::middleware('permission:mobile.view|manage_mobile')->group(function () {
            Route::get('/', [MobileContentController::class, 'index'])->name('dashboard');
            Route::put('/projects/{project}/mobile-content', [MobileContentController::class, 'updateProjectMobileContent'])->name('projects.update.mobile');
            Route::put('/campaigns/{campaign}/mobile-content', [MobileContentController::class, 'updateCampaignMobileContent'])->name('campaigns.update.mobile');
            Route::post('/pillars', [MobileContentController::class, 'pillarStore'])->name('pillars.store');
            Route::put('/pillars/{pillar}', [MobileContentController::class, 'pillarUpdate'])->name('pillars.update');
            Route::delete('/pillars/{pillar}', [MobileContentController::class, 'pillarDestroy'])->name('pillars.destroy');
            Route::get('/contact-info', [MobileContentController::class, 'contactInfoIndex'])->name('contact_info.index');
            Route::post('/contact-info', [MobileContentController::class, 'contactInfoStore'])->name('contact_info.store');
            Route::post('/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoUpdate'])->name('contact_info.update');
            Route::delete('/contact-info/{contactInfo}', [MobileContentController::class, 'contactInfoDestroy'])->name('contact_info.destroy');
        });
        
        Route::middleware('permission:mobile.home_content.view_edit|mobile.view|manage_mobile')->group(function () {
            Route::get('/home-content', [MobileContentController::class, 'homeContentIndex'])->name('home_content.index');
            Route::post('/home-content', [MobileContentController::class, 'homeContentStore'])->name('home_content.store');
            Route::match(['POST', 'PUT'], '/home-content/{item}', [MobileContentController::class, 'homeContentUpdate'])->name('home_content.update');
            Route::delete('/home-content/{item}', [MobileContentController::class, 'homeContentDestroy'])->name('home_content.destroy');
            Route::get('/banners', [MobileContentController::class, 'bannersIndex'])->name('banners.index');
            Route::post('/banners', [MobileContentController::class, 'bannerStore'])->name('banners.store');
            Route::delete('/banners/{banner}', [MobileContentController::class, 'bannerDestroy'])->name('banners.destroy');
        });

        Route::middleware('permission:mobile.news.view|mobile.view|manage_mobile')->group(function () {
            Route::get('/news', [MobileContentController::class, 'newsIndex'])->name('news.index');
            Route::post('/news', [MobileContentController::class, 'newsStore'])->name('news.store');
            Route::post('/news/{news}', [MobileContentController::class, 'newsUpdate'])->name('news.update');
            Route::delete('/news/{news}', [MobileContentController::class, 'newsDestroy'])->name('news.destroy');
        });

        Route::middleware('permission:mobile.notifications.view|mobile.view|manage_mobile')->group(function () {
            Route::get('/notifications', [MobileContentController::class, 'notificationsIndex'])->name('notifications.index');
            Route::post('/notifications', [MobileContentController::class, 'notificationStore'])->name('notifications.store');
            Route::match(['POST', 'PUT'], '/notifications/{notification}', [MobileContentController::class, 'notificationUpdate'])->name('notifications.update');
            Route::delete('/notifications/{notification}', [MobileContentController::class, 'notificationDestroy'])->name('notifications.destroy');
        });

        Route::middleware('permission:mobile.donations.view|mobile.view|manage_mobile')->group(function () {
            Route::get('/donations', [MobileContentController::class, 'donationsIndex'])->name('donations.index');
            Route::match(['post', 'patch'], '/donations/{donation}/status', [MobileContentController::class, 'updateDonationStatus'])->name('donations.update');
            Route::delete('/donations/{donation}', [MobileContentController::class, 'destroyDonation'])->name('donations.destroy');
            Route::get('/in-kind-donations', [MobileContentController::class, 'inKindDonationsIndex'])->name('inkind.index');
            Route::patch('/in-kind-donations/{donation}/status', [MobileContentController::class, 'inKindDonationUpdateStatus'])->name('inkind.update');
        });

        Route::middleware('permission:mobile.donors.view|mobile.view|manage_mobile')->group(function () {
            Route::get('/donors-auth', [MobileContentController::class, 'mobileDonorsIndex'])->name('donors_auth.index');
            Route::put('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorUpdate'])->name('donors_auth.update');
            Route::delete('/donors-auth/{user}', [MobileContentController::class, 'mobileDonorDestroy'])->name('donors_auth.destroy');
        });

        Route::middleware('permission:mobile.bookings.view|mobile.view|manage_mobile')->group(function () {
            Route::get('/bookings', [MobileContentController::class, 'bookingsIndex'])->name('bookings.index');
            Route::post('/bookings/{booking}/status', [MobileContentController::class, 'updateBookingStatus'])->name('bookings.update');
            Route::delete('/bookings/{booking}', [MobileContentController::class, 'destroyBooking'])->name('bookings.destroy');
            
            // Alias for web_bookings to support bookings.blade.php
            Route::post('/web-bookings/{booking}/status', [MobileContentController::class, 'updateBookingStatus'])->name('web_bookings.update');
            Route::delete('/web-bookings/{booking}', [MobileContentController::class, 'destroyBooking'])->name('web_bookings.destroy');
        });
    });

    // --- Initial Research & Case Applications (منظومة البحث المبدئي واستقبال الحالات) ---
    Route::group(['prefix' => 'admin/mobile', 'as' => 'mobile.'], function () {
        Route::middleware('permission:mobile.case_applications.view')->group(function () {
            Route::get('/case-applications', [MobileContentController::class, 'caseApplicationsIndex'])->name('case-applications.index');
            Route::post('/case-applications', [MobileContentController::class, 'storeCaseApplication'])->name('case-applications.store');
            Route::get('/case-applications/{application}/print', [MobileContentController::class, 'printCaseApplication'])->name('case-applications.print');
            Route::post('/case-applications/{application}/status', [MobileContentController::class, 'updateCaseApplicationStatus'])->name('case-applications.update');
            Route::delete('/case-applications/{application}', [MobileContentController::class, 'destroyCaseApplication'])->name('case-applications.destroy');
            Route::post('/case-applications/bulk-destroy', [MobileContentController::class, 'bulkDestroyCaseApplications'])->name('case-applications.bulk-destroy');
        });
    });

    // Direct and Legacy Route Aliases for Case Applications
    Route::middleware('permission:mobile.case_applications.view')->group(function () {
        Route::get('/case-applications', [MobileContentController::class, 'caseApplicationsIndex'])->name('case-applications.index');
        Route::post('/case-applications', [MobileContentController::class, 'storeCaseApplication'])->name('case-applications.store');
        Route::get('/case-applications/{application}/print', [MobileContentController::class, 'printCaseApplication'])->name('case-applications.print');
    });

    // Change Requests Routes (Viewable by authenticated staff, approvals managed by controllers)
    Route::get('admin/change-requests', [\App\Http\Controllers\ChangeRequestWebController::class, 'index'])->name('change-requests.index');
    Route::post('admin/change-requests/{changeRequest}/update-payload', [\App\Http\Controllers\ChangeRequestWebController::class, 'updatePayload'])->name('change-requests.update-payload');
    Route::post('admin/change-requests/{changeRequest}/edit-and-approve', [\App\Http\Controllers\ChangeRequestWebController::class, 'editAndApprove'])->name('change-requests.edit-and-approve');
    Route::post('admin/change-requests/{changeRequest}/resubmit', [\App\Http\Controllers\ChangeRequestWebController::class, 'resubmit'])->name('change-requests.resubmit');
    Route::post('admin/change-requests/{changeRequest}/approve', [\App\Http\Controllers\ChangeRequestWebController::class, 'approve'])->name('change-requests.approve');
    Route::post('admin/change-requests/{changeRequest}/reject', [\App\Http\Controllers\ChangeRequestWebController::class, 'reject'])->name('change-requests.reject');
    Route::delete('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class, 'destroy'])->name('change-requests.destroy');
    Route::post('admin/change-requests/bulk-destroy', [\App\Http\Controllers\ChangeRequestWebController::class, 'bulkDestroy'])->name('change-requests.bulk-destroy');
    Route::post('admin/change-requests/bulk-revert', [\App\Http\Controllers\ChangeRequestWebController::class, 'bulkRevert'])->name('change-requests.bulk-revert');
    Route::post('admin/change-requests/{changeRequest}/revert', [\App\Http\Controllers\ChangeRequestWebController::class, 'revert'])->name('change-requests.revert');
    Route::post('admin/change-requests/{changeRequest}/cancel', [\App\Http\Controllers\ChangeRequestWebController::class, 'cancel'])->name('change-requests.cancel');
    Route::put('admin/change-requests/{changeRequest}', [\App\Http\Controllers\ChangeRequestWebController::class, 'update'])->name('change-requests.update');

    // --- Specialized Services ---
    Route::middleware('permission:workspaces.view|manage_specialized_services')->group(function () {
        Route::resource('workspaces', \App\Http\Controllers\WorkspaceWebController::class);
        Route::post('workspaces/{workspace}/rentals', [\App\Http\Controllers\WorkspaceWebController::class, 'storeRental'])->name('workspaces.storeRental');
        Route::patch('workspaces/{workspace}/rentals/{rental}/status', [\App\Http\Controllers\WorkspaceWebController::class, 'updateRentalStatus'])->name('workspaces.updateRentalStatus');
        Route::delete('workspaces/{workspace}/rentals/{rental}', [\App\Http\Controllers\WorkspaceWebController::class, 'destroyRental'])->name('workspaces.destroyRental');
    });
    Route::middleware('permission:school_collaborations.view|manage_specialized_services')->resource('school-collaborations', \App\Http\Controllers\SchoolCollaborationWebController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:memberships.view|manage_specialized_services')->resource('memberships', \App\Http\Controllers\MembershipWebController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:oncology_medicine_reps.view|manage_specialized_services')->resource('oncology-medicine-reps', \App\Http\Controllers\OncologyMedicineRepWebController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:kafr_el_sheikh_brokers.view|kafr_el_sheikh_deliveries.view|manage_specialized_services|manage_logistics')->resource('kafr-el-sheikh-brokers', \App\Http\Controllers\KafrElSheikhBrokerWebController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:kafr_el_sheikh_deliveries.view|kafr_el_sheikh_services.view|manage_specialized_services|manage_logistics')->resource('kafr-el-sheikh-deliveries', \App\Http\Controllers\KafrElSheikhDeliveryWebController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:kafr_el_sheikh_services.view|kafr_el_sheikh_deliveries.view|manage_specialized_services|manage_logistics')
        ->resource('kafr-el-sheikh-services', \App\Http\Controllers\KafrElSheikhServiceWebController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::middleware('permission:tanta_workers.view|manage_logistics|manage_specialized_services')->resource('tanta-workers', \App\Http\Controllers\TantaWorkerWebController::class)->only(['index', 'store', 'update', 'destroy']);

    // Guest houses use their own granular permissions and must not depend on
    // the unrelated specialized-services umbrella permission.
    Route::middleware('permission:guest_houses.create')->group(function () {
        Route::resource('guest-houses', GuestHouseWebController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:guest_houses.view')->group(function () {
        Route::resource('guest-houses', GuestHouseWebController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:guest_houses.edit')->group(function () {
        Route::resource('guest-houses', GuestHouseWebController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:guest_houses.delete')->group(function () {
        Route::resource('guest-houses', GuestHouseWebController::class)->only(['destroy']);
    });
    Route::middleware('permission:guest_houses.set_manager')
        ->post('guest-houses/{guest_house}/manager', [GuestHouseWebController::class, 'setManager'])
        ->name('guest-houses.setManager');
    Route::middleware('permission:guest_houses.manage_volunteers')->group(function () {
        Route::post('guest-houses/{guest_house}/volunteers', [GuestHouseWebController::class, 'attachVolunteer'])
            ->name('guest-houses.attachVolunteer');
        Route::delete('guest-houses/{guest_house}/volunteers/{user}', [GuestHouseWebController::class, 'detachVolunteer'])
            ->name('guest-houses.detachVolunteer');
    });
    Route::middleware('permission:guest_houses.manage_monthly_volunteers')->group(function () {
        Route::post('guest-houses/{guest_house}/monthly-volunteers', [GuestHouseWebController::class, 'storeMonthlyVolunteer'])
            ->name('guest-houses.storeMonthlyVolunteer');
        Route::delete('guest-houses/{guest_house}/monthly-volunteers/{monthlyVolunteer}', [GuestHouseWebController::class, 'destroyMonthlyVolunteer'])
            ->name('guest-houses.destroyMonthlyVolunteer');
    });
    Route::post('guest-houses/{guest_house}/beneficiaries', [GuestHouseWebController::class, 'storeBeneficiary'])
        ->middleware(['permission:guest_houses.view'])
        ->name('guest-houses.storeBeneficiary');
    Route::post('guest-houses/{guest_house}/beneficiaries/{beneficiary}/manager-notes', [GuestHouseWebController::class, 'saveManagerNotes'])
        ->middleware(['permission:guest_houses.view'])
        ->name('guest-houses.beneficiaries.saveNotes');
    Route::post('guest-houses/{guest_house}/beneficiaries/{beneficiary}/update-companion', [GuestHouseWebController::class, 'updateCompanion'])
        ->middleware(['permission:guest_houses.view'])
        ->name('guest-houses.beneficiaries.update-companion');
    Route::post('guest-houses/{guest_house}/donations', [GuestHouseWebController::class, 'storeDonation'])
        ->middleware(['permission:guest_houses.view'])
        ->name('guest-houses.storeDonation');
    Route::post('guest-houses/{guest_house}/expenses', [GuestHouseWebController::class, 'storeExpense'])
        ->middleware(['permission:guest_houses.view'])
        ->name('guest-houses.storeExpense');
    Route::middleware('permission:guest_houses.view')->group(function () {
        Route::post('guest-houses/{guest_house}/wings', [GuestHouseWebController::class, 'storeWing'])->name('guest-houses.wings.store');
        Route::put('guest-houses/{guest_house}/wings/{wing}', [GuestHouseWebController::class, 'updateWing'])->name('guest-houses.wings.update');
        Route::delete('guest-houses/{guest_house}/wings/{wing}', [GuestHouseWebController::class, 'destroyWing'])->name('guest-houses.wings.destroy');
        Route::post('guest-houses/{guest_house}/wings/{wing}/beds', [GuestHouseWebController::class, 'storeBed'])->name('guest-houses.wings.beds.store');
        Route::put('guest-houses/{guest_house}/beds/{bed}', [GuestHouseWebController::class, 'updateBed'])->name('guest-houses.beds.update');
        Route::delete('guest-houses/{guest_house}/beds/{bed}', [GuestHouseWebController::class, 'destroyBed'])->name('guest-houses.beds.destroy');
        Route::patch('guest-houses/{guest_house}/beds/{bed}/status', [GuestHouseWebController::class, 'updateBedStatus'])->name('guest-houses.beds.status');
        Route::post('guest-houses/{guest_house}/custodies', [GuestHouseWebController::class, 'storeCustody'])->name('guest-houses.custodies.store');
        Route::post('guest-houses/{guest_house}/inventory/issues', [GuestHouseWebController::class, 'issueInventory'])->name('guest-houses.inventory.issue');
        Route::post('guest-houses/{guest_house}/inventory/supply', [GuestHouseWebController::class, 'supplyInventory'])->name('guest-houses.inventory.supply');
        Route::post('guest-houses/{guest_house}/meals', [GuestHouseWebController::class, 'storeMeal'])->name('guest-houses.meals.store');
        Route::post('guest-houses/{guest_house}/stays/{stay}/depart', [GuestHouseWebController::class, 'departStay'])->name('guest-houses.stays.depart');
        Route::post('guest-houses/{guest_house}/stays/{stay}/temporary-depart', [GuestHouseWebController::class, 'temporaryDepartStay'])->name('guest-houses.stays.temporaryDepart');
        Route::post('guest-houses/{guest_house}/stays/{stay}/return-temporary', [GuestHouseWebController::class, 'returnFromTemporaryDepart'])->name('guest-houses.stays.returnTemporary');
        Route::post('guest-houses/{guest_house}/stays/{stay}/return', [GuestHouseWebController::class, 'returnStay'])->name('guest-houses.stays.return');
        Route::post('guest-houses/{guest_house}/bookings/{booking}/decision', [GuestHouseWebController::class, 'decideBooking'])->name('guest-houses.bookings.decision');
        Route::post('guest-houses/{guest_house}/mobile-cases/{application}/decision', [GuestHouseWebController::class, 'decideMobileCase'])->name('guest-houses.mobile-cases.decision');
        Route::post('guest-houses/{guest_house}/petty-cash/expenses', [GuestHouseWebController::class, 'storePettyExpense'])->name('guest-houses.petty-cash.expenses.store');
        Route::post('guest-houses/{guest_house}/petty-cash/replenish', [GuestHouseWebController::class, 'replenishPettyCash'])->name('guest-houses.petty-cash.replenish');
        Route::post('guest-houses/{guest_house}/assets', [GuestHouseWebController::class, 'storeAsset'])->name('guest-houses.assets.store');
        Route::patch('guest-houses/{guest_house}/assets/{asset}/status', [GuestHouseWebController::class, 'updateAssetStatus'])->name('guest-houses.assets.status');
        Route::post('guest-houses/{guest_house}/assets/{asset}/maintenances', [GuestHouseWebController::class, 'storeAssetMaintenance'])->name('guest-houses.assets.maintenances.store');
        Route::post('guest-houses/{guest_house}/support-tickets', [GuestHouseWebController::class, 'storeSupportTicket'])->name('guest-houses.support-tickets.store');
        Route::patch('guest-houses/{guest_house}/support-tickets/{ticket}/status', [GuestHouseWebController::class, 'updateSupportTicketStatus'])->name('guest-houses.support-tickets.status');
    });

    // Ramadan Campaign
    Route::middleware('permission:ramadan_bags.view|manage_ramadan')->group(function () {
        Route::resource('ramadan-bags', \App\Http\Controllers\RamadanBagWebController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::delete('ramadan-bags/{ramadan_bag}', [\App\Http\Controllers\RamadanBagWebController::class, 'destroy'])->name('ramadan-bags.destroy');
    });
    Route::middleware('permission:ramadan_iftars.view|manage_ramadan')->group(function () {
        Route::resource('ramadan-iftars', \App\Http\Controllers\RamadanIftarWebController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::delete('ramadan-iftars/{ramadan_iftar}', [\App\Http\Controllers\RamadanIftarWebController::class, 'destroy'])->name('ramadan-iftars.destroy');
    });

    // Reception & Visits (Shared/General)
    Route::middleware('permission:visits.view')->group(function () {
        Route::get('visits/{visit}/print', [\App\Http\Controllers\VisitWebController::class, 'print'])->name('visits.print');
        Route::post('visits/{visit}/final-approval', [\App\Http\Controllers\VisitWebController::class, 'finalApproval'])->name('visits.final-approval');
        Route::resource('visits', \App\Http\Controllers\VisitWebController::class);
    });
    Route::middleware('permission:reception.view')->group(function () {
        Route::resource('reception', \App\Http\Controllers\ReceptionWebController::class)
            ->only(['index', 'store']);
        Route::post('reception/expenses', [\App\Http\Controllers\ReceptionWebController::class, 'storeExpense'])
            ->name('reception.expenses.store');
        Route::post('reception/custody-deposit', [\App\Http\Controllers\ReceptionWebController::class, 'storeCustodyDeposit'])
            ->name('reception.custody.deposit');
    });
    Route::middleware('permission:tasks.view|view_own_tasks|employee_tasks.view|employee_tasks.view_own|volunteer_tasks.view')->group(function () {
        Route::resource('tasks', TaskWebController::class);
    });

});

// Developer Information Route
Route::get('developer', function () {
    return view('developer.index');
})->name('developer.info');
