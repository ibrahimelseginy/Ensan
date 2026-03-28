<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ReceptionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_name',
        'phone',
        'visit_type',
        'purpose',
        'status',
        'directed_to',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getVisitTypeLabelAttribute()
    {
        return match($this->visit_type) {
            'personal' => 'زيارة شخصية',
            'phone' => 'مكالمة هاتفية',
            default => $this->visit_type,
        };
    }

    public function getPurposeLabelAttribute()
    {
        return match($this->purpose) {
            'help_request' => 'طلب مساعدة',
            'complaint' => 'شكوى',
            'donation' => 'تبرع',
            'inquiry' => 'استفسار',
            'other' => 'أخرى',
            default => $this->purpose,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'انتظار',
            'completed' => 'مكتمل',
            'directed' => 'تم التحويل',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'directed' => 'info',
            default => 'secondary',
        };
    }
}
