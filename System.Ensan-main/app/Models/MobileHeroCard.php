<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MobileHeroCard extends Model
{
    use HasFactory, \App\Traits\UploadsImages;

    protected $fillable = [
        'mobile_home_item_id',
        'title',
        'description',
        'image_path'
    ];

    public function getImageColumns(): array
    {
        return ['image_path'];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFileUrl('image_path');
    }

    protected $appends = ['image_url'];

    public function hero()
    {
        return $this->belongsTo(MobileHomeItem::class, 'mobile_home_item_id');
    }
}
