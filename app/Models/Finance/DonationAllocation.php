<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DonationAllocation extends Model
{
    protected $fillable = ['donation_id', 'line_no', 'amount', 'currency', 'fund_id', 'project_id', 'campaign_id', 'beneficiary_id', 'status', 'created_by', 'note'];
    protected $casts = ['amount' => 'decimal:4'];
    public function donation(): BelongsTo { return $this->belongsTo(Donation::class); }
    public function fund(): BelongsTo { return $this->belongsTo(Fund::class); }
}
