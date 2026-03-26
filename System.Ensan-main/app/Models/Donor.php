<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $fillable = [
        'name','type','phone','email','address','classification','recurring_cycle','active','sponsorship_type','sponsored_beneficiary_id','sponsorship_project_id','sponsorship_monthly_amount',
        'allocation_type','campaign_id','guest_house_id'
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function webDonations(): HasMany
    {
        return $this->hasMany(WebDonation::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }
}
