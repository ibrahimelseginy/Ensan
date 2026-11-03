<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GuestHouseSupportTicket extends Model
{
    protected $fillable = [
        'guest_house_id',
        'beneficiary_id',
        'guest_house_stay_id',
        'caller_name',
        'caller_phone',
        'caller_type',
        'category',
        'priority',
        'direction',
        'subject',
        'details',
        'action_taken',
        'status',
        'satisfaction_rating',
        'assigned_to',
        'created_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(GuestHouseStay::class, 'guest_house_stay_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'inquiry' => '📞 استفسار حجز ووصول',
            'medical_followup' => '🏥 متابعة جلسات مستشفى',
            'emergency' => '🚨 طوارئ وإسعاف عاجل',
            'resident_service' => '🛎️ خدمة ودعم نزيل بالدار',
            'complaint' => '⚠️ شكوى أو ملاحظة',
            'satisfaction_survey' => '⭐ استبيان رضا ومغادرة',
            default => (string) $this->category,
        };
    }

    public function getCallerTypeLabelAttribute(): string
    {
        return match ($this->caller_type) {
            'resident' => 'مريض مقيم بالدار',
            'companion' => 'مرافق مريض',
            'prospective_patient' => 'مريض قادم للاستفسار والحجز',
            'hospital' => 'جهة طبية / مستشفى',
            'donor' => 'متبرع أو زائر',
            default => 'أخرى',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'emergency' => 'طوارئ قصوى',
            'urgent' => 'عاجل وهام',
            default => 'عادي',
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'emergency' => 'bg-danger text-white pulse-animation',
            'urgent' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'مفتوح / جاري',
            'in_progress' => 'قيد المتابعة والتنفيذ',
            'resolved' => 'تم الحل والإنجاز',
            'cancelled' => 'ملغي',
            default => (string) $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-primary text-white',
            'in_progress' => 'bg-warning text-dark',
            'resolved' => 'bg-success text-white',
            'cancelled' => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
    }
}
