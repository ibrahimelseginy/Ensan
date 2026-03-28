<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class WebDonation extends Model
{
    protected $fillable = [
        'web_donor_id',
        'donor_id',
        'amount',
        'payment_method',
        'status',
        'category',
        'target_id',
        'donationable_type',
        'donationable_id',
        'campaign_id',
        'project_id',
        'allocation_note',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'float',
        'metadata' => 'array',
        'target_id' => 'integer',
        'donationable_id' => 'integer',
    ];

    public function webDonor(): BelongsTo
    {
        return $this->belongsTo(WebDonor::class, 'web_donor_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function donationable(): MorphTo
    {
        return $this->morphTo();
    }

    public function proof()
    {
        return $this->hasOne(DonationProof::class, 'web_donation_id');
    }

    // Reuse existing accessors for consistency
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'instapay' => 'انستا باي (Instapay)',
            'bank_transfer' => 'تحويل بنكي',
            'vodafone_cash' => 'فودافون كاش',
            'vodafone' => 'فودافون كاش',
            'fawry' => 'فوري',
            'cash' => 'نقدي',
            'card' => 'بطاقة ائتمان',
            'representative' => 'مندوب',
            'other' => 'أخرى'
        ];

        return $labels[$this->payment_method] ?? ($this->payment_method ?: 'غير محدد');
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'campaign' => 'حملة تبرع',
            'project' => 'مشروع تنموي',
            'general' => 'صدقة عامة',
            'sadaqa' => 'صدقة',
            'kafala' => 'كفالة',
            'zakat' => 'زكاة',
        ];

        return $labels[$this->category] ?? ($this->category ?: 'عام');
    }
}
