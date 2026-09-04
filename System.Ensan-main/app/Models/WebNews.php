<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebNews extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $attributes = [
        'views_count' => 0,
        'shares_count' => 0,
    ];

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'category',
        'contact_name',
        'contact_number',
        'views_count',
        'shares_count',
        'published_at',
        'statistic_number',
        'statistic_description',
        'images'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'images' => 'array'
    ];
}
