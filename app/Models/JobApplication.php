<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * طلب توظيف وارد من الموقع الإلكتروني (web_Ensan) ويُتابَع من قسم الموارد البشرية.
 */
final class JobApplication extends Model
{
    protected $table = 'job_applications';

    public const STATUS_NEW       = 'new';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_INTERVIEW = 'interview';
    public const STATUS_ACCEPTED  = 'accepted';
    public const STATUS_REJECTED  = 'rejected';

    public const STATUSES = [
        self::STATUS_NEW       => 'جديد - قيد الاستلام',
        self::STATUS_REVIEWING => 'قيد المراجعة',
        self::STATUS_INTERVIEW => 'تم تحديد مقابلة',
        self::STATUS_ACCEPTED  => 'تم القبول',
        self::STATUS_REJECTED  => 'مرفوض',
    ];

    protected $fillable = [
        'tracking_code',
        'full_name',
        'age',
        'phone',
        'email',
        'residence',
        'position',
        'faculty',
        'has_volunteered',
        'volunteer_details',
        'cv_path',
        'status',
        'interview_date',
        'interview_location',
        'hr_notes',
        'applicant_message',
        'reviewed_by',
        'reviewed_at',
        'source',
    ];

    protected $casts = [
        'has_volunteered' => 'boolean',
        'age'             => 'integer',
        'interview_date'  => 'datetime',
        'reviewed_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobApplication $model): void {
            if (empty($model->tracking_code)) {
                do {
                    $code = 'JOB-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
                } while (self::where('tracking_code', $code)->exists());
                $model->tracking_code = $code;
            }
            if (empty($model->status)) {
                $model->status = self::STATUS_NEW;
            }
        });
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? self::STATUSES[self::STATUS_NEW];
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED  => 'success',
            self::STATUS_REJECTED  => 'danger',
            self::STATUS_INTERVIEW => 'warning',
            self::STATUS_REVIEWING => 'info',
            default                => 'primary',
        };
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '2' . $digits; // مصر: 01xxxxxxxxx -> 201xxxxxxxxx
        }
        return 'https://wa.me/' . $digits;
    }

    /**
     * البيانات الآمنة التي تُعرض للمتقدم عند الاستعلام عن حالة طلبه (بدون ملاحظات HR الداخلية).
     */
    public function toPublicArray(): array
    {
        return [
            'tracking_code'      => $this->tracking_code,
            'full_name'          => $this->full_name,
            'position'           => $this->position,
            'status'             => $this->status,
            'status_label'       => $this->status_label,
            'interview_date'     => $this->interview_date?->format('Y-m-d H:i'),
            'interview_location' => $this->interview_location,
            'applicant_message'  => $this->applicant_message,
            'created_at'         => $this->created_at?->format('Y-m-d'),
            'reviewed_at'        => $this->reviewed_at?->format('Y-m-d'),
        ];
    }
}

