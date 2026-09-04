<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseMealServing extends Model
{
    protected $fillable = ['guest_house_meal_id', 'beneficiary_id', 'received', 'received_at', 'notes'];
    protected $casts = ['received' => 'boolean', 'received_at' => 'datetime'];

    public function meal() { return $this->belongsTo(GuestHouseMeal::class, 'guest_house_meal_id'); }
    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
}
