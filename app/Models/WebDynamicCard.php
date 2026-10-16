<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class WebDynamicCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'badge_text',
        'badge_icon',
        'tag_text',
        'main_button_text',
        'main_button_action',
        'sort_order',
        'card_color',
        'stats_data',
        'buttons_data',
        'badge_visible',
        'is_active',
    ];

    protected $casts = [
        'badge_visible' => 'boolean',
        'is_active' => 'boolean',
        'stats_data' => 'array',
        'buttons_data' => 'array',
    ];
}
