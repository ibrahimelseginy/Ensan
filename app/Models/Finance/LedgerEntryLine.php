<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LedgerEntryLine extends Model
{
    protected $fillable = ['ledger_entry_id', 'line_no', 'financial_account_id', 'debit', 'credit', 'fund_id', 'cash_account_id', 'project_id', 'campaign_id', 'beneficiary_id', 'custody_id', 'counterparty_type', 'counterparty_id', 'description', 'dimensions'];
    protected $casts = ['debit' => 'decimal:4', 'credit' => 'decimal:4', 'dimensions' => 'array'];
    public function entry(): BelongsTo { return $this->belongsTo(LedgerEntry::class, 'ledger_entry_id'); }
}
