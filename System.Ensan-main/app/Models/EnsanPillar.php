<?php

namespace App\Models;

use App\Traits\UploadsImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnsanPillar extends Model
{
    use HasFactory, UploadsImages;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'icon_path',
        'cover_path',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the full URL for the icon
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->getFileUrl('icon_path');
    }

    /**
     * Get the full URL for the cover image
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFileUrl('cover_path');
    }
    
    /**
     * Relationship with related projects
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'ensan_pillar_project');
    }

    /**
     * Relationship with related service items
     */
    public function services()
    {
        return $this->belongsToMany(MobileHomeItem::class, 'ensan_pillar_service_item', 'ensan_pillar_id', 'mobile_home_item_id');
    }
}
