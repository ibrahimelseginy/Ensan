<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegateTrip extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegate_id',
        'date',
        'description',
        'cost',
        'status',
        'journal_entry_id',
        'payment_method',
        'notes',
        'from_location',
        'to_location',
        'distance_km',
        'fuel_cost',
        'other_expenses'
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
        'fuel_cost' => 'decimal:2',
        'other_expenses' => 'decimal:2',
        'distance_km' => 'decimal:2'
    ];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }
    
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
    
    // Calculate total cost
    public function calculateTotalCost()
    {
        $total = $this->cost ?? 0;
        $total += $this->fuel_cost ?? 0;
        $total += $this->other_expenses ?? 0;
        return $total;
    }
}
