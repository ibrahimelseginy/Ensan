<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasswordPolicy extends Model
{
    protected $fillable = [
        'tier_name',
        'display_name',
        'min_length',
        'max_age_days',
        'grace_period_days',
        'max_snoozes',
        'snooze_hours',
        'history_limit',
        'disallow_common_passwords',
        'require_mixed_case',
        'require_numbers',
        'require_special_chars',
    ];

    protected $casts = [
        'min_length'                => 'integer',
        'max_age_days'              => 'integer',
        'grace_period_days'         => 'integer',
        'max_snoozes'               => 'integer',
        'snooze_hours'              => 'integer',
        'history_limit'             => 'integer',
        'disallow_common_passwords' => 'boolean',
        'require_mixed_case'        => 'boolean',
        'require_numbers'           => 'boolean',
        'require_special_chars'     => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'security_tier', 'tier_name');
    }

    public static function forTier(string $tierName): self
    {
        return static::where('tier_name', $tierName)->first()
            ?? static::where('tier_name', 'tier2_field')->first()
            ?? new static([
                'tier_name'                 => 'tier2_field',
                'display_name'              => 'المستوى الافتراضي',
                'min_length'                => 10,
                'max_age_days'              => 180,
                'grace_period_days'         => 7,
                'max_snoozes'               => 3,
                'snooze_hours'              => 8,
                'history_limit'             => 6,
                'disallow_common_passwords' => true,
                'require_mixed_case'        => true,
                'require_numbers'           => true,
                'require_special_chars'     => true,
            ]);
    }
}
