<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GuestHouseMealServing extends Model
{
    protected $fillable = [
        'guest_house_meal_id',
        'beneficiary_id',
        'received',
        'received_at',
        'notes',
        'patient_meals_count',
        'companion_meals_count',
        'total_meals_count',
        'companion_received',
        'companion_name',
    ];

    protected $casts = [
        'received'               => 'boolean',
        'companion_received'     => 'boolean',
        'received_at'            => 'datetime',
        'patient_meals_count'    => 'integer',
        'companion_meals_count'  => 'integer',
        'total_meals_count'      => 'integer',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(GuestHouseMeal::class, 'guest_house_meal_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}
