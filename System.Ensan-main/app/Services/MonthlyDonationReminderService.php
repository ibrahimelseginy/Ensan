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
    public function dueDonors(?Carbon $date = null): Collection
    {
        $date ??= now();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        return Donor::query()
            ->where('active', true)
            ->where('classification', 'recurring')
            ->where('recurring_cycle', 'monthly')
            ->whereNotNull('monthly_donation_day')
            ->whereDoesntHave('donations', function ($query) use ($monthStart, $monthEnd): void {
                $query->where('type', 'cash')
                    ->where('status', '<>', 'cancelled')
                    ->whereBetween('received_at', [$monthStart, $monthEnd]);
            })
            ->withCount('sponsoredBeneficiaries')
            ->orderBy('monthly_donation_day')
            ->orderBy('name')
            ->get()
            ->filter(fn (Donor $donor) => $this->dueDate($donor, $date)->lte($date))
            ->values();
    }

    public function notificationItems(?Carbon $date = null): array
    {
        $date ??= now();

        return $this->dueDonors($date)
            ->map(function (Donor $donor) use ($date): array {
                $dueDate = $this->dueDate($donor, $date);
                $isOverdue = $dueDate->lt($date->copy()->startOfDay());
                $target = $this->targetDescription($donor);
                $amount = (float) $donor->sponsorship_monthly_amount > 0
                    ? ' — ' . number_format((float) $donor->sponsorship_monthly_amount, 2) . ' جنيه'
                    : '';

                return [
                    'category' => 'monthly_donations',
                    'type' => $isOverdue ? 'danger' : 'warning',
                    'text' => sprintf(
                        '%s تبرع شهري: %s (%s) — الاستحقاق %s%s — %s',
                        $isOverdue ? 'متأخر' : 'مستحق اليوم',
                        $donor->name,
                        $donor->code ?? ('DON-' . $donor->id),
                        $dueDate->format('Y-m-d'),
                        $amount,
                        $target,
                    ),
                    'link' => route('donations.create', ['donor_id' => $donor->id]),
                ];
            })
            ->all();
    }

    private function dueDate(Donor $donor, Carbon $date): Carbon
    {
        $day = min(max((int) $donor->monthly_donation_day, 1), $date->daysInMonth);

        return $date->copy()->startOfMonth()->day($day)->startOfDay();
    }

    private function targetDescription(Donor $donor): string
    {
        if ($donor->sponsored_beneficiaries_count > 0) {
            return 'كفالة ' . $donor->sponsored_beneficiaries_count . ' طفل/حالة';
        }

        return trim((string) $donor->monthly_allocation_target) !== ''
            ? (string) $donor->monthly_allocation_target
            : 'بدون توجيه محدد';
    }
}
