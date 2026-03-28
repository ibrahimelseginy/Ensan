<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'rejection_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    protected static function booted()
    {
        static::created(function ($leave) {
            if ($leave->status === 'approved' && $leave->type !== 'unpaid') {
                $leave->user->adjustLeaveBalance(-$leave->days);
            }
        });

        static::updated(function ($leave) {
            if ($leave->isDirty('status')) {
                if ($leave->status === 'approved' && $leave->getOriginal('status') !== 'approved') {
                    // Deduct
                    if ($leave->type !== 'unpaid') {
                        $leave->user->adjustLeaveBalance(-$leave->days);
                    }
                } elseif ($leave->getOriginal('status') === 'approved' && $leave->status !== 'approved') {
                    // Refund
                    if ($leave->type !== 'unpaid') {
                        $leave->user->adjustLeaveBalance($leave->days);
                    }
                }
            }
            
            // Handle day changes for already approved leaves
            if ($leave->status === 'approved' && !$leave->isDirty('status')) {
                if ($leave->isDirty('start_date') || $leave->isDirty('end_date') || $leave->isDirty('type')) {
                    $oldStart = \Carbon\Carbon::parse($leave->getOriginal('start_date'));
                    $oldEnd = \Carbon\Carbon::parse($leave->getOriginal('end_date'));
                    $oldDays = $oldStart->diffInDays($oldEnd) + 1;
                    $newDays = $leave->days;
                    
                    $oldType = $leave->getOriginal('type');
                    $newType = $leave->type;
                    
                    if ($oldType === 'unpaid' && $newType !== 'unpaid') {
                        $leave->user->adjustLeaveBalance(-$newDays);
                    } elseif ($oldType !== 'unpaid' && $newType === 'unpaid') {
                        $leave->user->adjustLeaveBalance($oldDays);
                    } elseif ($newType !== 'unpaid') {
                        $diff = $newDays - $oldDays;
                        $leave->user->adjustLeaveBalance(-$diff);
                    }
                }
            }
        });

        static::deleted(function ($leave) {
            if ($leave->status === 'approved') {
                if ($leave->type !== 'unpaid') {
                    $leave->user->adjustLeaveBalance($leave->days);
                }
            }
        });
    }

    public function changeRequests()
    {
        return $this->morphMany(ChangeRequest::class, 'subject', 'model_type', 'model_id');
    }
}
