<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileBanner extends Model
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'title',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $appends = ['image_url'];
