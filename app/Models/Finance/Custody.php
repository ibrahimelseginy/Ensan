<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Treasury;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Custody extends Model
{
    protected $table = 'custodies';

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_UNDER_SETTLEMENT = 'under_settlement';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_NEEDS_DOCS = 'needs_docs';
    const STATUS_SETTLED_PENDING_RETURN = 'pending_return';
    const STATUS_CLOSED = 'closed';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_DRAFT => ['label' => 'مسودة', 'class' => 'bg-secondary'],
        self::STATUS_APPROVED => ['label' => 'معتمدة وجاهزة للصرف', 'class' => 'bg-info'],
        self::STATUS_DELIVERED => ['label' => 'تم التسليم للموظف', 'class' => 'bg-primary'],
        self::STATUS_UNDER_SETTLEMENT => ['label' => 'تحت التسوية', 'class' => 'bg-warning text-dark'],
        self::STATUS_PENDING_REVIEW => ['label' => 'مقدمة للمراجعة', 'class' => 'bg-primary'],
        self::STATUS_NEEDS_DOCS => ['label' => 'تحتاج استكمال مستندات', 'class' => 'bg-danger'],
        self::STATUS_SETTLED_PENDING_RETURN => ['label' => 'في انتظار رد المتبقي', 'class' => 'bg-warning text-dark'],
        self::STATUS_CLOSED => ['label' => 'مغلقة ومسواة بالكامل', 'class' => 'bg-success'],
        self::STATUS_REJECTED => ['label' => 'مرفوضة', 'class' => 'bg-danger'],
    ];

    protected $fillable = [
        'custody_number',
        'holder_user_id',
        'source_cash_account_id',
        'custody_account_id',
        'issued_amount',
        'settled_amount',
        'returned_amount',
        'currency',
        'fund_id',
        'project_id',
        'issued_on',
        'due_on',
        'status',
        'issue_entry_id',
        'closed_by',
        'closed_at',
        'purpose',
    ];

    protected $casts = [
        'issued_amount' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'returned_amount' => 'decimal:2',
        'issued_on' => 'date',
        'due_on' => 'date',
        'closed_at' => 'datetime',
    ];

    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'holder_user_id')->withTrashed();
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'source_cash_account_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by')->withTrashed();
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CustodyExpense::class, 'custody_id')->orderBy('expense_date');
    }

    public function getRemainingToReturnAttribute(): float
    {
        $remaining = (float) $this->issued_amount - (float) $this->settled_amount - (float) $this->returned_amount;
        return max(0.0, round($remaining, 2));
    }

    public function getEmployeeDuesAttribute(): float
    {
        $diff = (float) $this->settled_amount - (float) $this->issued_amount;
        return max(0.0, round($diff, 2));
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUSES[$this->status] ?? ['label' => $this->status, 'class' => 'bg-secondary'];
    }
}

