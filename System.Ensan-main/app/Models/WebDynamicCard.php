<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebDynamicCard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'badge_visible' => 'boolean',
        'is_active' => 'boolean',
        'stats_data' => 'array',
        'buttons_data' => 'array',
    ];
}
