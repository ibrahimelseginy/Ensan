<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebTestimonial extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = ['name', 'role', 'content', 'rating', 'image_path', 'status'];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getContentAttribute($value)
    {
        if (request()->is('api/*') && !request()->is('api/*/admin/*')) {
            $parts = explode('---', $value);
            return trim($parts[0]);
        }
        return $value;
    }
}
