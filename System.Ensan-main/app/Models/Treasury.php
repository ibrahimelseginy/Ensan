<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Treasury extends Model
{
    protected $table = 'treasuries';

    /**
     * Treasury Types
     */
    const TYPE_MAIN = 'main';
    const TYPE_BRANCH = 'branch';
    const TYPE_DELEGATE = 'delegate';
    const TYPE_PROJECT = 'project';
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_PETTY_CASH = 'petty_cash';

    const TYPES = [
        self::TYPE_MAIN => 'الخزينة الرئيسية',
        self::TYPE_BRANCH => 'خزينة فرع',
        self::TYPE_DELEGATE => 'عهدة مندوب',
        self::TYPE_PROJECT => 'خزينة مشروع',
        self::TYPE_CAMPAIGN => 'خزينة حملة',
        self::TYPE_PETTY_CASH => 'صندوق مصروفات نثرية'
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'manager_id',
        'location',
        'currency',
        'opening_balance',
        'current_balance',
        'is_active',
        'project_id',
        'campaign_id',
        'delegate_id',
        'branch_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(TreasuryTransaction::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // ==========================================
    // TYPE HELPERS
    // ==========================================

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function isMain()
    {
        return $this->type === self::TYPE_MAIN || str_starts_with($this->code, 'MAIN');
    }

    public function isBranch()
    {
        return $this->type === self::TYPE_BRANCH || str_starts_with($this->code, 'BRANCH');
    }

    public function isDelegate()
    {
        return $this->type === self::TYPE_DELEGATE || str_starts_with($this->code, 'DEL');
    }

    public function isProject()
    {
        return $this->type === self::TYPE_PROJECT || str_starts_with($this->code, 'PROJ');
    }

    public function isCampaign()
    {
        return $this->type === self::TYPE_CAMPAIGN || str_starts_with($this->code, 'CAMP');
    }

    public function isPettyCash()
    {
        return $this->type === self::TYPE_PETTY_CASH || str_starts_with($this->code, 'PETTY');
    }

    // ==========================================
    // BALANCE METHODS
    // ==========================================

    public function getTotalDonations()
    {
        return $this->donations()->where('type', 'cash')->sum('amount');
    }

    public function getTotalIn()
    {
        return $this->transactions()->where('type', 'in')->sum('amount');
    }

    public function getTotalOut()
    {
        return $this->transactions()->where('type', 'out')->sum('amount');
    }

    public function updateBalance()
    {
        $totalIn = $this->getTotalIn();
        $totalOut = $this->getTotalOut();
        
        $this->current_balance = $this->opening_balance + $totalIn - $totalOut;
        $this->save();
        
        return $this->current_balance;
    }

    public function hasEnoughBalance($amount)
    {
        return $this->current_balance >= $amount;
    }

    // ==========================================
    // TRANSACTION METHODS
    // ==========================================

    public function addTransaction($type, $amount, $description, $reference = null, $extraData = [])
    {
        $data = array_merge([
            'type' => $type,
            'amount' => $amount,
            'currency' => $this->currency,
            'description' => $description,
            'reference' => $reference,
            'transaction_date' => now(),
            'created_by' => auth()->id()
        ], $extraData);

        $transaction = $this->transactions()->create($data);
        $this->updateBalance();

        return $transaction;
    }

    public function addIncome($amount, $description, $reference = null, $extraData = [])
    {
        return $this->addTransaction('in', $amount, $description, $reference, $extraData);
    }

    public function addExpense($amount, $description, $reference = null, $extraData = [])
    {
        if (!$this->hasEnoughBalance($amount)) {
            throw new \Exception("رصيد الخزينة غير كافي. المتاح: {$this->current_balance}");
        }
        return $this->addTransaction('out', $amount, $description, $reference, $extraData);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMain($query)
    {
        return $query->where(function($q) {
            $q->where('type', self::TYPE_MAIN)
                ->orWhere('code', 'like', 'MAIN%');
        });
    }

    public function scopeForDepartment($query, $department)
    {
        $departments = [
            'donations' => [self::TYPE_MAIN, self::TYPE_BRANCH, self::TYPE_DELEGATE, self::TYPE_CAMPAIGN],
            'expenses' => [self::TYPE_MAIN, self::TYPE_BRANCH, self::TYPE_PETTY_CASH, self::TYPE_PROJECT],
            'payroll' => [self::TYPE_MAIN],
            'projects' => [self::TYPE_MAIN, self::TYPE_PROJECT],
            'campaigns' => [self::TYPE_MAIN, self::TYPE_CAMPAIGN]
        ];

        if (isset($departments[$department])) {
            return $query->whereIn('type', $departments[$department]);
        }

        return $query;
    }

    // ==========================================
    // STATIC HELPERS
    // ==========================================

    public static function getDefault($department = null)
    {
        $query = self::active();

        if ($department) {
            $query->forDepartment($department);
        }

        return $query->main()->first() ?? $query->first();
    }

    public static function getTypesList()
    {
        return self::TYPES;
    }
}
