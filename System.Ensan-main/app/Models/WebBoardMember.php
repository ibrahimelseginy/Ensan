<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebBoardMember extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'role',
        'description',
        'image_path',
        'sort_order'
    ];
}
