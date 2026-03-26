<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebContactMessage extends Model
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
