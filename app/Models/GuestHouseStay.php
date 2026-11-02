<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseStay extends Model
{
    protected $fillable = [
        'guest_house_id', 'beneficiary_id', 'guest_house_bed_id', 'previous_stay_id',
        'source_type', 'source_id', 'status', 'is_temporary_out', 'temporary_out_at',
        'arrival_date', 'expected_days', 'admitted_at', 'departed_at', 'notes', 'approved_by',
    ];
    protected $casts = [
        'arrival_date' => 'date',
        'admitted_at' => 'datetime',
        'departed_at' => 'datetime',
        'is_temporary_out' => 'boolean',
        'temporary_out_at' => 'datetime',
    ];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function bed() { return $this->belongsTo(GuestHouseBed::class, 'guest_house_bed_id'); }
    public function previousStay() { return $this->belongsTo(self::class, 'previous_stay_id'); }
    public function source() { return $this->morphTo(); }

    public function getActualDaysAttribute(): int
    {
        if (!$this->arrival_date) {
            return (int) ($this->expected_days ?? 1);
        }
        $start = \Carbon\Carbon::parse($this->arrival_date)->startOfDay();
        $end = $this->departed_at 
            ? \Carbon\Carbon::parse($this->departed_at)->startOfDay() 
            : now()->startOfDay();
        return max(1, $start->diffInDays($end));
    }
}
