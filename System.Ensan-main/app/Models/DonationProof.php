<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DonationProof extends Model
{
    use \App\Traits\UploadsImages;

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
        return $this->getFileUrl('image_path');
    }
}
