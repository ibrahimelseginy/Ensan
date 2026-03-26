<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebDonor extends Authenticatable
{
    use Notifiable, \App\Traits\UploadsImages;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
        'active',
        'profile_photo_path',
        'city',
        'governorate',
        'country',
    ];

    protected $hidden = [
        'password',
        'otp_code',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'active'         => 'boolean',
        'otp_verified'   => 'boolean',
    ];

    /**
     * Get the verified donations from the main donations table.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'web_donor_id');
    }

    /**
     * Get the web-specific donations (usually pending/web-origin).
     */
    public function webDonations(): HasMany
    {
        return $this->hasMany(WebDonation::class, 'web_donor_id');
    }

    /**
     * Compatibility for PersonalAccessToken linking
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id')->where('tokenable_type', static::class);
    }

    public function createToken(string $name)
    {
        $plainTextToken = \Illuminate\Support\Str::random(40);
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
            'expires_at' => null,
            'tokenable_type' => static::class,
        ]);

        return (object) [
            'plainTextToken' => $plainTextToken,
            'accessToken' => $token,
        ];
    }
}
