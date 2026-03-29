<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MobileHomeItem extends Model
{
    use HasFactory, \App\Traits\UploadsImages;

    protected $fillable = [
        'type',
        'title',
        'description',
        'image_path',
        'icon',
        'price',
        'share_price',
        'details',
        'sort_order'
    ];

    public function getImageColumns(): array
    {
        return ['image_path', 'icon'];
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->getFileUrl('icon');
    }

    protected $appends = ['image_url', 'icon_url'];

    public function cards()
    {
        return $this->hasMany(MobileHeroCard::class, 'mobile_home_item_id');
    }
}
