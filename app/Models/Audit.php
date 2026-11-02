<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Audit extends Model
{
    protected $fillable = ['user_id','method','path','status_code','ip','user_agent','entity_type','entity_id','payload'];
    protected $casts = ['payload' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionAttribute(): string
    {
        return match (strtoupper($this->method ?? '')) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'view',
        };
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->path ?? null;
    }

    public function getNewValuesAttribute(): ?array
    {
        return is_array($this->payload) ? $this->payload : null;
    }
}

