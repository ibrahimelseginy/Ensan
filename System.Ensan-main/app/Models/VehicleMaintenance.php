<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VehicleMaintenance extends Model
{
    protected $table = 'vehicle_maintenance';

    protected $fillable = [
        'delegate_id',
        'vehicle_type',
        'vehicle_plate',
        'maintenance_type',
        'description',
        'cost',
        'maintenance_date',
        'next_maintenance_date',
        'odometer_reading',
        'status'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
        'odometer_reading' => 'integer'
    ];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }

    // Check if maintenance is due
    public function isDue()
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        return $this->next_maintenance_date <= now();
    }

    // Get maintenance type in Arabic
    public function getMaintenanceTypeArabic()
    {
        $types = [
            'oil_change' => 'تغيير زيت',
            'tire_change' => 'تغيير إطارات',
            'repair' => 'إصلاح',
            'inspection' => 'فحص دوري',
            'other' => 'أخرى'
        ];

        return $types[$this->maintenance_type] ?? $this->maintenance_type;
    }
}
