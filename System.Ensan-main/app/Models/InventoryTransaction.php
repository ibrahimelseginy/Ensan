<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventoryTransaction extends Model
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'type',
        'quantity',
        'unit_cost',
        'total_cost',
        'source_donation_id',
        'beneficiary_id',
        'project_id',
        'campaign_id',
        'expense_id',
        'delegate_id',
        'user_id',
        'journal_entry_id',
        'reference',
        'notes',
        'related_transaction_id',
        'transaction_date',
        'status',
        'approved_by',
        'approved_at',
        'batch_number',
        'expiry_date',
        'location_in_warehouse'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
        'expiry_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity' => 'decimal:2'
    ];

    // Relationships
    public function relatedTransaction(): BelongsTo 
    { 
        return $this->belongsTo(InventoryTransaction::class, 'related_transaction_id'); 
    }

    public function item(): BelongsTo 
    { 
        return $this->belongsTo(Item::class); 
    }

    public function warehouse(): BelongsTo 
    { 
        return $this->belongsTo(Warehouse::class); 
    }

    public function sourceDonation(): BelongsTo 
    { 
        return $this->belongsTo(Donation::class, 'source_donation_id'); 
    }

    public function beneficiary(): BelongsTo 
    { 
        return $this->belongsTo(Beneficiary::class); 
    }

    public function project(): BelongsTo 
    { 
        return $this->belongsTo(Project::class); 
    }

    public function campaign(): BelongsTo 
    { 
        return $this->belongsTo(Campaign::class); 
    }

    public function expense(): BelongsTo 
    { 
        return $this->belongsTo(Expense::class); 
    }

    public function delegate(): BelongsTo 
    { 
        return $this->belongsTo(Delegate::class); 
    }

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function journalEntry(): BelongsTo 
    { 
        return $this->belongsTo(JournalEntry::class); 
    }

    public function approvedBy(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'approved_by'); 
    }

    // Calculate total cost
    public function calculateTotalCost()
    {
        if ($this->unit_cost && $this->quantity) {
            $this->total_cost = $this->unit_cost * $this->quantity;
            return $this->total_cost;
        }
        return 0;
    }

    // Check if transaction is approved
    public function isApproved()
    {
        return $this->status === 'approved' && $this->approved_at !== null;
    }

    // Check if item is expired
    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date < now();
    }

    // Get transaction type in Arabic
    public function getTypeArabic()
    {
        $types = [
            'in' => 'وارد',
            'out' => 'صادر',
            'transfer' => 'تحويل',
            'adjustment' => 'تسوية',
            'damage' => 'تالف',
            'return' => 'مرتجع'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Get status in Arabic
    public function getStatusArabic()
    {
        $statuses = [
            'pending' => 'معلق',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
