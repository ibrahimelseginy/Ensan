<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Delegate extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumn(): string
    {
        return 'profile_photo_path';
    }

    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'route_id',
        'active',
        'profile_photo_path',
        'user_id',
        'vehicle_type',
        'vehicle_plate',
        'license_number',
        'license_expiry',
        'emergency_contact',
        'emergency_phone',
        'national_id',
        'address',
        'hire_date',
        'notes'
    ];

    protected $casts = [
        'active' => 'boolean',
        'license_expiry' => 'date',
        'hire_date' => 'date'
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TravelRoute::class, 'route_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(DelegateTrip::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(DelegateRating::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function scheduledTrips(): HasMany
    {
        return $this->hasMany(ScheduledTrip::class);
    }

    // Calculate average rating
    public function getAverageRating()
    {
        return $this->ratings()->avg('overall_rating') ?? 0;
    }

    // Get total trips cost
    public function getTotalTripsCost()
    {
        $total = 0;
        foreach ($this->trips as $trip) {
            $total += $trip->calculateTotalCost();
        }
        return $total;
    }

    // Get total distance
    public function getTotalDistance()
    {
        return $this->trips()->sum('distance_km') ?? 0;
    }

    // Check if license is expired
    public function isLicenseExpired()
    {
        if (!$this->license_expiry) {
            return false;
        }
        return $this->license_expiry < now();
    }

    // Get upcoming maintenance
    public function getUpcomingMaintenance()
    {
        return $this->maintenanceRecords()
            ->where('next_maintenance_date', '<=', now()->addDays(30))
            ->where('status', 'completed')
            ->orderBy('next_maintenance_date')
            ->get();
    }
}
