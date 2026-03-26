<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'researcher_id',
        'visit_date',
        'status',
        'report',
        'recommendation'
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function researcher()
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'مجدولة',
            'completed' => 'تمت الزيارة',
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
            default => '-',
        };
    }
}
