<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'amount',
        'currency',
        'paid_at',
        'journal_entry_id',
        'status',
        'notes',
        'deductions',
        'bonuses',
        'net_amount'
    ];
    
    protected $casts = [
        'paid_at' => 'date',
        'deductions' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'net_amount' => 'decimal:2'
    ];
    
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function journalEntry(): BelongsTo 
    { 
        return $this->belongsTo(JournalEntry::class); 
    }
    
    // Calculate net amount
    public function calculateNetAmount()
    {
        $this->net_amount = $this->amount + ($this->bonuses ?? 0) - ($this->deductions ?? 0);
        return $this->net_amount;
    }
}
