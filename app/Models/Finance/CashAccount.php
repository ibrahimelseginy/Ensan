<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashAccount extends Model
{
    protected $fillable = ['code', 'name', 'kind', 'ledger_account_id', 'currency', 'legacy_treasury_id', 'bank_name', 'account_mask', 'manager_id', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function ledgerAccount(): BelongsTo { return $this->belongsTo(FinancialAccount::class, 'ledger_account_id'); }
}
