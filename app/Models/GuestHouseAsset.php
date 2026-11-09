<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class GuestHouseAsset extends Model
{
    protected $fillable = [
        'guest_house_id',
        'guest_house_wing_id',
        'name',
        'quantity',
        'code',
        'category',
        'location',
        'status',
        'serial_number',
        'purchase_date',
        'warranty_until',
        'cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_date' => 'date',
        'warranty_until' => 'date',
        'cost' => 'decimal:2',
    ];

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function wing(): BelongsTo
    {
        return $this->belongsTo(GuestHouseWing::class, 'guest_house_wing_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(GuestHouseAssetMaintenance::class, 'guest_house_asset_id')->latest('maintenance_date');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'electrical' => 'أجهزة كهربائية ومنزلية',
            'medical' => 'معدات وأجهزة طبية',
            'furniture' => 'أثاث وتجهيزات غرف',
            default => 'أخرى ومرافق عامة',
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'electrical' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
            'medical' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
            'furniture' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
            default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'electrical' => 'bi-lightning-charge-fill',
            'medical' => 'bi-heart-pulse-fill',
            'furniture' => 'bi-lamp-fill',
            default => 'bi-box-seam',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'working' => 'يعمل بكفاءة',
            'needs_maintenance' => 'يحتاج صيانة',
            'out_of_service' => 'معطل / خارج الخدمة',
            'scrapped' => 'تالف / كهنة',
            default => (string) $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'working' => 'bg-success text-white',
            'needs_maintenance' => 'bg-warning text-dark',
            'out_of_service' => 'bg-danger text-white',
            'scrapped' => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
    }
}
