<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

final class BeneficiaryFamilyMember extends Model
{
    protected $fillable = [
        'beneficiary_id', 'relationship', 'full_name', 'birth_date', 'death_date', 'age', 'code',
        'national_id', 'phone', 'backup_phone', 'sponsorship_amount', 'education_level',
        'case_details', 'is_patient', 'active', 'sort_order',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'sponsorship_amount' => 'decimal:2',
        'is_patient' => 'boolean',
        'active' => 'boolean',
    ];

    public function getAgeAttribute($value): ?int
    {
        if ($this->birth_date) {
            $end = $this->death_date ?? now();
            return (int) \Carbon\Carbon::parse($this->birth_date)->diffInYears($end);
        }
        return $value;
    }

    protected static function booted(): void
    {
        static::creating(function (BeneficiaryFamilyMember $member): void {
            $member->code = self::normalizeCode($member->code) ?: self::nextAvailableCode();
        });

        static::saving(function (BeneficiaryFamilyMember $member): void {
            if ($member->code) {
                $member->code = self::normalizeCode($member->code);
            }
        });
    }

    public static function normalizeCode(mixed $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code !== '' ? $code : null;
    }

    public static function nextAvailableCode(): string
    {
        do {
            $code = 'FM-' . strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(Donor::class, 'family_member_sponsors', 'family_member_id', 'donor_id')
            ->withPivot(['monthly_amount', 'notes'])
            ->withTimestamps();
    }

    public function assessments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChildAssessment::class, 'family_member_id')->latest('assessment_date');
    }

    public function entryAssessment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ChildAssessment::class, 'family_member_id')
            ->where('type', 'entry')
            ->latestOfMany('assessment_date');
    }

    public function latestMonthlyAssessment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ChildAssessment::class, 'family_member_id')
            ->where('type', 'monthly')
            ->latestOfMany('assessment_date');
    }

    public function getRelationshipLabelAttribute(): string
    {
        return match ($this->relationship) {
            'husband' => 'الزوج',
            'wife' => 'الزوجة',
            'child' => 'طفل / ابن',
            'patient' => 'المريض',
            default => 'فرد أسرة',
        };
    }
}
