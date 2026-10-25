<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

final class Task extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'title',
        'volunteer_activity_name',
        'description',
        'assigned_to',
        'assigned_by',
        'due_date',
        'status',
        'priority',
        'reviewed_by',
        'reviewed_at',
        'completed_at',
        'order',
        'project_id',
        'campaign_id',
        'guest_house_id',
        'rating',
        'evaluation_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')->withPivot('role')->withTimestamps();
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TaskLog::class)->latest();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function logActivity(string $action, ?string $fromStatus = null, ?string $toStatus = null, ?string $details = null): TaskLog
    {
        return $this->logs()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'details' => $details,
        ]);
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قائمة المهام',
            'in_progress' => 'قيد التنفيذ',
            'under_review' => 'قيد المراجعة',
            'done' => 'مكتملة ومراجعة',
            default => $this->status,
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'urgent' => ['label' => 'عاجل جداً', 'color' => 'danger', 'icon' => 'fire'],
            'high' => ['label' => 'أولوية قصوى', 'color' => 'warning', 'icon' => 'arrow-up'],
            'low' => ['label' => 'منخفضة', 'color' => 'info', 'icon' => 'arrow-down'],
            default => ['label' => 'عادية', 'color' => 'secondary', 'icon' => 'dash'],
        };
    }

    /** Check if user can see or access this task. */
    public function canAccess(?User $user): bool
    {
        if (!$user) return false;
        if ($user->roles()->whereIn('key', ['admin', 'manager'])->exists()) return true;
        if ($this->assigned_to === $user->id || $this->assigned_by === $user->id) return true;
        return $this->assignees()->where('users.id', $user->id)->exists();
    }
}
