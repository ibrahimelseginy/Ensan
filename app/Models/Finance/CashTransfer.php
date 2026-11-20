<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashTransfer extends Model
{
    protected $table = 'cash_transfers';

    protected $fillable = [
        'transfer_number',
        'from_cash_account_id',
        'to_cash_account_id',
        'amount',
        'currency',
        'requested_on',
        'received_at',
        'status',
        'requested_by',
        'received_by',
        'approved_by',
        'ledger_entry_id',
        'document_id',
        'reference',
        'note',
    ];

    protected $casts = [
        'requested_on' => 'date',
        'received_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'from_cash_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'to_cash_account_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by')->withTrashed();
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by')->withTrashed();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }
}
