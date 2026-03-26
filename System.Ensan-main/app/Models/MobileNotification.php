<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileNotification extends Model
{
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'title',
        'body',
        'image_path',
        'target_audience',
        'is_sent',
        'sent_at'
    ];

    protected $appends = ['image_url'];
}
