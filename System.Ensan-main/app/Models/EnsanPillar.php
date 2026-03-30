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
    public function getIconUrlAttribute()
    {
        return $this->icon_path ? url('/api/media?path=' . $this->icon_path) : null;
    }

    /**
     * Get the full URL for the cover image
     */
    public function getCoverUrlAttribute()
    {
        return $this->cover_path ? url('/api/media?path=' . $this->cover_path) : null;
    }
    
    /**
     * Relationship with related projects (conceptually)
     * In a real implementation, you might have a many-to-many 
     * or use a category/tag system.
     */
}
