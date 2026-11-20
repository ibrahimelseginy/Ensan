<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FundBalance extends Model
{
    protected $fillable = ['fund_id', 'currency', 'available_amount', 'reserved_amount', 'lock_version'];
    protected $casts = ['available_amount' => 'decimal:4', 'reserved_amount' => 'decimal:4'];
    public function fund(): BelongsTo { return $this->belongsTo(Fund::class); }
}
