<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PsychologicalSupportReport extends Model
{
    protected $fillable = [
        'beneficiary_id',
        'family_member_id',
        'user_id',
        'volunteer_name',
        'report_month',
        'medical_condition',
        'treatment_stage',
        'call_condition',
        'psychological_state',
        'rating',
        'needs_improvement',
        'nutrition_diet',
        'general_notes',
        'supporter_opinion',
        'created_by',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryFamilyMember::class, 'family_member_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRatingStarsAttribute(): string
    {
        $stars = max(1, min(5, (int) $this->rating));
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
    }
}
