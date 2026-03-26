<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledTrip extends Model
{
    protected $fillable = [
        'delegate_id',
        'route_id',
        'title',
        'description',
        'scheduled_date',
        'scheduled_time',
        'from_location',
        'to_location',
        'estimated_cost',
        'estimated_distance',
        'status',
        'actual_trip_id',
        'notes'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'estimated_distance' => 'decimal:2'
    ];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(TravelRoute::class, 'route_id');
    }

    public function actualTrip(): BelongsTo
    {
        return $this->belongsTo(DelegateTrip::class, 'actual_trip_id');
    }

    // Check if trip is upcoming
    public function isUpcoming()
    {
        return $this->scheduled_date > now() && $this->status === 'scheduled';
    }

    // Check if trip is overdue
    public function isOverdue()
    {
        return $this->scheduled_date < now() && in_array($this->status, ['scheduled', 'confirmed']);
    }

    // Get status in Arabic
    public function getStatusArabic()
    {
        $statuses = [
            'scheduled' => 'مجدول',
            'confirmed' => 'مؤكد',
            'in_progress' => 'جاري',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Convert to actual trip
    public function convertToActualTrip()
    {
        if ($this->actual_trip_id) {
            return $this->actualTrip;
        }

        $trip = DelegateTrip::create([
            'delegate_id' => $this->delegate_id,
            'date' => $this->scheduled_date,
            'description' => $this->title . "\n" . $this->description,
            'cost' => $this->estimated_cost ?? 0,
            'from_location' => $this->from_location,
            'to_location' => $this->to_location,
            'distance_km' => $this->estimated_distance,
            'status' => 'pending',
            'notes' => $this->notes
        ]);

        $this->update([
            'actual_trip_id' => $trip->id,
            'status' => 'completed'
        ]);

        return $trip;
    }
}
