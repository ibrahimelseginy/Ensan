<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouse extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = ['name','governorate','location','phone','capacity','status','description','manager_user_id','manager_photo_url'];

    protected static function booted(): void
    {
        static::saved(function (GuestHouse $gh) {
            \App\Models\Permission::updateOrCreate(
                ['key' => "guest_houses.manage.{$gh->id}"],
                ['name' => "إدارة دار ضيافة: {$gh->name}"]
            );
        });

        static::deleted(function (GuestHouse $gh) {
            \App\Models\Permission::where('key', "guest_houses.manage.{$gh->id}")->delete();
        });
    }

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

    public function wings() { return $this->hasMany(GuestHouseWing::class); }
    public function beds() { return $this->hasManyThrough(GuestHouseBed::class, GuestHouseWing::class); }
    public function stays() { return $this->hasMany(GuestHouseStay::class); }
    public function custodies() { return $this->hasMany(GuestHouseCustody::class); }
    public function meals() { return $this->hasMany(GuestHouseMeal::class); }
}
