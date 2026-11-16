<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupSetting extends Model
{
    protected $fillable = [
        'auto_enabled',
        'frequency',
        'schedule_time',
        'default_scope',
        'keep_daily_count',
        'keep_weekly_count',
        'keep_monthly_count',
        'max_storage_mb',
        'encrypt_backups',
        'last_run_at',
        'last_status',
        'updated_by',
    ];

    protected $casts = [
        'auto_enabled' => 'boolean',
        'encrypt_backups' => 'boolean',
        'keep_daily_count' => 'integer',
        'keep_weekly_count' => 'integer',
        'keep_monthly_count' => 'integer',
        'max_storage_mb' => 'integer',
        'last_run_at' => 'datetime',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'auto_enabled' => true,
                'frequency' => 'daily',
                'schedule_time' => '02:00',
                'default_scope' => 'db_only',
                'keep_daily_count' => 7,
                'keep_weekly_count' => 4,
                'keep_monthly_count' => 3,
                'max_storage_mb' => 10240,
                'encrypt_backups' => true,
            ]
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
