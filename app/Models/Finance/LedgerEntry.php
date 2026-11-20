<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class LedgerEntry extends Model
{
    protected $fillable = ['entry_number', 'accounting_period_id', 'entry_date', 'posted_at', 'entry_type', 'currency', 'status', 'source_type', 'source_id', 'idempotency_key', 'reversal_of_id', 'document_id', 'posted_by', 'narrative', 'reference', 'metadata'];
    protected $casts = ['entry_date' => 'date', 'posted_at' => 'datetime', 'metadata' => 'array'];
    public function lines(): HasMany { return $this->hasMany(LedgerEntryLine::class); }
    public function reversalOf(): BelongsTo { return $this->belongsTo(self::class, 'reversal_of_id'); }
}
