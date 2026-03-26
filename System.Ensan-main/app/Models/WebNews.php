<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebNews extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

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
        'statistic_description'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];
}
