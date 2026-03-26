<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDailyMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'day_date',
        'responsible_user_id',
        'meal_type',
        'menu',
        'meal_count',
        'ingredients',
        'notes'
    ];

    protected $casts = [
        'day_date' => 'date',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
