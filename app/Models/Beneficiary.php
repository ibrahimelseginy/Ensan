<?php

declare(strict_types=1);
namespace App\Models;

use App\Rules\UniqueBeneficiaryName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

final class Beneficiary extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'code', 'full_name', 'guardian_name', 'patient_name', 'patient_age', 'patient_code',
        'national_id', 'visa_card_number', 'phone', 'backup_phone', 'address', 'assistance_type',
        'collection_day', 'collection_method', 'status', 'project_id', 'campaign_id', 'guest_house_id',
        'notes', 'rejection_reason', 'archived_reason', 'mother_name', 'children_names', 'family_members_data',
        'children_count', 'sponsored_children_count', 'monthly_sponsorship_amount', 'brothers_count',
        'adult_children_count', 'adult_children_ages', 'study_grade', 'poultry_type', 'notes_cases',
        'meat', 'allocation_type', 'child_sponsorship_type', 'sponsorship_scope_type'
    ];

    protected $casts = [
        'family_members_data' => 'array',
        'monthly_sponsorship_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (Beneficiary $beneficiary): void {
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
}
