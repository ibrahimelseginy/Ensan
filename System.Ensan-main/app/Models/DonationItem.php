<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationItem extends Model
{
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
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
