<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'source',
        'user_id',
        'category',
        'target_id',
        'donationable_type',
        'donationable_id',
        'amount',
        'payment_method',
        'status',
        'is_flagged',
        'donor_id',
        'type',
        'cash_channel',
        'currency',
        'receipt_number',
        'estimated_value',
        'project_id',
        'campaign_id',
        'guest_house_id',
        'warehouse_id',
        'treasury_id',
        'item_id',
        'quantity',
        'delegate_id',
        'route_id',
        'allocation_note',
        'received_at',
        'created_by',
        'auto_added_to_inventory',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_added_to_inventory' => 'boolean',
        'is_flagged' => 'boolean',
        'amount' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'quantity' => 'decimal:2'
    ];

    public function donationable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function webDonor()
    {
        return $this->belongsTo(WebDonor::class, 'web_donor_id');
    }

    public function donor(): BelongsTo 
    { 
        return $this->belongsTo(Donor::class); 
    }

    public function project(): BelongsTo 
    { 
        return $this->belongsTo(Project::class); 
    }

    public function campaign(): BelongsTo 
    { 
        return $this->belongsTo(Campaign::class); 
    }

    public function guestHouse(): BelongsTo 
    { 
        return $this->belongsTo(GuestHouse::class, 'guest_house_id'); 
    }

    public function warehouse(): BelongsTo 
    { 
        return $this->belongsTo(Warehouse::class); 
    }

    public function treasury(): BelongsTo 
    { 
        return $this->belongsTo(Treasury::class); 
    }

    public function item(): BelongsTo 
    { 
        return $this->belongsTo(Item::class); 
    }

    public function delegate(): BelongsTo 
    { 
        return $this->belongsTo(Delegate::class); 
    }

    public function route(): BelongsTo 
    { 
        return $this->belongsTo(TravelRoute::class, 'route_id'); 
    }

    public function createdBy(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'created_by'); 
    }

    public function cancelledBy(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'cancelled_by'); 
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Get donation value
    public function getValue()
    {
        if ($this->type === 'cash') {
            return $this->amount ?? 0;
        }
        return $this->estimated_value ?? 0;
    }

    // Check if donation is cash
    public function isCash()
    {
        return $this->type === 'cash';
    }

    // Check if donation is in-kind
    public function isInKind()
    {
        return $this->type === 'in_kind';
    }

    // Check if donation is cancelled
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    // Check if donation is active
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function proof()
    {
        return $this->hasOne(DonationProof::class);
    }

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
