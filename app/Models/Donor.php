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
        'monthly_donation_day', 'active',
        'sponsorship_type', 'sponsored_beneficiary_id', 'sponsorship_project_id', 'sponsorship_monthly_amount',
        'allocation_type', 'monthly_allocation_target', 'campaign_id', 'guest_house_id'
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
}
