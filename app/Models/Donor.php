<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Donor extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'code', 'name', 'type', 'phone', 'email', 'address', 'classification', 'recurring_cycle',
        'monthly_donation_day', 'active',
        'sponsorship_type', 'sponsored_beneficiary_id', 'sponsorship_project_id', 'sponsorship_monthly_amount',
        'allocation_type', 'monthly_allocation_target', 'campaign_id', 'guest_house_id'
    ];

    protected static function booted(): void
    {
        static::creating(function (Donor $donor): void {
            $donor->code = self::normalizeCode($donor->code) ?: self::nextAvailableCode();
        });

        static::updating(function (Donor $donor): void {
            $originalCode = self::normalizeCode($donor->getOriginal('code'));

            // كود المتبرع هو هوية ثابتة: يسمح بتعيينه مرة واحدة فقط.
            if ($originalCode !== null && $donor->isDirty('code')) {
                $donor->code = $originalCode;
            } elseif ($originalCode === null) {
                $donor->code = self::normalizeCode($donor->code) ?: self::nextAvailableCode();
            }
        });
    }

    public static function normalizeCode(mixed $code): ?string
    {
        $normalized = strtoupper(trim((string) $code));

        return $normalized !== '' ? $normalized : null;
    }

    public static function nextAvailableCode(): string
    {
        $nextId = ((int) self::max('id')) + 1;
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

        do {
            $code = 'DON-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
            $nextId++;
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
