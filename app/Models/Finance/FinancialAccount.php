<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FinancialAccount extends Model
{
    protected $fillable = ['code', 'name', 'classification', 'normal_balance', 'parent_id', 'is_posting', 'is_active', 'currency', 'description'];
    protected $casts = ['is_posting' => 'boolean', 'is_active' => 'boolean'];
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
}
