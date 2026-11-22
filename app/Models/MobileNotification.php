<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MobileNotification extends Model
{
    use \App\Traits\UploadsImages;

    public const CATEGORY_GENERAL = 'عام';
    public const CATEGORY_CAMPAIGN = 'حملات';
    public const CATEGORY_DONATION = 'تبرعات';
    public const CATEGORY_URGENT = 'عاجل';

    protected $fillable = [
        'title',
        'body',
        'image_path',
        'category',
        'target_audience',
        'target_type',
        'target_job_title',
        'target_department',
        'target_role',
        'target_user_id',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id')->withTrashed();
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user) {
            // 1. إشعار موجه لهذا الموظف شخصياً بالاسم
            $q->where('target_user_id', $user->id)
              // 2. إشعار عام لكافة موظفي المؤسسة (دون تحديد مستخدم خاص)
              ->orWhere(function (Builder $subAll) {
                  $subAll->where('target_type', 'all')
                         ->whereNull('target_user_id');
              });

            // 3. إشعار موجه لمسمى وظيفي يطابق وظيفة هذا الموظف
            if (!empty($user->job_title)) {
                $q->orWhere(function (Builder $sub) use ($user) {
                    $sub->where('target_type', 'job_title')
                        ->whereNull('target_user_id')
                        ->where(function ($subJt) use ($user) {
                            $subJt->where('target_job_title', $user->job_title)
                                  ->orWhere('target_job_title', 'like', '%' . $user->job_title . '%');
                        });
                });
            }

            // 4. إشعار موجه لقسم يطابق قسم هذا الموظف
            if (!empty($user->department)) {
                $q->orWhere(function (Builder $sub) use ($user) {
                    $sub->where('target_type', 'department')
                        ->whereNull('target_user_id')
                        ->where(function ($subDept) use ($user) {
                            $subDept->where('target_department', $user->department)
                                    ->orWhere('target_department', 'like', '%' . $user->department . '%');
                        });
                });
            }

            // 5. إشعار موجه لدور وظيفي يطابق دور هذا الموظف
            $userRoles = method_exists($user, 'roles') ? $user->roles()->pluck('name')->toArray() : [];
            if (!empty($user->role)) {
                $userRoles[] = $user->role;
            }
            if (!empty($userRoles)) {
                $q->orWhere(function (Builder $sub) use ($userRoles) {
                    $sub->where('target_type', 'role')
                        ->whereNull('target_user_id')
                        ->whereIn('target_role', array_unique($userRoles));
                });
            }
        });
    }

    public static function getCategories(): array
    {
        return [
            ['id' => self::CATEGORY_GENERAL, 'label' => 'عام', 'icon' => 'info-circle'],
            ['id' => self::CATEGORY_CAMPAIGN, 'label' => 'حملات', 'icon' => 'megaphone'],
            ['id' => self::CATEGORY_DONATION, 'label' => 'تبرعات', 'icon' => 'heart'],
            ['id' => self::CATEGORY_URGENT, 'label' => 'عاجل', 'icon' => 'exclamation-triangle'],
        ];
    }

    public function getTargetDescriptionAttribute(): string
    {
        return match ($this->target_type) {
            'job_title' => 'الوظيفة: ' . ($this->target_job_title ?: 'غير محدد'),
            'department' => 'القسم: ' . ($this->target_department ?: 'غير محدد'),
            'role' => 'الدور: ' . ($this->target_role ?: 'غير محدد'),
            'user' => 'الموظف: ' . ($this->targetUser?->name ?: 'محدد'),
            default => 'الجميع (' . ($this->target_audience ?? 'الكل') . ')',
        };
    }

    protected $appends = ['image_url', 'target_description'];
}
