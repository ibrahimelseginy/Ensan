<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class FieldVisit extends Model
{
    use \App\Traits\HashedRouteKey;
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'researcher_id',
        'visit_date',
        'status',
        'report',
        'recommendation',
        // Research Fields
        'patient_name',
        'guardian_name',
        'national_id',
        'phone',
        'researcher_phone',
        'home_ownership',
        'house_photo_path',
        'address',
        'researcher_opinion',
        'family_members',
        // Income
        'income_job',
        'income_daily_work',
        'income_pension',
        'income_social_affairs',
        'income_charities',
        'total_income',
        // Expenses
        'expense_rent',
        'expense_tuition',
        'expense_food_weekly',
        'expense_transportation',
        'expense_utilities',
        'total_expenses',
        // Debts
        'total_debts',
        'debt_repaid',
        'debt_remaining',
        'debt_monthly_installment',
        'debt_reason',
        // Case Needs & Assessment
        'case_guide',
        'case_needs',
        'home_and_social_condition',
        'condition_photos',
        // Oncology conditional fields
        'has_cancer_patient',
        'hospital_name',
        'tumor_location',
        'disease_discovery_date',
        'disease_duration',
        'chemo_sessions_taken',
        'chemo_sessions_remaining',
        'radiation_sessions_taken',
        'radiation_sessions_remaining',
        'hospital_visit_frequency',
        'patient_psychological_state',
        'before_tumor_notes',
        'after_tumor_notes',
        'treatment_protocol',
        'foundation_manager',
        'project_manager',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'has_cancer_patient' => 'boolean',
        'family_members' => 'array',
        'condition_photos' => 'array',
        'income_job' => 'decimal:2',
        'income_daily_work' => 'decimal:2',
        'income_pension' => 'decimal:2',
        'income_social_affairs' => 'decimal:2',
        'income_charities' => 'decimal:2',
        'total_income' => 'decimal:2',
        'expense_rent' => 'decimal:2',
        'expense_tuition' => 'decimal:2',
        'expense_food_weekly' => 'decimal:2',
        'expense_transportation' => 'decimal:2',
        'expense_utilities' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_debts' => 'decimal:2',
        'debt_repaid' => 'decimal:2',
        'debt_remaining' => 'decimal:2',
        'debt_monthly_installment' => 'decimal:2',
        'chemo_sessions_taken' => 'integer',
        'chemo_sessions_remaining' => 'integer',
        'radiation_sessions_taken' => 'integer',
        'radiation_sessions_remaining' => 'integer',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function researcher()
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable', 'entity_type', 'entity_id');
    }

    public function getHousePhotoUrlAttribute(): ?string
    {
        if (empty($this->house_photo_path)) {
            return null;
        }
        return app(\App\Services\ImageUploadService::class)->url($this->house_photo_path);
    }

    public function getConditionPhotoUrlsAttribute(): array
    {
        $urls = [];
        $paths = is_array($this->condition_photos) ? $this->condition_photos : [];
        $service = app(\App\Services\ImageUploadService::class);
        foreach ($paths as $p) {
            if (!empty($p)) {
                $urls[] = $service->url($p);
            }
        }
        return $urls;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'مجدولة',
            'completed' => 'تمت الزيارة والبحث',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getRecommendationLabelAttribute()
    {
        return match($this->recommendation) {
            'approve' => 'يوصى بالقبول',
            'reject' => 'يوصى بالرفض',
            'needs_review' => 'تحتاج مراجعة',
            default => 'قيد التقييم',
        };
    }

    public function getResearchTypeLabelAttribute(): string
    {
        return $this->has_cancer_patient ? 'بحث مريض أورام' : 'بحث اجتماعي عادي';
    }
}
