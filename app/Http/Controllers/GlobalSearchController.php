<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Donation;
use App\Models\Donor;
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

        $results = [
            'beneficiaries' => [],
            'donors' => [],
            'employees' => [],
            'volunteers' => [],
            'donations' => [],
        ];

        // 1. Beneficiaries / الحالات والمستفيدون
        $cleanQ = str_replace(
            ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            $q
        );

        $beneficiariesQuery = Beneficiary::query();
        if (preg_match('/^\d{2}$/', $cleanQ)) {
            $beneficiariesQuery->where(function ($w) use ($cleanQ, $q) {
                $w->where('code', 'like', "{$cleanQ}%")
                  ->orWhere('code', 'like', "G-{$cleanQ}%")
                  ->orWhere('patient_code', 'like', "{$cleanQ}%")
                  ->orWhere('full_name', 'like', "%{$q}%");
            });
        } elseif (preg_match('/^(?:19|20)(\d{2})$/', $cleanQ, $matches)) {
            $yr = $matches[1];
            $beneficiariesQuery->where(function ($w) use ($yr, $cleanQ, $q) {
                $w->where('code', 'like', "{$yr}%")
                  ->orWhere('code', 'like', "G-{$yr}%")
                  ->orWhere('code', 'like', "%{$cleanQ}%")
                  ->orWhere('patient_code', 'like', "{$yr}%")
                  ->orWhere('full_name', 'like', "%{$q}%");
            });
        } else {
            $beneficiariesQuery->where(function ($w) use ($q, $cleanQ) {
                $w->where('full_name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$cleanQ}%")
                  ->orWhere('phone', 'like', "%{$cleanQ}%")
                  ->orWhere('backup_phone', 'like', "%{$cleanQ}%")
                  ->orWhere('national_id', 'like', "%{$cleanQ}%")
                  ->orWhere('patient_name', 'like', "%{$q}%")
                  ->orWhere('patient_code', 'like', "%{$cleanQ}%")
                  ->orWhere('guardian_name', 'like', "%{$q}%");
            });
        }

        $beneficiaries = $beneficiariesQuery
            ->select(['id', 'code', 'full_name', 'phone', 'national_id', 'assistance_type', 'status'])
            ->limit(5)
            ->get();

        foreach ($beneficiaries as $b) {
            $codeStr = $b->code ?: ('BEN-' . $b->id);
            $subParts = [];
            if ($b->phone) $subParts[] = 'هاتف: ' . $b->phone;
            if ($b->national_id) $subParts[] = 'رقم قومي: ' . $b->national_id;
            if ($b->assistance_type) $subParts[] = $b->assistance_type;

            $results['beneficiaries'][] = [
                'id' => $b->id,
                'title' => $b->full_name,
                'code' => $codeStr,
                'subtitle' => implode(' • ', $subParts),
                'url' => route('beneficiaries.show', $b),
                'badge' => 'حالة / مستفيد',
                'badge_class' => 'bg-danger-subtle text-danger',
                'icon' => 'bi-heart-pulse-fill',
            ];
        }

        // 2. Donors / المتبرعون
        $donors = Donor::query()
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('national_id', 'like', "%{$q}%");
            })
            ->select(['id', 'code', 'name', 'phone', 'classification', 'sponsorship_monthly_amount'])
            ->limit(5)
            ->get();

        foreach ($donors as $d) {
            $codeStr = $d->code ?: ('D-' . str_pad((string)$d->id, 5, '0', STR_PAD_LEFT));
            $subParts = [];
            if ($d->phone) $subParts[] = 'هاتف: ' . $d->phone;
            if ($d->classification === 'recurring') {
                $subParts[] = 'متبرع شهري' . ($d->sponsorship_monthly_amount > 0 ? ' (' . number_format((float)$d->sponsorship_monthly_amount) . ' ج.م)' : '');
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

        // 3. Staff / Employees (الموظفون وفريق العمل)
        $employees = User::query()
            ->where(function ($w) {
                $w->where('is_volunteer', false)->orWhereNull('is_volunteer');
            })
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('job_title', 'like', "%{$q}%")
                  ->orWhere('department', 'like', "%{$q}%");
            })
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

        // 4. Volunteers (المتطوعون)
        $volunteers = User::query()
            ->where('is_volunteer', true)
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%");
            })
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

        // 5. Donations & Receipts (إيصالات وتبرعات)
        $donations = Donation::query()
            ->with(['donor:id,name'])
            ->where(function ($w) use ($q) {
                $w->where('receipt_number', 'like', "%{$q}%")
                  ->orWhere('operation_number', 'like', "%{$q}%");
            })
            ->select(['id', 'receipt_number', 'donor_id', 'amount', 'type', 'created_at', 'status'])
            ->limit(4)
            ->get();

        foreach ($donations as $dn) {
            $results['donations'][] = [
                'id' => $dn->id,
                'title' => 'إيصال #' . ($dn->receipt_number ?: $dn->id),
                'code' => 'REC-' . ($dn->receipt_number ?: $dn->id),
                'subtitle' => 'المبلغ: ' . number_format((float)$dn->amount, 2) . ' ج.م • المتبرع: ' . ($dn->donor?->name ?? 'فاعل خير'),
                'url' => route('donations.show', $dn),
                'badge' => 'إيصال تبرع',
                'badge_class' => 'bg-info-subtle text-info',
                'icon' => 'bi-receipt',
            ];
        }

        $totalCount = count($results['beneficiaries']) + count($results['donors']) + count($results['employees']) + count($results['volunteers']) + count($results['donations']);

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
                    'title' => 'إيصالات التبرع',
                    'icon' => 'bi-receipt',
                    'color' => 'text-info',
                    'count' => count($results['donations']),
                    'items' => $results['donations'],
                ],
            ],
            'reports_url' => route('reports.index', ['q' => $q]),
        ]);
    }
}
