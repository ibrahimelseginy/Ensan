<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'channel',
        'metadata_payload',
        'created_at',
    ];

    protected $casts = [
        'metadata_payload' => 'array',
        'created_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        ?int $userId,
        string $eventType,
        string $severity = 'info',
        array $metadata = [],
        ?string $channel = 'web'
    ): self {
        return static::create([
            'user_id'          => $userId,
            'event_type'       => $eventType,
            'severity'         => $severity,
            'ip_address'       => request()->ip() ?? '127.0.0.1',
            'user_agent'       => substr((string) request()->userAgent(), 0, 500),
            'channel'          => $channel,
            'metadata_payload' => $metadata,
            'created_at'       => now(),
        ]);
    }
}
