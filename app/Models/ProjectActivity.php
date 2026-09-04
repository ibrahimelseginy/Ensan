<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProjectActivity extends Model
{
    use HasFactory, \App\Traits\HashedRouteKey;

    protected $fillable = ['project_id', 'type', 'location', 'activity_date', 'revenue', 'expenses', 'description'];

    protected $casts = ['activity_date' => 'date', 'revenue' => 'decimal:2', 'expenses' => 'decimal:2'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
