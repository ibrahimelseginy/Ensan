<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebPartner extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumn(): string
    {
        return 'logo_path';
    }

    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'logo_path',
        'description',
        'type',
        'website_url'
    ];
}
