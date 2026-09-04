<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseStay extends Model
{
    protected $fillable = [
        'guest_house_id', 'beneficiary_id', 'guest_house_bed_id', 'previous_stay_id',
        'source_type', 'source_id', 'status', 'arrival_date', 'expected_days',
        'admitted_at', 'departed_at', 'notes', 'approved_by',
    ];
    protected $casts = ['arrival_date' => 'date', 'admitted_at' => 'datetime', 'departed_at' => 'datetime'];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function bed() { return $this->belongsTo(GuestHouseBed::class, 'guest_house_bed_id'); }
    public function previousStay() { return $this->belongsTo(self::class, 'previous_stay_id'); }
    public function source() { return $this->morphTo(); }
}
