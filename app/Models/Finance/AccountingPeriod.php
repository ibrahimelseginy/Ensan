<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

final class AccountingPeriod extends Model
{
    protected $fillable = ['code', 'starts_on', 'ends_on', 'status', 'closed_by', 'closed_at', 'close_note'];
    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'closed_at' => 'datetime'];
}
