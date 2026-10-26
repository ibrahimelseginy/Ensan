<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Attachment extends Model
{
    protected $fillable = ['entity_type','entity_id','path','mime','original_name'];

    public function getImageUrlAttribute(): ?string
    {
        return route('attachments.show', $this);
    }

    public function getViewUrlAttribute(): string
    {
        return route('attachments.show', $this);
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)
            || str_starts_with((string)$this->mime, 'image/');
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }
}
