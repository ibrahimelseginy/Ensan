<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DonationItem extends Model
{
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'category_id', 'title', 'description',
        'icon', 'image', 'status', 'sort_order', 'bg_style'
    ];

    protected $casts = ['status' => 'boolean'];

    protected $appends = ['icon_url', 'image_url'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class, 'category_id');
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->getFileUrl('icon');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFileUrl('image');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
