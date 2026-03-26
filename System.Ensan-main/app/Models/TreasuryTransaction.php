<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryTransaction extends Model
{
    protected $fillable = [
        'treasury_id',
        'type',
        'amount',
        'currency',
        'description',
        'reference',
        'transaction_date',
        'created_by',
        'donation_id',
        'expense_id',
        'journal_entry_id'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    // Get type in Arabic
    public function getTypeArabic()
    {
        return $this->type === 'in' ? 'وارد' : 'صادر';
    }
}
