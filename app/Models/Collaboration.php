<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Collaboration extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'name',
        'category',
        'specialty',
        'service_provided',
        'discount_volunteers',
        'activation_volunteers',
        'discount_beneficiaries',
        'activation_beneficiaries',
        'target_audience',
        'working_hours',
        'address',
        'city',
        'phone',
        'phone_alt',
        'contact_person',
        'contact_person_phone',
        'contact_person_role',
        'partnership_source',
        'start_date',
        'end_date',
        'status',
        'rating',
        'notes',
        'doc_links',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rating' => 'integer',
        'doc_links' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'معامل تحاليل ومراكز أشعة' => 'bi-radioactive',
            'عيادات وأطباء' => 'bi-hospital',
            'صيدليات' => 'bi-capsule',
            'مستشفيات ومراكز طبية' => 'bi-building-heart',
            'أكاديميات ومراكز تعليمية' => 'bi-mortarboard',
            'مكتبات ومطابع' => 'bi-book',
            'جيمات ونوادي رياضية' => 'bi-heart-pulse',
            'تجار جملة' => 'bi-shop',
            'شركات تكنولوجيا' => 'bi-laptop',
            default => 'bi-briefcase',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'تم عقد التعاون ونشط الآن',
            'extended' => 'ممدود وساري',
            'expired' => 'منتهي الصلاحية',
            'pending' => 'قيد التفاوض والاتفاق',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active', 'extended' => 'success',
            'expired' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    public function getRatingStarsAttribute(): string
    {
        $stars = max(1, min(5, (int) $this->rating));
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
    }
}
