<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ArabicSearchHelper;
use App\Models\Beneficiary;
use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\Complaint;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\FieldVisit;
use App\Models\GuestHouse;
use App\Models\MobileCaseApplication;
use App\Models\Project;
use App\Models\SchoolCollaboration;
use App\Models\Treasury;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([
                'query' => $q,
                'total' => 0,
                'categories' => [],
            ]);
        }

        $parsed = ArabicSearchHelper::parseQuery($q);
        $cleanQ = $parsed['raw'];
        $variants = $parsed['variants'];
        $words = $parsed['words'];
        $numbers = $parsed['numbers'];

        $results = [
            'beneficiaries' => [],
            'case_applications' => [],
            'collaborations' => [],
            'projects' => [],
            'donors' => [],
            'employees' => [],
            'volunteers' => [],
            'donations' => [],
            'complaints' => [],
        ];

        // 1. Beneficiaries / الحالات والمستفيدون (وأفراد الأسرة)
        $beneficiariesQuery = Beneficiary::query()->with([
            'familyMembers' => fn ($fm) => $fm->where(fn ($sq) => $sq->whereNull('active')->orWhere('active', true)->orWhere('active', 1)),
        ]);

        if (preg_match('/^\d{2}$/', $cleanQ)) {
            $beneficiariesQuery->where(function ($w) use ($cleanQ, $q) {
                $w->where('code', 'like', "{$cleanQ}%")
                  ->orWhere('code', 'like', "G-{$cleanQ}%")
                  ->orWhere('code', 'like', "B{$cleanQ}%")
                  ->orWhere('patient_code', 'like', "{$cleanQ}%")
                  ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', "{$cleanQ}%")->orWhere('code', 'like', "B{$cleanQ}%"))
                  ->orWhere('full_name', 'like', "%{$q}%");
            });
        } elseif (preg_match('/^(?:19|20)(\d{2})$/', $cleanQ, $matches)) {
            $yr = $matches[1];
            $beneficiariesQuery->where(function ($w) use ($yr, $cleanQ, $q) {
                $w->where('code', 'like', "{$yr}%")
                  ->orWhere('code', 'like', "G-{$yr}%")
                  ->orWhere('code', 'like', "B{$yr}%")
                  ->orWhere('code', 'like', "%{$cleanQ}%")
                  ->orWhere('patient_code', 'like', "{$yr}%")
                  ->orWhereHas('familyMembers', fn ($m) => $m->where('code', 'like', "{$yr}%")->orWhere('code', 'like', "B{$yr}%"))
                  ->orWhere('full_name', 'like', "%{$q}%");
            });
        } else {
            $beneficiariesQuery->where(function ($w) use ($cleanQ, $variants, $words, $numbers) {
                ArabicSearchHelper::applySmartSearch(
                    $w,
                    $cleanQ,
                    ['full_name', 'patient_name', 'guardian_name', 'mother_name', 'deceased_husband_name', 'address'],
                    ['code', 'patient_code', 'national_id', 'phone', 'backup_phone', 'visa_card_number', 'reference_number']
                );

                // Also search inside familyMembers (children, patients, spouses)
                $w->orWhereHas('familyMembers', function ($fm) use ($cleanQ) {
                    ArabicSearchHelper::applySmartSearch(
                        $fm,
                        $cleanQ,
                        ['full_name'],
                        ['code', 'national_id', 'phone']
                    );
                });
            });
        }

        $beneficiaries = $beneficiariesQuery
            ->select(['id', 'code', 'full_name', 'patient_name', 'phone', 'national_id', 'assistance_type', 'status'])
            ->limit(6)
            ->get();

        foreach ($beneficiaries as $b) {
            $codeStr = $b->code ?: ('BEN-' . $b->id);
            $subParts = [];
            if ($b->phone) $subParts[] = 'هاتف: ' . $b->phone;
            if ($b->national_id) $subParts[] = 'رقم قومي: ' . $b->national_id;
            if ($b->assistance_type) $subParts[] = $b->assistance_type;

            // Check if a family member matched specifically
            $matchedMemberTitle = null;
            if ($b->relationLoaded('familyMembers') && $b->familyMembers->isNotEmpty()) {
                foreach ($b->familyMembers as $m) {
                    $mNorm = ArabicSearchHelper::normalize((string) $m->full_name);
                    $qNorm = ArabicSearchHelper::normalize($cleanQ);
                    if (
                        ($qNorm !== '' && mb_stripos($mNorm, $qNorm) !== false)
                        || ($m->national_id && stripos((string) $m->national_id, $cleanQ) !== false)
                        || ($m->code && stripos((string) $m->code, $cleanQ) !== false)
                    ) {
                        $matchedMemberTitle = $m->full_name . ' (أسرة: ' . $b->full_name . ')';
                        if ($m->national_id && !$b->national_id) {
                            $subParts[] = 'قومي الفرد: ' . $m->national_id;
                        }
                        if ($m->code) {
                            $codeStr = $m->code;
                        }
                        break;
                    }
                }
            }

            $results['beneficiaries'][] = [
                'id' => $b->id,
                'title' => $matchedMemberTitle ?: $b->full_name,
                'code' => $codeStr,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('beneficiaries.show', $b),
                'badge' => 'حالة / مستفيد',
                'badge_class' => 'bg-danger-subtle text-danger',
                'icon' => 'bi-heart-pulse-fill',
            ];
        }

        // 2. Initial Research & Case Applications + Field Visits / طلبات البحث المبدئي والأبحاث الميدانية
        $caseAppsQuery = MobileCaseApplication::query();
        ArabicSearchHelper::applySmartSearch(
            $caseAppsQuery,
            $cleanQ,
            ['applicant_name', 'submitter_name', 'mother_name', 'deceased_husband_name', 'guide_name', 'address', 'city'],
            ['applicant_id_number', 'applicant_phone', 'backup_phone', 'submitter_phone', 'guide_phone', 'reference_number']
        );

        $caseApps = $caseAppsQuery->latest('created_at')->limit(6)->get();

        foreach ($caseApps as $app) {
            $subParts = [];
            if ($app->applicant_phone) $subParts[] = 'هاتف: ' . $app->applicant_phone;
            if ($app->applicant_id_number) $subParts[] = 'رقم قومي: ' . $app->applicant_id_number;
            $subParts[] = $app->status_label;

            $searchParam = $app->applicant_id_number ?: ($app->applicant_phone ?: $app->applicant_name);
            $results['case_applications'][] = [
                'id' => $app->id,
                'title' => $app->applicant_name,
                'code' => $app->reference_number ?: ('APP-' . $app->id),
                'subtitle' => implode(' • ', $subParts),
                'url' => route('case-applications.index', ['q' => $searchParam]),
                'badge' => 'طلب بحث (' . $app->status_label . ')',
                'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                'icon' => 'bi-clipboard2-pulse-fill',
            ];
        }

        // Field Visits / استمارات البحث الميداني
        $visitsQuery = FieldVisit::query();
        ArabicSearchHelper::applySmartSearch(
            $visitsQuery,
            $cleanQ,
            ['patient_name', 'guardian_name', 'address', 'case_guide'],
            ['national_id', 'phone', 'researcher_phone']
        );
        $visits = $visitsQuery->latest('id')->limit(4)->get();

        foreach ($visits as $visit) {
            $vName = $visit->patient_name ?: ($visit->guardian_name ?: ('بحث ميداني #' . $visit->id));
            $subParts = [];
            if ($visit->phone) $subParts[] = 'هاتف: ' . $visit->phone;
            if ($visit->national_id) $subParts[] = 'رقم قومي: ' . $visit->national_id;
            if ($visit->address) $subParts[] = $visit->address;

            $results['case_applications'][] = [
                'id' => 'fv-' . $visit->id,
                'title' => $vName,
                'code' => 'VST-' . $visit->id,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('visits.show', $visit),
                'badge' => 'بحث ميداني',
                'badge_class' => 'bg-info-subtle text-info',
                'icon' => 'bi-clipboard-check-fill',
            ];
        }

        // 3. Collaborations & Partnerships / التعاونات والشراكات والشركات المتعاقدة
        $collabsQuery = Collaboration::query();
        ArabicSearchHelper::applySmartSearch(
            $collabsQuery,
            $cleanQ,
            ['name', 'specialty', 'service_provided', 'contact_person', 'category', 'city', 'address', 'notes'],
            ['phone', 'phone_alt', 'contact_person_phone']
        );
        $collabs = $collabsQuery->limit(6)->get();

        foreach ($collabs as $col) {
            $subParts = [];
            if ($col->category) $subParts[] = $col->category;
            if ($col->specialty) $subParts[] = $col->specialty;
            if ($col->phone) $subParts[] = 'هاتف: ' . $col->phone;
            elseif ($col->contact_person_phone) $subParts[] = 'هاتف: ' . $col->contact_person_phone;

            $results['collaborations'][] = [
                'id' => $col->id,
                'title' => $col->name,
                'code' => 'COL-' . $col->id,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('collaborations.index', ['search' => $col->name]),
                'badge' => 'شراكة وتعاون',
                'badge_class' => 'bg-success-subtle text-success',
                'icon' => $col->category_icon ?: 'bi-diagram-3-fill',
            ];
        }

        // School Collaborations / تعاونات المدارس والمكتبات
        $schoolCollabsQuery = SchoolCollaboration::query();
        ArabicSearchHelper::applySmartSearch(
            $schoolCollabsQuery,
            $cleanQ,
            ['shop_name', 'address', 'campaign', 'notes'],
            ['phone']
        );
        foreach ($schoolCollabsQuery->limit(3)->get() as $sc) {
            $subParts = [];
            if ($sc->phone) $subParts[] = 'هاتف: ' . $sc->phone;
            if ($sc->address) $subParts[] = $sc->address;
            if ($sc->discount) $subParts[] = 'خصم: ' . $sc->discount;

            $results['collaborations'][] = [
                'id' => 'sc-' . $sc->id,
                'title' => $sc->shop_name,
                'code' => 'SCH-' . $sc->id,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('school-collaborations.index', ['q' => $sc->shop_name]),
                'badge' => 'تعاون مدارس',
                'badge_class' => 'bg-success-subtle text-success',
                'icon' => 'bi-shop',
            ];
        }

        // 4. Projects, Campaigns & Guest Houses / المشاريع والحملات ودور الضيافة
        $projectsQuery = Project::query();
        ArabicSearchHelper::applySmartSearch($projectsQuery, $cleanQ, ['name', 'description'], ['code']);
        foreach ($projectsQuery->limit(4)->get() as $proj) {
            $results['projects'][] = [
                'id' => $proj->id,
                'title' => $proj->name,
                'code' => $proj->code ?: ('PRJ-' . $proj->id),
                'subtitle' => 'مشروع خيري',
                'url' => route('projects.show', $proj),
                'badge' => 'مشروع',
                'badge_class' => 'bg-primary-subtle text-primary',
                'icon' => 'bi-kanban-fill',
            ];
        }

        $campaignsQuery = Campaign::query();
        ArabicSearchHelper::applySmartSearch($campaignsQuery, $cleanQ, ['name'], []);
        foreach ($campaignsQuery->limit(3)->get() as $camp) {
            $results['projects'][] = [
                'id' => 'cmp-' . $camp->id,
                'title' => $camp->name,
                'code' => 'CMP-' . $camp->id,
                'subtitle' => 'حملة خيرية',
                'url' => route('campaigns.show', $camp),
                'badge' => 'حملة',
                'badge_class' => 'bg-info-subtle text-info',
                'icon' => 'bi-megaphone-fill',
            ];
        }

        $guestHousesQuery = GuestHouse::query();
        ArabicSearchHelper::applySmartSearch($guestHousesQuery, $cleanQ, ['name', 'location'], []);
        foreach ($guestHousesQuery->limit(3)->get() as $gh) {
            $results['projects'][] = [
                'id' => 'gh-' . $gh->id,
                'title' => $gh->name,
                'code' => 'GH-' . $gh->id,
                'subtitle' => $gh->location ?: 'دار ضيافة',
                'url' => route('guest-houses.show', $gh),
                'badge' => 'دار ضيافة',
                'badge_class' => 'bg-danger-subtle text-danger',
                'icon' => 'bi-house-heart-fill',
            ];
        }

        // 5. Donors / المتبرعون
        $donorsQuery = Donor::query();
        ArabicSearchHelper::applySmartSearch(
            $donorsQuery,
            $cleanQ,
            ['name', 'address', 'notes'],
            ['code', 'phone', 'national_id']
        );
        $donors = $donorsQuery
            ->select(['id', 'code', 'name', 'phone', 'classification', 'sponsorship_monthly_amount'])
            ->limit(6)
            ->get();

        foreach ($donors as $d) {
            $codeStr = $d->code ?: ('D-' . str_pad((string) $d->id, 5, '0', STR_PAD_LEFT));
            $subParts = [];
            if ($d->phone) $subParts[] = 'هاتف: ' . $d->phone;
            if ($d->classification === 'recurring') {
                $subParts[] = 'متبرع شهري' . ($d->sponsorship_monthly_amount > 0 ? ' (' . number_format((float) $d->sponsorship_monthly_amount) . ' ج.م)' : '');
            } else {
                $subParts[] = 'متبرع عام';
            }

            $results['donors'][] = [
                'id' => $d->id,
                'title' => $d->name,
                'code' => $codeStr,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('donors.show', $d),
                'action_url' => route('donations.create', ['donor_id' => $d->id]),
                'action_label' => 'تسجيل تبرع',
                'badge' => 'متبرع',
                'badge_class' => 'bg-success-subtle text-success',
                'icon' => 'bi-hand-thumbs-up-fill',
            ];
        }

        // 6. Staff / Employees (الموظفون وفريق العمل)
        $employeesQuery = User::query()
            ->where(function ($w) {
                $w->where('is_volunteer', false)->orWhereNull('is_volunteer');
            });
        ArabicSearchHelper::applySmartSearch(
            $employeesQuery,
            $cleanQ,
            ['name', 'job_title', 'department'],
            ['phone', 'email', 'code']
        );
        $employees = $employeesQuery
            ->select(['id', 'code', 'name', 'phone', 'job_title', 'department', 'role'])
            ->limit(5)
            ->get();

        foreach ($employees as $emp) {
            $subParts = [];
            if ($emp->job_title) $subParts[] = $emp->job_title;
            if ($emp->department) $subParts[] = $emp->department;
            if ($emp->phone) $subParts[] = 'هاتف: ' . $emp->phone;

            $results['employees'][] = [
                'id' => $emp->id,
                'title' => $emp->name,
                'code' => $emp->code ?: ('EMP-' . $emp->id),
                'subtitle' => implode(' • ', $subParts),
                'url' => route('users.show', $emp),
                'badge' => 'موظف / فريق العمل',
                'badge_class' => 'bg-primary-subtle text-primary',
                'icon' => 'bi-person-badge-fill',
            ];
        }

        // 7. Volunteers (المتطوعون)
        $volunteersQuery = User::query()->where('is_volunteer', true);
        ArabicSearchHelper::applySmartSearch(
            $volunteersQuery,
            $cleanQ,
            ['name', 'city'],
            ['phone', 'code']
        );
        $volunteers = $volunteersQuery
            ->select(['id', 'code', 'name', 'phone', 'city', 'volunteer_hours'])
            ->limit(5)
            ->get();

        foreach ($volunteers as $v) {
            $subParts = [];
            if ($v->phone) $subParts[] = 'هاتف: ' . $v->phone;
            if ($v->city) $subParts[] = $v->city;
            if ($v->volunteer_hours) $subParts[] = $v->volunteer_hours . ' ساعة تطوع';

            $results['volunteers'][] = [
                'id' => $v->id,
                'title' => $v->name,
                'code' => $v->code ?: ('V-' . $v->id),
                'subtitle' => implode(' • ', $subParts),
                'url' => route('volunteers.show', $v),
                'badge' => 'متطوع',
                'badge_class' => 'bg-warning-subtle text-warning-emphasis',
                'icon' => 'bi-people-fill',
            ];
        }

        // 8. Donations & Treasuries (إيصالات التبرع والخزائن)
        $donations = Donation::query()
            ->with(['donor:id,name'])
            ->where(function ($w) use ($cleanQ, $numbers) {
                $w->where('receipt_number', 'like', "%{$cleanQ}%")
                  ->orWhere('operation_number', 'like', "%{$cleanQ}%");
                foreach ($numbers as $num) {
                    if (strlen($num) >= 2) {
                        $w->orWhere('receipt_number', 'like', "%{$num}%")
                          ->orWhere('operation_number', 'like', "%{$num}%");
                    }
                }
            })
            ->select(['id', 'receipt_number', 'operation_number', 'donor_id', 'amount', 'type', 'created_at', 'status'])
            ->limit(4)
            ->get();

        foreach ($donations as $dn) {
            $recCode = $dn->receipt_number ?: ($dn->operation_number ?: $dn->id);
            $results['donations'][] = [
                'id' => $dn->id,
                'title' => 'إيصال / عملية #' . $recCode,
                'code' => 'REC-' . $recCode,
                'subtitle' => 'المبلغ: ' . number_format((float) $dn->amount, 2) . ' ج.م • المتبرع: ' . ($dn->donor?->name ?? 'فاعل خير'),
                'url' => route('donations.show', $dn),
                'badge' => 'إيصال تبرع',
                'badge_class' => 'bg-info-subtle text-info',
                'icon' => 'bi-receipt',
            ];
        }

        $treasuriesQuery = Treasury::query();
        ArabicSearchHelper::applySmartSearch($treasuriesQuery, $cleanQ, ['name', 'location', 'description'], ['code']);
        foreach ($treasuriesQuery->limit(3)->get() as $tr) {
            $results['donations'][] = [
                'id' => 'tr-' . $tr->id,
                'title' => $tr->name,
                'code' => $tr->code ?: ('TR-' . $tr->id),
                'subtitle' => 'الرصيد: ' . number_format((float) $tr->current_balance, 2) . ' ' . $tr->currency,
                'url' => route('treasuries.show', $tr),
                'badge' => 'خزينة / عهدة',
                'badge_class' => 'bg-success-subtle text-success',
                'icon' => 'bi-safe2-fill',
            ];
        }

        // 9. Complaints / الشكاوى والمقترحات
        $complaintsQuery = Complaint::query();
        ArabicSearchHelper::applySmartSearch(
            $complaintsQuery,
            $cleanQ,
            ['complainant_name', 'subject', 'message'],
            ['tracking_code', 'complainant_phone']
        );
        foreach ($complaintsQuery->latest('id')->limit(3)->get() as $comp) {
            $subParts = [];
            if ($comp->complainant_phone) $subParts[] = 'هاتف: ' . $comp->complainant_phone;
            if ($comp->subject) $subParts[] = $comp->subject;

            $results['complaints'][] = [
                'id' => $comp->id,
                'title' => $comp->complainant_name ?: ('شكوى #' . $comp->tracking_code),
                'code' => $comp->tracking_code ?: ('CMP-' . $comp->id),
                'subtitle' => implode(' • ', $subParts),
                'url' => route('complaints.index', ['q' => $comp->tracking_code ?: $comp->complainant_name]),
                'badge' => 'شكوى / مقترح',
                'badge_class' => 'bg-secondary-subtle text-secondary',
                'icon' => 'bi-chat-dots-fill',
            ];
        }

        $totalCount = count($results['beneficiaries'])
            + count($results['case_applications'])
            + count($results['collaborations'])
            + count($results['projects'])
            + count($results['donors'])
            + count($results['employees'])
            + count($results['volunteers'])
            + count($results['donations'])
            + count($results['complaints']);

        return response()->json([
            'query' => $q,
            'total' => $totalCount,
            'categories' => [
                [
                    'key' => 'beneficiaries',
                    'title' => 'الحالات والمستفيدون',
                    'icon' => 'bi-heart-pulse-fill',
                    'color' => 'text-danger',
                    'count' => count($results['beneficiaries']),
                    'items' => $results['beneficiaries'],
                ],
                [
                    'key' => 'case_applications',
                    'title' => 'طلبات البحث المبدئي والأبحاث الميدانية',
                    'icon' => 'bi-clipboard2-pulse-fill',
                    'color' => 'text-warning',
                    'count' => count($results['case_applications']),
                    'items' => $results['case_applications'],
                ],
                [
                    'key' => 'collaborations',
                    'title' => 'التعاونات والشراكات والشركات',
                    'icon' => 'bi-diagram-3-fill',
                    'color' => 'text-success',
                    'count' => count($results['collaborations']),
                    'items' => $results['collaborations'],
                ],
                [
                    'key' => 'projects',
                    'title' => 'المشاريع والحملات ودور الضيافة',
                    'icon' => 'bi-kanban-fill',
                    'color' => 'text-primary',
                    'count' => count($results['projects']),
                    'items' => $results['projects'],
                ],
                [
                    'key' => 'donors',
                    'title' => 'المتبرعون',
                    'icon' => 'bi-hand-thumbs-up-fill',
                    'color' => 'text-success',
                    'count' => count($results['donors']),
                    'items' => $results['donors'],
                ],
                [
                    'key' => 'employees',
                    'title' => 'الموظفون وفريق العمل',
                    'icon' => 'bi-person-badge-fill',
                    'color' => 'text-primary',
                    'count' => count($results['employees']),
                    'items' => $results['employees'],
                ],
                [
                    'key' => 'volunteers',
                    'title' => 'المتطوعون',
                    'icon' => 'bi-people-fill',
                    'color' => 'text-warning',
                    'count' => count($results['volunteers']),
                    'items' => $results['volunteers'],
                ],
                [
                    'key' => 'donations',
                    'title' => 'الإيصالات والخزائن',
                    'icon' => 'bi-receipt',
                    'color' => 'text-info',
                    'count' => count($results['donations']),
                    'items' => $results['donations'],
                ],
                [
                    'key' => 'complaints',
                    'title' => 'الشكاوى والمقترحات',
                    'icon' => 'bi-chat-dots-fill',
                    'color' => 'text-secondary',
                    'count' => count($results['complaints']),
                    'items' => $results['complaints'],
                ],
            ],
            'reports_url' => route('reports.index', ['q' => $q]),
        ]);
    }
}
