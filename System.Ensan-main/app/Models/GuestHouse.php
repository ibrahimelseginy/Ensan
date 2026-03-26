<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestHouse extends Model
{
    protected $fillable = ['name','location','phone','capacity','status','description','manager_user_id','manager_photo_url'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'guest_house_volunteers')
            ->withPivot('role','started_at','hours')
            ->withTimestamps();
    }

    public function monthlyVolunteers()
    {
        return $this->hasMany(GuestHouseMonthlyVolunteer::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }
}

