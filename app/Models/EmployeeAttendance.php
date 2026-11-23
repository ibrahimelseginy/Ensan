<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmployeeAttendance extends Model
{
    use \App\Traits\HashedRouteKey;

    public const WORK_START_TIME = '10:15';

    protected $fillable = [
        'user_id',
        'date',
        'check_in_at',
        'check_in_photo',
        'check_in_lat',
        'check_in_lng',
        'check_out_at',
        'check_out_photo',
        'check_out_lat',
        'check_out_lng',
        'notes',
        'rating',
        'evaluation_notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * حساب دقائق التأخير بعد الساعة 10:15 ص
     */
    public function getLateMinutesAttribute(): int
    {
        if (empty($this->check_in_at)) {
            return 0;
        }

        try {
            $rawTime = trim((string) $this->check_in_at);
            $parts = explode(':', $rawTime);
            if (count($parts) >= 2) {
                $checkInMinutes = ((int) $parts[0] * 60) + (int) $parts[1];
                $workStartMinutes = (10 * 60) + 15; // 615 دقيقة = 10:15 ص

                if ($checkInMinutes > $workStartMinutes) {
                    return $checkInMinutes - $workStartMinutes;
                }
            }
        } catch (\Throwable $e) {
            return 0;
        }

        return 0;
    }

    /**
     * هل تسجيل الحضور بعد 10:15 ص؟
     */
    public function getIsLateAttribute(): bool
    {
        return $this->late_minutes > 0;
    }

    /**
     * نص توضيحي لتفاصيل التأخير
     */
    public function getDelayTextAttribute(): string
    {
        $minutes = $this->late_minutes;
        if ($minutes <= 0) {
            return 'في الموعد';
        }

        $hours = intdiv($minutes, 60);
        $remMins = $minutes % 60;

        if ($hours > 0 && $remMins > 0) {
            return "تأخير {$hours} س و {$remMins} د";
        } elseif ($hours > 0) {
            return "تأخير {$hours} " . ($hours === 1 ? 'ساعة' : ($hours === 2 ? 'ساعتين' : 'ساعات'));
        }

        return "تأخير {$remMins} دقيقة";
    }
}
