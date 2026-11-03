<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GuestHouseAssetMaintenance extends Model
{
    protected $fillable = [
        'guest_house_asset_id',
        'maintenance_date',
        'issue_description',
        'cost',
        'technician_name',
        'technician_phone',
        'invoice_path',
        'status',
        'completion_notes',
        'created_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(GuestHouseAsset::class, 'guest_house_asset_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getInvoiceUrlAttribute(): ?string
    {
        if (empty($this->invoice_path)) {
            return null;
        }
        return app(\App\Services\ImageUploadService::class)->url($this->invoice_path);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'بلاغ قيد الانتظار',
            'in_progress' => 'جاري الإصلاح والصيانة',
            'completed' => 'تم الإصلاح بنجاح',
            default => (string) $this->status,
        };
    }
}
