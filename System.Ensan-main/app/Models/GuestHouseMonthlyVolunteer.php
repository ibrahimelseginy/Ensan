<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestHouseMonthlyVolunteer extends Model
{
    protected $fillable = ['guest_house_id', 'user_id', 'month', 'year', 'notes'];

    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
