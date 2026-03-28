<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Campaign extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumns(): array
    {
        return ['image_path', 'icon_path', 'manager_photo_url', 'deputy_photo_url'];
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->getFileUrl('icon_path');
    }

    protected $appends = ['image_url', 'icon_url', 'progress_percentage'];

    public function getProgressPercentageAttribute()
    {
        if ($this->goal_amount > 0) {
            return round(($this->current_amount / $this->goal_amount) * 100, 2);
        }
        return 0;
    }

    protected $fillable = [
        'name',
        'season_title',
        'season_year',
        'start_date',
        'end_date',
        'status',
        'project_id',
        'manager_user_id',
        'deputy_user_id',
        'manager_photo_url',
        'deputy_photo_url',
        'image_path',
        'category',
        'website_content',
        'goal_amount',
        'goal_unit',
        'current_amount',
        'beneficiaries_count',
        'share_price',
        'ui_contribute_btn',
        'ui_remind_btn',
        'ui_ended_btn',
        'ui_filter_upcoming',
        'ui_collected_label',
        'ui_benefited_label',
        'ui_share_label',
        'ui_goal_label',
        'icon_path',
        'start_date_text',
        'ui_progress_override',
        'ui_collected_override',
        'ui_goal_override',
        'ui_beneficiaries_override',
        'ui_share_override',
        'ui_theme_color',
        'ui_button_color',
        'action_url',
        'show_on_mobile',
        'mobile_content'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function donations()
    {
        return $this->morphMany(Donation::class, 'donationable');
    }
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class , 'manager_user_id');
    }

    public function deputy(): BelongsTo
    {
        return $this->belongsTo(User::class , 'deputy_user_id');
    }


    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(User::class , 'campaign_volunteers')
            ->withPivot('role', 'started_at', 'hours')
            ->withTimestamps();
    }

    public function monthlyVolunteers(): HasMany
    {
        return $this->hasMany(CampaignMonthlyVolunteer::class)->orderByDesc('year')->orderByDesc('month');
    }

    public function dailyMenus(): HasMany
    {
        return $this->hasMany(CampaignDailyMenu::class)->orderBy('day_date');
    }
}
