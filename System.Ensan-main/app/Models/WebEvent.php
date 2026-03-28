<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebEvent extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $table = 'web_events';

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'location',
        'category',
        'event_date',
        'event_end_date',
        'views_count',
        'shares_count',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];
}
