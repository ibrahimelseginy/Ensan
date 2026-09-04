<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DelegateRating extends Model
{
    protected $fillable = [
        'delegate_id',
        'trip_id',
        'rated_by',
        'punctuality_rating',
        'professionalism_rating',
        'communication_rating',
        'overall_rating',
        'comments',
        'rating_date'
    ];

    protected $casts = [
        'rating_date' => 'date',
        'punctuality_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'communication_rating' => 'integer',
        'overall_rating' => 'integer'
    ];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(DelegateTrip::class, 'trip_id');
    }

    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    // Calculate average rating
    public function calculateAverageRating()
    {
        return ($this->punctuality_rating + $this->professionalism_rating + 
                $this->communication_rating + $this->overall_rating) / 4;
    }
}
