<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnsanPillarCard extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'ensan_pillar_id',
        'title',
        'description',
        'price',
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

    public function pillar()
    {
        return $this->belongsTo(EnsanPillar::class, 'ensan_pillar_id');
    }
}
