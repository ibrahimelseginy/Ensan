<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ChildAssessment extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'family_member_id',
        'beneficiary_id',
        'type', // 'entry' or 'monthly'
        'assessment_date',
        'reviewer_name',
        'reviewer_role', // 'employee' or 'volunteer'
        'score_percentage',
        'test_image_path',
        'is_locked',
        'academic_rating',
        'psychological_rating',
        'social_rating',
        'health_rating',
        'data',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'score_percentage' => 'decimal:2',
        'is_locked' => 'boolean',
        'data' => 'array',
    ];

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryFamilyMember::class, 'family_member_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTestImageUrlAttribute(): ?string
    {
        if (!$this->test_image_path) {
            return null;
        }
        return app(\App\Services\ImageUploadService::class)->url($this->test_image_path);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'entry' => 'تقييم دخول شامل',
            default => 'تقييم دوري / شهري',
        };
    }

    public function getReviewerRoleLabelAttribute(): string
    {
        return match ($this->reviewer_role) {
            'volunteer' => 'متطوع',
            default => 'موظف',
        };
    }

    public function uploadImage($file, string $directory = 'children/assessments'): void
    {
        $path = app(\App\Services\ImageUploadService::class)->upload($file, $directory);
        $this->update(['test_image_path' => $path]);
    }
}
