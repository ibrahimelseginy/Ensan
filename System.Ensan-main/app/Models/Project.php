<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
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

    protected $appends = ['image_url', 'icon_url'];

    protected $fillable = [
        'name',
        'fixed',
        'status',
        'description',
        'manager_user_id',
        'deputy_user_id',
        'manager_photo_url',
        'deputy_photo_url',
        'image_path',
        'category',
        'website_content',
        'sponsorship_details',
        'icon_path',
        'short_description',
        'features',
        'stats',
        'theme_colors',
        'action_text',
        'action_url',
        'is_visible',
        'show_badge',
        'badge_text',
        'badge_icon',
        'subcategory_text',
        'show_subcategory',
        'action_icon',
        'ui_button_color',
        'show_on_mobile',
        'mobile_content'
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'features' => 'array',
        'stats' => 'array',
        'theme_colors' => 'array',
        'is_visible' => 'boolean',
        'show_badge' => 'boolean',
        'show_subcategory' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('exclude_guest_house', function ($q) {
            $q->where('name', 'not like', '%دار ضيافة%')
                ->where('name', 'not like', '%ضيافة%');
        });
    }

    public function donations()
    {
        return $this->morphMany(Donation::class, 'donationable');
    }
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
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
        return $this->belongsToMany(User::class , 'project_volunteers')->withPivot(['role', 'started_at', 'campaign_id', 'hours'])->withTimestamps();
    }
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function monthlyVolunteers(): HasMany
    {
        return $this->hasMany(ProjectMonthlyVolunteer::class)->orderByDesc('year')->orderByDesc('month');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class)->orderByDesc('activity_date');
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }
}
