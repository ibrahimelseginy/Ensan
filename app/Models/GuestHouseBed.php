<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class GuestHouseBed extends Model
{
    protected $fillable = ['guest_house_wing_id', 'number', 'type', 'status', 'notes'];

    public function wing() { return $this->belongsTo(GuestHouseWing::class, 'guest_house_wing_id'); }
    public function stays() { return $this->hasMany(GuestHouseStay::class); }
    public function activeStay() { return $this->hasOne(GuestHouseStay::class)->where('status', 'resident')->latestOfMany(); }

    public function isMattress(): bool
    {
        return ($this->type ?? 'standard') === 'mattress'
            || str_contains((string)$this->notes, 'مرتبة')
            || str_contains((string)$this->notes, 'مرتبه')
            || str_contains((string)$this->number, 'مرتبة')
            || str_contains((string)$this->number, 'مرتبه');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->isMattress() ? 'مرتبة إضافية' : 'سرير أساسي';
    }
}
