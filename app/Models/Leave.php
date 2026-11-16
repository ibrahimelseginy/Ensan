<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Leave extends Model
{
    use HasFactory, \App\Traits\HashedRouteKey;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'from_time',
        'to_time',
        'reason',
        'status',
        'approved_by',
        'rejection_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public static function typeLabels(): array
    {
        return [
            'annual'     => 'سنوية',
            'sick'       => 'مرضية',
            'emergency'  => 'عارضة',
            'unpaid'     => 'بدون راتب',
            'permission' => 'طلب إذن (بالساعات)',
            'other'      => 'أخرى',
        ];
    }

    public static function deductsFromBalance(?string $type): bool
    {
        return !in_array($type, ['unpaid', 'permission'], true);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? (string) $this->type;
    }

    public function getIsPermissionAttribute(): bool
    {
        return $this->type === 'permission';
    }

    public function getFromTimeFormattedAttribute(): ?string
    {
        return $this->from_time ? substr((string) $this->from_time, 0, 5) : null;
    }

    public function getToTimeFormattedAttribute(): ?string
    {
        return $this->to_time ? substr((string) $this->to_time, 0, 5) : null;
    }

    public function getPermissionDurationLabelAttribute(): ?string
    {
        if (!$this->from_time || !$this->to_time) {
            return null;
        }
        $from = \Carbon\Carbon::createFromFormat('H:i', substr((string) $this->from_time, 0, 5));
        $to   = \Carbon\Carbon::createFromFormat('H:i', substr((string) $this->to_time, 0, 5));
        $minutes = (int) round(max(0, $from->diffInMinutes($to, false)));
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} ساعة و {$mins} دقيقة";
        }
        if ($hours > 0) {
            return "{$hours} ساعة";
        }
        return "{$mins} دقيقة";
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function getDaysAttribute()
    {
        if ($this->type === 'permission') {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    protected static function booted()
    {
        static::created(function ($leave) {
            if ($leave->status === 'approved' && self::deductsFromBalance($leave->type)) {
                $leave->user?->adjustLeaveBalance(-$leave->days);
            }
        });

        static::updated(function ($leave) {
            if ($leave->isDirty('status')) {
                if ($leave->status === 'approved' && $leave->getOriginal('status') !== 'approved') {
                    if (self::deductsFromBalance($leave->type)) {
                        $leave->user?->adjustLeaveBalance(-$leave->days);
                    }
                } elseif ($leave->getOriginal('status') === 'approved' && $leave->status !== 'approved') {
                    if (self::deductsFromBalance($leave->type)) {
                        $leave->user?->adjustLeaveBalance($leave->days);
                    }
                }
            }

            // Handle day changes for already approved leaves
            if ($leave->status === 'approved' && !$leave->isDirty('status')) {
                if ($leave->isDirty('start_date') || $leave->isDirty('end_date') || $leave->isDirty('type')) {
                    $oldType = $leave->getOriginal('type');
                    $newType = $leave->type;

                    $oldStart = \Carbon\Carbon::parse($leave->getOriginal('start_date'));
                    $oldEnd   = \Carbon\Carbon::parse($leave->getOriginal('end_date'));
                    $oldDays  = $oldType === 'permission' ? 0 : ($oldStart->diffInDays($oldEnd) + 1);
                    $newDays  = $leave->days;

                    $oldDeducts = self::deductsFromBalance($oldType);
                    $newDeducts = self::deductsFromBalance($newType);

                    if (!$oldDeducts && $newDeducts) {
                        $leave->user?->adjustLeaveBalance(-$newDays);
                    } elseif ($oldDeducts && !$newDeducts) {
                        $leave->user?->adjustLeaveBalance($oldDays);
                    } elseif ($newDeducts) {
                        $diff = $newDays - $oldDays;
                        $leave->user?->adjustLeaveBalance(-$diff);
                    }
                }
            }
        });

        static::deleted(function ($leave) {
            if ($leave->status === 'approved' && self::deductsFromBalance($leave->type)) {
                $leave->user?->adjustLeaveBalance($leave->days);
            }
        });
    }

    public function changeRequests()
    {
        return $this->morphMany(ChangeRequest::class, 'subject', 'model_type', 'model_id');
    }
}
