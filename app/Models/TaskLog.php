<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TaskLog extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'details',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionTitleAttribute(): string
    {
        return match ($this->action) {
            'created' => 'إنشاء المهمة',
            'status_changed' => 'تحديث الحالة',
            'attachment_added' => 'إرفاق ملف',
            'reviewed' => 'مراجعة المهمة',
            'completed' => 'إكمال المهمة واعتمادها',
            'assigned' => 'تعيين موظف',
            'note_added' => 'إضافة ملاحظة',
            default => $this->action,
        };
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'قائمة المهام',
            'in_progress' => 'قيد التنفيذ',
            'under_review' => 'قيد المراجعة',
            'done' => 'مكتملة ومراجعة',
            default => $status,
        };
    }
}
