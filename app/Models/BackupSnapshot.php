<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupSnapshot extends Model
{
    use HashedRouteKey;

    protected $fillable = [
        'snapshot_code',
        'scope',
        'trigger_type',
        'status',
        'format',
        'file_path',
        'file_name',
        'file_size_bytes',
        'sha256_checksum',
        'hmac_signature',
        'is_encrypted',
        'is_pinned',
        'db_tables_count',
        'files_count',
        'app_version',
        'notes',
        'error_message',
        'created_by',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'is_encrypted' => 'boolean',
        'is_pinned' => 'boolean',
        'db_tables_count' => 'integer',
        'files_count' => 'integer',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size_bytes;
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }

    public function getScopeLabelAttribute(): string
    {
        return $this->scope === 'full'
            ? 'نسخة كاملة (قاعدة البيانات + الملفات)'
            : 'قاعدة البيانات فقط';
    }

    public function getTriggerLabelAttribute(): string
    {
        return match ($this->trigger_type) {
            'scheduled' => 'مجدولة تلقائياً',
            'pre_restore' => 'لقطة أمان قبل الاستعادة',
            'uploaded' => 'مرفوعة يدوياً',
            default => 'يدوية',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'مكتملة',
            'failed' => 'فشلت',
            default => 'قيد التنفيذ',
        };
    }
}
