<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadsImages;
use Illuminate\Support\Facades\Storage;

final class MobileNews extends Model
{
    use UploadsImages;

    protected $fillable = ['title', 'content', 'image_path', 'category', 'views', 'shares'];

    public function getImageColumn(): string
    {
        return 'image_path';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }
        return null;
    }

    protected $appends = ['image_url'];
}
