<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCategory extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function items(): HasMany
    {
        return $this->hasMany(DonationItem::class, 'category_id')->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(DonationItem::class, 'category_id')
            ->where('status', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
