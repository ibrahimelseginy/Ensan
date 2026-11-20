<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

final class CashTransfer extends Model
{
    protected $fillable = ['transfer_number', 'from_cash_account_id', 'to_cash_account_id', 'amount', 'currency', 'requested_on', 'received_at', 'status', 'requested_by', 'received_by', 'approved_by', 'ledger_entry_id', 'document_id', 'reference', 'note'];
    protected $casts = ['requested_on' => 'date', 'received_at' => 'datetime', 'amount' => 'decimal:4'];
}
