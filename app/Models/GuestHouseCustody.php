<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseCustody extends Model
{
    protected $fillable = ['guest_house_id', 'name', 'type', 'treasury_id', 'warehouse_id', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function treasury() { return $this->belongsTo(Treasury::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}
