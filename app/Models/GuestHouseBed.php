<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseBed extends Model
{
    protected $fillable = ['guest_house_wing_id', 'number', 'status', 'notes'];

    public function wing() { return $this->belongsTo(GuestHouseWing::class, 'guest_house_wing_id'); }
    public function stays() { return $this->hasMany(GuestHouseStay::class); }
    public function activeStay() { return $this->hasOne(GuestHouseStay::class)->where('status', 'resident')->latestOfMany(); }
}
