<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'user_id',
        'token_selector',
        'token_validator_hash',
        'otp_code_hash',
        'channel',
        'target_destination',
        'attempts_count',
        'max_attempts',
        'is_used',
        'used_at',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_used'        => 'boolean',
        'attempts_count' => 'integer',
        'max_attempts'   => 'integer',
        'used_at'        => 'datetime',
        'expires_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired() && $this->attempts_count < $this->max_attempts;
    }
}
