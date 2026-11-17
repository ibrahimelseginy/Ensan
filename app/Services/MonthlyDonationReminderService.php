<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Donor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MonthlyDonationReminderService
{
    /**
     * Monthly donors whose due date has arrived and who do not have a
     * completed cash donation recorded in the selected month.
     */
    /**
     * Monthly donors whose due date has arrived or is due within 24 hours (tomorrow)
     * and who have not yet paid for this cycle.
     */
    public function dueDonors(?Carbon $date = null): Collection
    {
        $date ??= now();
        $tomorrowEnd = $date->copy()->addDay()->endOfDay();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        return Donor::query()
            ->where('active', true)
            ->where('classification', 'recurring')
            ->where('recurring_cycle', 'monthly')
            ->where(function ($q) {
                $q->whereNotNull('next_due_date')
                  ->orWhereNotNull('monthly_donation_day');
            })
            ->with(['sponsoredBeneficiary', 'sponsoredBeneficiaries', 'sponsoredFamilyMembers.beneficiary'])
            ->orderBy('monthly_donation_day')
            ->orderBy('name')
            ->get()
            ->filter(function (Donor $donor) use ($date, $tomorrowEnd, $monthStart, $monthEnd): bool {
                $dueDate = $this->dueDate($donor, $date);

                // If next_due_date is set and in the future beyond tomorrow, not due yet
                if ($donor->next_due_date && $dueDate->gt($tomorrowEnd)) {
                    return false;
                }

                // If next_due_date is not set, check if paid this month
                if (!$donor->next_due_date) {
                    $hasPaid = $donor->donations()
                        ->where('type', 'cash')
                        ->where('status', '<>', 'cancelled')
                        ->whereBetween('received_at', [$monthStart, $monthEnd])
                        ->exists();

                    if ($hasPaid) {
                        return false;
                    }
                }

                // Due if due date is on or before tomorrow
                return $dueDate->lte($tomorrowEnd);
            })
            ->values();
    }

    public function notificationItems(?Carbon $date = null): array
    {
        $date ??= now();
        $todayStart = $date->copy()->startOfDay();
        $tomorrowStart = $date->copy()->addDay()->startOfDay();

        return $this->dueDonors($date)
            ->map(function (Donor $donor) use ($date, $todayStart, $tomorrowStart): array {
                $dueDate = $this->dueDate($donor, $date);
                $isTomorrow = $dueDate->isSameDay($tomorrowStart);
                $isToday = $dueDate->isSameDay($todayStart);
                $isOverdue = $dueDate->lt($todayStart);

                $target = $this->targetDescription($donor);
                $caseSum = (float) $donor->active_sponsored_cases_sum;
                $effectiveAmount = $caseSum > 0 ? $caseSum : (float) $donor->sponsorship_monthly_amount;
                $amount = $effectiveAmount > 0
                    ? ' — ' . number_format($effectiveAmount, 2) . ' جنيه'
                    : '';

                $donorCode = $donor->display_code ?? ($donor->code ?? ('D-' . $donor->id));

                if ($isTomorrow) {
                    $type = 'info';
                    $statusText = 'تنبيه للاستقبال (خلال 24 ساعة): موعد تحصيل كفالة غداً من';
                } elseif ($isToday) {
                    $type = 'warning';
                    $statusText = 'مستحق اليوم: حان موعد تحصيل كفالة من';
                } else {
                    $type = 'danger';
                    $statusText = 'متأخر: كفالة متأخرة تستوجب التحصيل من';
                }

                $dueMonthKey = $dueDate->format('Y-m');

                return [
                    'id' => 'monthly_don_' . $donor->id . '_' . $dueMonthKey,
                    'donor_id' => $donor->id,
                    'category' => 'monthly_donations',
                    'type' => $type,
                    'text' => sprintf(
                        '%s %s (%s) — الاستحقاق %s%s — %s',
                        $statusText,
                        $donor->name,
                        $donorCode,
                        $dueDate->format('Y-m-d'),
                        $amount,
                        $target,
                    ),
                    'link' => route('donations.create', ['donor_id' => $donor->id]),
                    'timestamp' => $dueDate->timestamp,
                    'is_today' => $isToday,
                    'is_tomorrow' => $isTomorrow,
                    'is_overdue' => $isOverdue,
                ];
            })
            ->all();
    }

    private function dueDate(Donor $donor, Carbon $date): Carbon
    {
        if ($donor->next_due_date) {
            return Carbon::parse($donor->next_due_date)->startOfDay();
        }

        $day = min(max((int) ($donor->monthly_donation_day ?: 1), 1), $date->daysInMonth);

        return $date->copy()->startOfMonth()->day($day)->startOfDay();
    }

    private function targetDescription(Donor $donor): string
    {
        if ($donor->sponsoredFamilyMembers->isNotEmpty()) {
            if ($donor->sponsoredFamilyMembers->count() === 1) {
                $fm = $donor->sponsoredFamilyMembers->first();
                return 'كفالة الطفل: ' . $fm->full_name . ' (' . ($fm->code ?? 'BEN-' . $fm->beneficiary_id) . ')';
            }
            return 'كفالة ' . $donor->sponsoredFamilyMembers->count() . ' أطفال/أفراد أسر';
        }

        $fmParentIds = $donor->sponsoredFamilyMembers->pluck('beneficiary_id')->filter()->unique()->all();
        $directBens = $donor->sponsoredBeneficiaries->filter(function ($sb) use ($fmParentIds) {
            $pivotAmt = (float) ($sb->pivot?->monthly_amount ?? 0);
            return !in_array($sb->id, $fmParentIds, true) || $pivotAmt > 0;
        });

        if ($directBens->isNotEmpty()) {
            if ($directBens->count() === 1) {
                $ben = $directBens->first();
                return 'كفالة الأسرة: ' . $ben->full_name . ' (' . ($ben->code ?? 'BEN-' . $ben->id) . ')';
            }
            return 'كفالة ' . $directBens->count() . ' أسر/حالات';
        }

        if ($donor->sponsoredBeneficiary && !in_array($donor->sponsoredBeneficiary->id, $fmParentIds, true)) {
            return 'كفالة الحالة: ' . $donor->sponsoredBeneficiary->full_name . ' (' . ($donor->sponsoredBeneficiary->code ?? 'BEN-' . $donor->sponsoredBeneficiary->id) . ')';
        }

        return trim((string) $donor->monthly_allocation_target) !== ''
            ? (string) $donor->monthly_allocation_target
            : 'كفالة شهرية منتظمة';
    }
}
