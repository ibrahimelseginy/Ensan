<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CustodyExpense extends Model
{
    protected $table = 'custody_expenses';

    protected $fillable = [
        'custody_id',
        'expense_date',
        'amount',
        'expense_account_id',
        'fund_id',
        'project_id',
        'campaign_id',
        'invoice_number',
        'merchant_name',
        'status',
        'approved_by',
        'ledger_entry_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function custody(): BelongsTo
    {
        return $this->belongsTo(Custody::class, 'custody_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }
}
