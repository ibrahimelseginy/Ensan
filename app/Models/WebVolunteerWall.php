<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebVolunteerWall extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $table = 'web_volunteers_wall';
    protected $fillable = ['name', 'role', 'hours', 'rank', 'image_path'];
}
