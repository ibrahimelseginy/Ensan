<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class WebPage extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'sort_order'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $slug = Str::slug($model->title);
                // Ensure uniqueness
                $originalSlug = $slug;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $model->slug = $slug;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
            // Should we update slug? Usually better not to change URL unless requested.
            // Keeping it as is unless explicitly changed.
            }
        });
    }
}
