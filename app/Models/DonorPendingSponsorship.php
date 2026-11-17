<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorPendingSponsorship extends Model
{
    protected $fillable = [
        'donor_id',
        'type',
        'monthly_amount',
        'target_notes',
        'status',
        'linked_family_member_id',
        'linked_beneficiary_id',
        'linked_at',
        'linked_by_user_id',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
        'linked_at' => 'datetime',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function linkedFamilyMember(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryFamilyMember::class, 'linked_family_member_id');
    }

    public function linkedBeneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'linked_beneficiary_id');
    }

    public function linkedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_by_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'family' ? 'كفالة أسرة كاملة' : 'كفالة طفل / يتيم';
    }
}
