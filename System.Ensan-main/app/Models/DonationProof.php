<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationProof extends Model
{
    protected $fillable = [
        'donation_id',
        'web_donation_id',
        'image_path',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime'
    ];

    protected $appends = ['image_url'];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function webDonation(): BelongsTo
    {
        return $this->belongsTo(WebDonation::class, 'web_donation_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
