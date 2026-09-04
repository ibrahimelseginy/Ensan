<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebContactMessage extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'image_path',
        'read'
    ];
}
