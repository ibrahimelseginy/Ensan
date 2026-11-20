<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Fund extends Model
{
    protected $fillable = ['code', 'name', 'restriction_type', 'spending_policy', 'allow_negative', 'is_active', 'restriction_note'];
    protected $casts = ['allow_negative' => 'boolean', 'is_active' => 'boolean'];
    public function balances(): HasMany { return $this->hasMany(FundBalance::class); }
}
