<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseWing extends Model
{
    protected $fillable = ['guest_house_id', 'name', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function beds() { return $this->hasMany(GuestHouseBed::class); }
}
