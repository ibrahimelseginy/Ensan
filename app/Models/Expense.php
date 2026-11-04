<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Expense extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'type',
        'category',
        'amount',
        'currency',
        'payment_method',
        'description',
        'project_id',
        'campaign_id',
        'guest_house_id',
        'workspace_id',
        'beneficiary_id',
        'treasury_id',
        'created_by',
        'paid_at',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'attachment_path'
    ];

    protected $casts = [
        'paid_at' => 'date',
        'cancelled_at' => 'datetime'
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function guestHouse(): BelongsTo { return $this->belongsTo(GuestHouse::class); }
    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function beneficiary(): BelongsTo { return $this->belongsTo(Beneficiary::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function treasury(): BelongsTo { return $this->belongsTo(Treasury::class); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }

    public function getExpenseDateAttribute(): ?string
    {
        if ($this->paid_at) {
            return $this->paid_at->format('Y-m-d');
        }
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->description;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->description;
    }

    public function getReceiptAttribute(): ?string
    {
        return $this->attachment_path;
    }
}

