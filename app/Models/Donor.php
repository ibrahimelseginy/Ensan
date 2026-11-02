<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Donor extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'code', 'name', 'type', 'phone', 'national_id', 'email', 'address', 'classification', 'recurring_cycle',
        'monthly_donation_day', 'next_due_date', 'active',
        'sponsorship_type', 'sponsored_beneficiary_id', 'sponsorship_project_id', 'sponsorship_monthly_amount',
        'allocation_type', 'monthly_allocation_target', 'campaign_id', 'guest_house_id'
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'active' => 'boolean',
        'sponsorship_monthly_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Donor $donor): void {
            $donor->code = self::normalizeCode($donor->code) ?: self::nextAvailableCode();
            if ($donor->code && !str_starts_with($donor->code, 'D-')) {
                $donor->code = self::nextAvailableCode();
            }
        });

        static::updating(function (Donor $donor): void {
            $originalCode = self::normalizeCode($donor->getOriginal('code'));

            // كود المتبرع هو هوية ثابتة: يسمح بتعيينه مرة واحدة فقط.
            if ($originalCode !== null && $donor->isDirty('code')) {
                $donor->code = $originalCode;
            } elseif ($originalCode === null) {
                $donor->code = self::normalizeCode($donor->code) ?: self::nextAvailableCode();
            }
            if ($donor->code && !str_starts_with($donor->code, 'D-')) {
                $donor->code = self::nextAvailableCode();
            }
        });
    }

    public static function normalizeCode(mixed $code): ?string
    {
        $normalized = strtoupper(trim((string) $code));

        return $normalized !== '' ? $normalized : null;
    }

    public function getDisplayCodeAttribute(): string
    {
        if (!empty($this->code) && str_starts_with($this->code, 'D-')) {
            return $this->code;
        }
        return sprintf('D-%05d', $this->id);
    }

    public static function nextAvailableCode(): string
    {
        $codes = self::query()
            ->where('code', 'like', 'D-%')
            ->pluck('code');

        $maxNum = 0;
        foreach ($codes as $c) {
            if (preg_match('/D-(\d+)/', (string) $c, $m)) {
                $maxNum = max($maxNum, (int) $m[1]);
            }
        }

        $pendingCodes = ChangeRequest::query()
            ->where('model_type', self::class)
            ->where('action', 'create')
            ->where('status', 'pending')
            ->pluck('payload')
            ->map(function ($payload): ?string {
                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }
                if (($payload['__is_wrapped'] ?? false) === true) {
                    $payload = $payload['data'] ?? [];
                }

                return self::normalizeCode($payload['code'] ?? null);
            })
            ->filter()
            ->all();

        $nextNum = $maxNum + 1;
        do {
            $code = sprintf('D-%05d', $nextNum);
            $nextNum++;
        } while (self::where('code', $code)->exists() || in_array($code, $pendingCodes, true));

        return $code;
    }

    public function sponsoredBeneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'beneficiary_sponsors')->withTimestamps();
    }

    public function sponsoredBeneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'sponsored_beneficiary_id');
    }

    public function sponsoredFamilyMembers()
    {
        return $this->belongsToMany(
            BeneficiaryFamilyMember::class,
            'family_member_sponsors',
            'donor_id',
            'family_member_id'
        )->withPivot(['monthly_amount', 'notes'])->withTimestamps();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function webDonations(): HasMany
    {
        return $this->hasMany(WebDonation::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function getSponsorshipProfile(): array
    {
        $this->loadMissing([
            'sponsoredBeneficiaries',
            'sponsoredFamilyMembers.beneficiary',
            'campaign',
            'guestHouse',
        ]);

        $lastDonation = $this->donations()
            ->where('status', '!=', 'cancelled')
            ->latest('received_at')
            ->latest('id')
            ->first();

        // Monthly amount
        $monthlyAmount = (float) $this->sponsorship_monthly_amount;
        if ($monthlyAmount <= 0 && $lastDonation && (float)$lastDonation->amount > 0) {
            $monthlyAmount = (float) $lastDonation->amount;
        }

        // Cases / Beneficiaries
        $cases = [];
        $addedBeneficiaryIds = [];
        foreach ($this->sponsoredBeneficiaries as $sb) {
            $caseAmt = null;
            if ($monthlyAmount > 0 && $this->sponsoredBeneficiaries->count() === 1) {
                $caseAmt = $monthlyAmount;
            } elseif ((float)($sb->monthly_sponsorship_amount ?? 0) > 0) {
                $caseAmt = (float)$sb->monthly_sponsorship_amount;
            }
            $cases[] = [
                'id' => $sb->id,
                'code' => $sb->code ?: ('BEN-' . $sb->id),
                'name' => $sb->full_name,
                'phone' => $sb->phone ?: '',
                'type' => 'beneficiary',
                'amount' => $caseAmt,
                'notes' => 'كفالة حالة',
            ];
            $addedBeneficiaryIds[] = $sb->id;
        }

        foreach ($this->sponsoredFamilyMembers as $fm) {
            $fmAmount = (float)($fm->pivot->monthly_amount ?? 0);
            if ($fmAmount <= 0 && $monthlyAmount > 0 && $this->sponsoredFamilyMembers->count() === 1 && empty($cases)) {
                $fmAmount = $monthlyAmount;
            } elseif ($fmAmount <= 0 && (float)($fm->sponsorship_amount ?? 0) > 0) {
                $fmAmount = (float)$fm->sponsorship_amount;
            }

            if (in_array($fm->beneficiary_id, $addedBeneficiaryIds, true)) {
                foreach ($cases as &$existingCase) {
                    if ($existingCase['id'] == $fm->beneficiary_id) {
                        $existingCase['name'] = $fm->full_name . ' (' . ($fm->relationship_label ?? 'فرد أسرة') . ')';
                        if ($fmAmount > 0 && empty($existingCase['amount'])) {
                            $existingCase['amount'] = $fmAmount;
                        }
                    }
                }
                unset($existingCase);
            } else {
                $cases[] = [
                    'id' => $fm->beneficiary_id,
                    'family_member_id' => $fm->id,
                    'code' => $fm->code ?: ($fm->beneficiary?->code ?: 'BEN-' . $fm->beneficiary_id),
                    'name' => $fm->full_name . ' (' . ($fm->relationship_label ?? 'فرد أسرة') . ' - أسرة ' . ($fm->beneficiary?->full_name ?? '') . ')',
                    'phone' => $fm->phone ?: ($fm->beneficiary?->phone ?: ''),
                    'type' => 'family_member',
                    'amount' => $fmAmount > 0 ? $fmAmount : null,
                    'notes' => 'كفالة فرد: ' . $fm->full_name,
                ];
                $addedBeneficiaryIds[] = $fm->beneficiary_id;
            }
        }

        // If no cases directly bound on donor but last donation had beneficiary allocations, extract them
        if (empty($cases) && $lastDonation && !empty($lastDonation->beneficiary_allocations) && is_array($lastDonation->beneficiary_allocations)) {
            foreach ($lastDonation->beneficiary_allocations as $ba) {
                if (!empty($ba['beneficiary_id'])) {
                    $cases[] = [
                        'id' => (int)$ba['beneficiary_id'],
                        'code' => $ba['code'] ?? ('BEN-' . $ba['beneficiary_id']),
                        'name' => $ba['name'] ?? ('مستفيد #' . $ba['beneficiary_id']),
                        'phone' => $ba['phone'] ?? '',
                        'type' => 'beneficiary',
                        'amount' => !empty($ba['amount']) ? (float)$ba['amount'] : null,
                        'notes' => $ba['notes'] ?? 'كفالة حالة سابقة',
                    ];
                }
            }
        }

        // Suggested purpose
        $suggestedPurpose = null;
        if ($lastDonation?->purpose) {
            $suggestedPurpose = $lastDonation->purpose;
        } elseif ($this->sponsoredFamilyMembers->contains('is_patient', true) || str_contains((string)$this->monthly_allocation_target, 'ورم') || str_contains((string)$this->monthly_allocation_target, 'علاج')) {
            $suggestedPurpose = 'kafalat_awram';
        } elseif ($this->sponsoredBeneficiaries->isNotEmpty() || $this->sponsoredFamilyMembers->isNotEmpty()) {
            $suggestedPurpose = 'kafalat_aytam';
        } elseif ($this->allocation_type === 'sadaqa_jariya') {
            $suggestedPurpose = 'sadaqa_jariya';
        } elseif ($this->allocation_type === 'project' || $this->sponsorship_project_id) {
            $suggestedPurpose = 'sadaqat';
        } elseif ($this->allocation_type === 'guest_house' || $this->guest_house_id) {
            $suggestedPurpose = 'sadaqat';
        }

        // Last paid month and next suggested month
        $lastPaidMonth = null;
        if ($lastDonation && !empty($lastDonation->paid_months) && is_array($lastDonation->paid_months)) {
            $sortedMonths = $lastDonation->paid_months;
            sort($sortedMonths);
            $lastPaidMonth = end($sortedMonths);
        }

        $suggestedMonth = null;
        if ($this->next_due_date) {
            $suggestedMonth = $this->next_due_date->format('Y-m');
        } elseif ($lastPaidMonth) {
            try {
                $suggestedMonth = \Carbon\Carbon::createFromFormat('Y-m', $lastPaidMonth)->addMonth()->format('Y-m');
            } catch (\Throwable $e) {
                $suggestedMonth = now()->format('Y-m');
            }
        } else {
            $suggestedMonth = now()->format('Y-m');
        }

        $isRecurring = ($this->classification === 'recurring')
            || ($monthlyAmount > 0)
            || (!empty($cases))
            || ($this->allocation_type === 'sponsorship')
            || (!empty($this->monthly_allocation_target));

        $targetLabel = null;
        if (!empty($cases)) {
            $targetLabel = 'كفالة ' . count($cases) . ' حالة: ' . implode('، ', array_map(fn($c) => $c['name'], array_slice($cases, 0, 2)));
            if (count($cases) > 2) {
                $targetLabel .= ' وغيرهم';
            }
        } elseif ($this->allocation_type === 'guest_house' || $this->guest_house_id) {
            $ghName = $this->guestHouse?->name ?: ($lastDonation?->guestHouse?->name ?? 'دار الضيافة');
            $targetLabel = 'دار الضيافة: ' . $ghName;
        } elseif ($this->allocation_type === 'campaign' || $this->campaign_id) {
            $cName = $this->campaign?->name ?: ($lastDonation?->campaign?->name ?? 'حملة');
            $targetLabel = 'حملة: ' . $cName;
        } elseif ($this->allocation_type === 'project' || $this->sponsorship_project_id) {
            $pName = $this->sponsorship_project_id ? \App\Models\Project::find($this->sponsorship_project_id)?->name : ($lastDonation?->project?->name ?? 'مشروع');
            $targetLabel = 'مشروع: ' . $pName;
        } elseif (!empty($this->monthly_allocation_target)) {
            $targetLabel = $this->monthly_allocation_target;
        }

        return [
            'donor_id' => $this->id,
            'name' => $this->name,
            'code' => $this->display_code,
            'phone' => $this->phone ?: '',
            'classification' => $this->classification ?: 'one_time',
            'is_recurring' => (bool)$isRecurring,
            'recurring_cycle' => $this->recurring_cycle ?: 'monthly',
            'monthly_donation_day' => (int) ($this->monthly_donation_day ?: 1),
            'next_due_date' => $this->next_due_date ? $this->next_due_date->format('Y-m-d') : null,
            'monthly_amount' => $monthlyAmount > 0 ? $monthlyAmount : null,
            'allocation_type' => $this->allocation_type,
            'target_label' => $targetLabel,
            'project_id' => $this->sponsorship_project_id ?: ($lastDonation?->project_id),
            'campaign_id' => $this->campaign_id ?: ($lastDonation?->campaign_id),
            'guest_house_id' => $this->guest_house_id ?: ($lastDonation?->guest_house_id),
            'suggested_purpose' => $suggestedPurpose,
            'suggested_month' => $suggestedMonth,
            'last_paid_month' => $lastPaidMonth,
            'last_channel' => $lastDonation?->cash_channel ?: 'cash',
            'sponsored_cases' => $cases,
        ];
    }
}
