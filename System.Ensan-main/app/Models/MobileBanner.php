<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileBanner extends Model
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'title',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $appends = ['image_url'];
