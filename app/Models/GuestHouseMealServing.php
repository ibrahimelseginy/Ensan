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

    /**
     * Determines whether the given companion name string indicates an actual companion.
     * Returns false if null, empty, or negative phrasing like 'لا يوجد', 'بدون مرافق', etc.
     */
    public static function isRealCompanion(?string $name): bool
    {
        if ($name === null) {
            return false;
        }

        $clean = trim($name);
        if ($clean === '') {
            return false;
        }

        // Normalize Arabic letters and extra spaces
        $norm = preg_replace('/\s+/u', ' ', $clean);
        $norm = str_replace(['أ', 'إ', 'آ'], 'ا', $norm);
        $norm = str_replace(['ة'], 'ه', $norm);
        $norm = mb_strtolower(trim($norm), 'UTF-8');

        $negativeTerms = [
            'لا يوجد',
            'لايوجد',
            'لا يوجد مرافق',
            'لايوجد مرافق',
            'لا يوجد مرافقين',
            'لايوجد مرافقين',
            'بدون مرافق',
            'بدون مرافقين',
            'بدون مرافق له',
            'بدون',
            'لا',
            'كلا',
            'منفرد',
            'لوحده',
            'بمفرده',
            'غير متوفر',
            '-',
            '--',
            '---',
            'none',
            'no',
            'null',
            'n/a',
            'na',
        ];

        return !in_array($norm, $negativeTerms, true);
    }

    public function hasCompanion(): bool
    {
        return self::isRealCompanion($this->companion_name);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(GuestHouseMeal::class, 'guest_house_meal_id');
    }

    public function guestHouseMeal(): BelongsTo
    {
        return $this->belongsTo(GuestHouseMeal::class, 'guest_house_meal_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}

