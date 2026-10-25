<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileCaseApplication extends Model
{
    use \App\Traits\UploadsImages;

    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_RESEARCH = 'under_research';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public function getImageColumn(): string
    {
        return 'id_image_path';
    }

    public function getImageColumns(): array
    {
        return ['id_image_path', 'medical_report_path'];
    }

    protected $fillable = [
        'applicant_name',
        'submitter_type',
        'submitter_name',
        'submitter_phone',
        'submitter_relation',
        'applicant_phone',
        'backup_phone',
        'applicant_id_number',
        'case_type',
        'description',
        'governorate',
        'city',
        'address',
        'id_image_path',
        'medical_report_path',
        'status',
        'admin_notes',
        'rejection_reason',
        'assigned_researcher_id',
        'field_visit_id',
        'source',
        'source_name',
        'reference_number',
        'mother_name',
        'deceased_husband_name',
        'submitter_relation_to_mother',
        'transit_details',
        'death_details',
        'children_count',
        'children_details',
        'income_aid_sources',
        'expenses_debts_details',
        'zad_details',
        'need_type',
        'family_members_count',
        'social_status_description',
        'guide_name',
        'guide_phone',
        'midrar_details',
        'hospital_name',
        'tumor_location',
        'treatment_protocol',
        'tumor_details',
        'user_id',
        'guest_house_id',
        'beneficiary_id',
        'guest_house_stay_id'
    ];

    protected $casts = [
        'zad_details' => 'array',
        'midrar_details' => 'array',
        'tumor_details' => 'array',
    ];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function stay() { return $this->belongsTo(GuestHouseStay::class, 'guest_house_stay_id'); }
    public function assignedResearcher() { return $this->belongsTo(User::class, 'assigned_researcher_id'); }
    public function fieldVisit() { return $this->belongsTo(FieldVisit::class, 'field_visit_id'); }

    public function getIdImageUrlAttribute(): ?string
    {
        return $this->getFileUrl('id_image_path');
    }

    public function getMedicalReportUrlAttribute(): ?string
    {
        return $this->getFileUrl('medical_report_path');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending', 'في انتظار البحث' => 'في انتظار البحث',
            'under_research', 'تحت البحث', 'reviewed' => 'تحت البحث',
            'rejected', 'مرفوض' => 'مرفوض',
            'completed', 'تم البحث', 'accepted', 'approved' => 'تم البحث',
            default => (string)($this->status ?? 'غير محدد'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending', 'في انتظار البحث' => 'warning',
            'under_research', 'تحت البحث', 'reviewed' => 'info',
            'rejected', 'مرفوض' => 'danger',
            'completed', 'تم البحث', 'accepted', 'approved' => 'success',
            default => 'secondary',
        };
    }

    public function getCaseTypeLabelAttribute(): string
    {
        return match($this->case_type) {
            'zad', 'أيتام' => 'كفالة أيتام (مشروع زاد)',
            'midrar', 'water', 'وصلة مياه' => 'مشروع مدرار (وصلات مياه / أسقف)',
            'hope', 'أورام', 'medical' => 'مرضى أورام وعلاج (بعثاء الأمل)',
            'roof', 'ترميم' => 'سقف وترميم منازل',
            'financial' => 'مساعدة مالية / كفالة',
            'food', 'عينية' => 'سلة غذائية / مساعدة عينية',
            'education' => 'مساعدة تعليمية',
            default => (string)($this->case_type ?? 'عام'),
        };
    }

    public function getSourceCategoryAttribute(): string
    {
        $src = strtolower((string)($this->source ?? 'mobile'));
        if (str_starts_with($src, 'web')) {
            return 'website';
        }
        if (in_array($src, ['system', 'admin_dashboard', 'dashboard', 'manual'])) {
            return 'system';
        }
        return 'mobile';
    }

    public function getSourceBadgeAttribute(): array
    {
        return match($this->source_category) {
            'website' => [
                'key' => 'website',
                'label' => 'الويبسايت',
                'sub' => match($this->source) {
                    'website_zad' => 'استمارة زاد',
                    'website_midrar' => 'استمارة مدرار',
                    'website_hope' => 'بعثاء الأمل',
                    default => 'الموقع الإلكتروني'
                },
                'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                'badge_color' => 'info',
                'icon' => 'bi-globe2'
            ],
            'system' => [
                'key' => 'system',
                'label' => 'السيستم',
                'sub' => 'لوحة التحكم',
                'class' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                'badge_color' => 'primary',
                'icon' => 'bi-laptop'
            ],
            default => [
                'key' => 'mobile',
                'label' => 'الموبايل',
                'sub' => 'تطبيق إنسان',
                'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                'badge_color' => 'success',
                'icon' => 'bi-phone'
            ],
        };
    }

    public function getNationalIdAttribute(): ?string
    {
        return $this->applicant_id_number;
    }

    public function setNationalIdAttribute($value): void
    {
        $this->attributes['applicant_id_number'] = $value;
    }

    protected $appends = ['id_image_url', 'medical_report_url', 'status_label', 'status_color', 'case_type_label', 'national_id', 'source_category', 'source_badge'];
}
