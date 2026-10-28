<?php

declare(strict_types=1);
namespace App\Models;

use App\Rules\UniqueBeneficiaryName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

final class Beneficiary extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'code', 'full_name', 'guardian_name', 'patient_name', 'patient_age', 'patient_code',
        'national_id', 'birth_date', 'age', 'visa_card_number', 'phone', 'backup_phone', 'address', 'assistance_type',
        'collection_day', 'collection_method', 'status', 'project_id', 'campaign_id', 'guest_house_id',
        'notes', 'rejection_reason', 'archived_reason', 'mother_name', 'children_names', 'family_members_data',
        'children_count', 'sponsored_children_count', 'monthly_sponsorship_amount', 'brothers_count',
        'adult_children_count', 'adult_children_ages', 'study_grade', 'poultry_type', 'notes_cases',
        'meat', 'allocation_type', 'child_sponsorship_type', 'sponsorship_scope_type',
        'source_name', 'reference_number', 'deceased_husband_name', 'deceased_husband_birth_date', 'deceased_husband_death_date', 'deceased_husband_age',
        'transit_details', 'death_details', 'income_aid_sources', 'expenses_debts_details', 'zad_details',
        'need_type', 'family_members_count', 'social_status_description', 'guide_name', 'guide_phone',
        'midrar_details', 'hospital_name', 'tumor_location', 'treatment_protocol', 'tumor_details',
        'created_by', 'updated_by', 'classification'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased_husband_birth_date' => 'date',
        'deceased_husband_death_date' => 'date',
        'family_members_data' => 'array',
        'zad_details' => 'array',
        'midrar_details' => 'array',
        'tumor_details' => 'array',
        'monthly_sponsorship_amount' => 'decimal:2',
    ];

    public function getAgeAttribute($value): ?int
    {
        if ($this->birth_date) {
            return \Carbon\Carbon::parse($this->birth_date)->age;
        }
        if ($value !== null) {
            $elapsed = $this->created_at ? (int) $this->created_at->diffInYears(now()) : 0;
            return (int) $value + $elapsed;
        }
        return null;
    }

    public function getDeceasedHusbandAgeAttribute($value): ?int
    {
        if ($this->deceased_husband_birth_date) {
            $end = $this->deceased_husband_death_date ?? now();
            return (int) \Carbon\Carbon::parse($this->deceased_husband_birth_date)->diffInYears($end);
        }
        if ($value !== null) {
            $end = $this->deceased_husband_death_date ?? now();
            $elapsed = $this->created_at ? (int) $this->created_at->diffInYears($end) : 0;
            return (int) $value + $elapsed;
        }
        return null;
    }

    public function patientMember(): ?BeneficiaryFamilyMember
    {
        return $this->relationLoaded('familyMembers')
            ? $this->familyMembers->firstWhere('is_patient', true)
            : $this->familyMembers()->where('is_patient', true)->first();
    }

    public function getPatientAgeAttribute($value): ?int
    {
        $member = $this->patientMember();
        if ($member && $member->birth_date) {
            $end = $member->death_date ?? now();
            return (int) \Carbon\Carbon::parse($member->birth_date)->diffInYears($end);
        }
        if ($member && $member->age) {
            return (int) $member->age;
        }
        if ($value !== null) {
            $elapsed = $this->created_at ? (int) $this->created_at->diffInYears(now()) : 0;
            return (int) $value + $elapsed;
        }
        if ($this->birth_date && $this->patient_name && ($this->patient_name === $this->full_name || ($this->patient_relationship ?? '') === 'patient')) {
            return (int) \Carbon\Carbon::parse($this->birth_date)->age;
        }
        return null;
    }

    public function getPatientBirthDateAttribute(): ?string
    {
        $member = $this->patientMember();
        if ($member && $member->birth_date) {
            return $member->birth_date->format('Y-m-d');
        }
        if ($this->birth_date && $this->patient_name && ($this->patient_name === $this->full_name || ($this->patient_relationship ?? '') === 'patient')) {
            return $this->birth_date->format('Y-m-d');
        }
        return null;
    }

    protected static function booted(): void
    {
        static::saving(function (Beneficiary $beneficiary): void {
            // Auto-sync birth_date and age dynamically from elapsed years
            if (!empty($beneficiary->birth_date)) {
                $beneficiary->age = (int) \Carbon\Carbon::parse($beneficiary->birth_date)->age;
            } elseif (!empty($beneficiary->age) && (int) $beneficiary->age > 0) {
                $beneficiary->birth_date = \Carbon\Carbon::now()->subYears((int) $beneficiary->age)->startOfYear()->format('Y-m-d');
            }

            // Also for deceased husband if in Zad project
            if (!empty($beneficiary->deceased_husband_birth_date)) {
                $end = $beneficiary->deceased_husband_death_date ?? now();
                $beneficiary->deceased_husband_age = (int) \Carbon\Carbon::parse($beneficiary->deceased_husband_birth_date)->diffInYears($end);
            } elseif (!empty($beneficiary->deceased_husband_age) && (int) $beneficiary->deceased_husband_age > 0 && empty($beneficiary->deceased_husband_birth_date)) {
                $base = $beneficiary->deceased_husband_death_date ?? now();
                $beneficiary->deceased_husband_birth_date = \Carbon\Carbon::parse($base)->subYears((int) $beneficiary->deceased_husband_age)->startOfYear()->format('Y-m-d');
            }

            // Auto-sync patient age if patient is beneficiary himself
            if (empty($beneficiary->patient_age) && !empty($beneficiary->patient_name) && !empty($beneficiary->birth_date) && ($beneficiary->patient_name === $beneficiary->full_name || ($beneficiary->patient_relationship ?? '') === 'patient')) {
                $beneficiary->patient_age = (int) \Carbon\Carbon::parse($beneficiary->birth_date)->age;
            }

            if ($beneficiary->exists && ! $beneficiary->isDirty('full_name')) {
                return;
            }

            $normalizedName = UniqueBeneficiaryName::normalize((string) $beneficiary->full_name);
            $duplicateId = UniqueBeneficiaryName::existingBeneficiaryId(
                $normalizedName,
                $beneficiary->exists ? (int) $beneficiary->id : null,
            );

            if ($duplicateId !== null) {
                throw ValidationException::withMessages([
                    'full_name' => 'لا يمكن حفظ المستفيد: يوجد مستفيد آخر بنفس الاسم أو باسم مطابق بعد إزالة اختلافات الكتابة.',
                ]);
            }
        });
        static::creating(function (Beneficiary $beneficiary): void {
            if (auth()->check() && empty($beneficiary->created_by)) {
                $beneficiary->created_by = auth()->id();
            }
            if (empty($beneficiary->classification)) {
                $beneficiary->classification = 'A';
            }
            if (!empty($beneficiary->guest_house_id) && (empty($beneficiary->code) || !str_starts_with($beneficiary->code, 'G-'))) {
                $beneficiary->code = self::nextGuestCode();
            }
        });

        static::updating(function (Beneficiary $beneficiary): void {
            if (empty($beneficiary->updated_by) && auth()->check()) {
                $beneficiary->updated_by = auth()->id();
            }
            if (!empty($beneficiary->guest_house_id) && (empty($beneficiary->code) || !str_starts_with($beneficiary->code, 'G-'))) {
                $beneficiary->code = self::nextGuestCode();
            }
        });
    }

    public function changeRequests(): MorphMany
    {
        return $this->morphMany(ChangeRequest::class, 'subject', 'model_type', 'model_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getActualCreatorAttribute(): ?User
    {
        $creationRequest = $this->relationLoaded('changeRequests')
            ? $this->changeRequests->firstWhere('action', 'create')
            : ChangeRequest::where('model_type', self::class)->where('model_id', $this->id)->where('action', 'create')->first();

        return $creationRequest?->user ?? $this->creator;
    }

    public function getActualApproverAttribute(): ?User
    {
        $creationRequest = $this->relationLoaded('changeRequests')
            ? $this->changeRequests->firstWhere('action', 'create')
            : ChangeRequest::where('model_type', self::class)->where('model_id', $this->id)->where('action', 'create')->first();

        return $creationRequest?->reviewer;
    }

    public function getActualUpdaterAttribute(): ?User
    {
        $updateRequest = $this->relationLoaded('changeRequests')
            ? $this->changeRequests->where('action', 'update')->where('status', 'approved')->first()
            : ChangeRequest::where('model_type', self::class)->where('model_id', $this->id)->where('action', 'update')->where('status', 'approved')->latest('updated_at')->first();

        return $updateRequest?->user ?? $this->updater;
    }

    public function getActualUpdateApproverAttribute(): ?User
    {
        $updateRequest = $this->relationLoaded('changeRequests')
            ? $this->changeRequests->where('action', 'update')->where('status', 'approved')->first()
            : ChangeRequest::where('model_type', self::class)->where('model_id', $this->id)->where('action', 'update')->where('status', 'approved')->latest('updated_at')->first();

        return $updateRequest?->reviewer;
    }

    public function caseApplications(): HasMany
    {
        return $this->hasMany(MobileCaseApplication::class);
    }

    public function childAssessments(): HasMany
    {
        return $this->hasMany(ChildAssessment::class)->latest('assessment_date');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(GuestHousePatientProfile::class);
    }

    public function guestHouseStays(): HasMany
    {
        return $this->hasMany(GuestHouseStay::class);
    }

    public function allocatedBeneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'beneficiary_allocations',
            'beneficiary_id',
            'allocated_beneficiary_id'
        )->withTimestamps();
    }

    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(Donor::class, 'beneficiary_sponsors')->withTimestamps();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'beneficiary_id')
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('received_at');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(BeneficiaryFamilyMember::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class , 'attachable', 'entity_type', 'entity_id');
    }
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public static function nextGuestCode(): string
    {
        $codes = self::query()
            ->where('code', 'like', 'G-%')
            ->pluck('code');

        $maxNum = 0;
        foreach ($codes as $code) {
            if (preg_match('/G-(\d+)/', (string) $code, $m)) {
                $maxNum = max($maxNum, (int) $m[1]);
            }
        }

        return sprintf('G-%05d', $maxNum + 1);
    }

    public function getGuestCodeAttribute(): string
    {
        if (!empty($this->code) && str_starts_with($this->code, 'G-')) {
            return $this->code;
        }
        return sprintf('G-%05d', $this->id);
    }
}
