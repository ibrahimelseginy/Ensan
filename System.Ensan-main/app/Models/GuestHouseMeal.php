<?php

namespace App\Models;

use App\Traits\UploadsImages;
use Illuminate\Database\Eloquent\Model;

final class GuestHouseMeal extends Model
{
    use UploadsImages;

    protected $fillable = ['guest_house_id', 'meal_date', 'meal_type', 'served_at', 'image_path', 'notes', 'created_by'];
    protected $casts = ['meal_date' => 'date'];

    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
    public function servings() { return $this->hasMany(GuestHouseMealServing::class); }
}
