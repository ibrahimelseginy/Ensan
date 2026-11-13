<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebVolunteerRequest extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumn(): string
    {
        return 'cv_path';
    }

    public function getImageColumns(): array
    {
        return ['cv_path', 'id_card_path'];
    }

    public function cvExists(): bool
    {
        if (!$this->cv_path) return false;
        
        $cvPath = $this->cv_path;
        // Clean up path variations
        if (str_starts_with($cvPath, 'http')) {
            $parsed = parse_url($cvPath);
            $cvPath = ltrim(str_replace('/storage/', '', $parsed['path']), '/');
        } else {
            $cvPath = ltrim($cvPath, '/');
            if (str_starts_with($cvPath, 'storage/')) {
                $cvPath = substr($cvPath, 8);
            }
        }

        $pathsToTry = [
            \Illuminate\Support\Facades\Storage::disk('public')->path($cvPath),
            \Illuminate\Support\Facades\Storage::disk('local')->path($cvPath),
            storage_path("app/public/" . $cvPath),
            storage_path("app/private/" . $cvPath),
            public_path("storage/" . $cvPath)
        ];

        foreach ($pathsToTry as $path) {
            if (file_exists($path)) {
                return true;
            }
        }
        
        return false;
    }

    protected static function booted(): void
    {
        static::creating(function (WebVolunteerRequest $model): void {
            if (empty($model->tracking_code)) {
                $model->tracking_code = 'VOL-' . date('Y') . '-' . strtoupper(substr(uniqid('', true), -6));
            }
        });
    }

    protected $casts = [
        'interview_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'previous_experience' => 'boolean',
    ];

    protected $fillable = [
        'tracking_code',
        'name',
        'phone',
        'email',
        'area_of_interest',
        'cv_path',
        'message',
        'status',
        'assigned_project_id',
        'assigned_project_name',
        'interview_date',
        'interview_location',
        'commitment_score',
        'teamwork_score',
        'responsibility_score',
        'communication_score',
        'enthusiasm_score',
        'project_fit_score',
        'interview_decision',
        'interview_conditions',
        'response_notes',
        'reviewed_at',
        'reviewed_by',
        'address',
        'current_address',
        'birth_date',
        'national_id',
        'id_card_path',
        'gender',
        'education_level',
        'faculty',
        'university',
        'current_job',
        'previous_experience',
        'skills',
        'goal',
        'expectations',
        'volunteer_hours'
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class, 'assigned_project_id');
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getEffectiveProjectNameAttribute(): ?string
    {
        return $this->project?->name ?: $this->assigned_project_name;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'accepted' => 'تم القبول بمشروع',
            'interview' => 'تحديد موعد مقابلة',
            'contacted' => 'تم التواصل',
            'rejected' => 'مرفوض',
            default => 'قيد المراجعة (جديد)',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'accepted' => 'success',
            'interview' => 'warning',
            'contacted' => 'info',
            'rejected' => 'danger',
            default => 'primary',
        };
    }
}

