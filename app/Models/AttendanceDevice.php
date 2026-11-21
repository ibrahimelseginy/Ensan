<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDevice extends Model
{
    protected $fillable = [
        'device_token',
        'device_name',
        'location_name',
        'guest_house_id',
        'authorized_by_user_id',
        'ip_address',
        'user_agent',
        'last_used_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by_user_id');
    }
}
