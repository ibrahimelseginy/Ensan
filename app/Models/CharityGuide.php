<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class CharityGuide extends Model
{
    use HasFactory;

    protected $table = 'charity_guides';

    protected $fillable = [
        'governorate',
        'center',
        'village',
        'coordinator_name',
        'phone',
        'secondary_phone',
        'exhibition_amount',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getCleanPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }
        $p = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($p, '20')) {
            return $p;
        }
        if (str_starts_with($p, '01')) {
            return '2' . $p;
        }
        if (str_starts_with($p, '1') && strlen($p) === 10) {
            return '20' . $p;
        }
        return $p;
    }
}

