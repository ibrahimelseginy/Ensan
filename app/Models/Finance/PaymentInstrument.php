<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PaymentInstrument extends Model
{
    protected $table = 'payment_instruments';

    const TYPE_CHEQUE = 'cheque';
    const TYPE_BANK_TRANSFER = 'bank_transfer';
    const TYPE_WALLET_TRANSFER = 'wallet_transfer';

    protected $fillable = [
        'instrument_number',
        'instrument_type',
        'source_cash_account_id',
        'amount',
        'currency',
        'beneficiary_type',
        'beneficiary_id',
        'beneficiary_name',
        'status',
        'issued_on',
        'cleared_on',
        'external_reference',
        'fund_id',
        'project_id',
        'expense_account_id',
        'ledger_entry_id',
        'created_by',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_on' => 'date',
        'cleared_on' => 'date',
    ];

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'source_cash_account_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}

