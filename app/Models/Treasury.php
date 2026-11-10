<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Treasury extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $table = 'treasuries';

    /**
     * Treasury Types
     */
    const TYPE_MAIN = 'main';
    const TYPE_RECEPTION = 'reception';
    const TYPE_GUEST_HOUSE = 'guest_house';
    const TYPE_BRANCH = 'branch';
    const TYPE_DELEGATE = 'delegate';
    const TYPE_PROJECT = 'project';
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_PETTY_CASH = 'petty_cash';

    const TYPES = [
        self::TYPE_MAIN => 'الخزينة العامة / الحسابات',
        self::TYPE_RECEPTION => 'خزينة الاستقبال',
        self::TYPE_GUEST_HOUSE => 'خزينة دار ضيافة',
        self::TYPE_PROJECT => 'خزينة مشروع',
        self::TYPE_CAMPAIGN => 'خزينة حملة',
        self::TYPE_DELEGATE => 'عهدة مندوب',
        self::TYPE_BRANCH => 'خزينة فرع',
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
        'guest_house_id',
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

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class, 'guest_house_id');
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
        return $this->transactions()
            ->where('type', 'in')
            ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'cancelled'))
            ->sum('amount');
    }

    public function getTotalOut()
    {
        return $this->transactions()
            ->where('type', 'out')
            ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'cancelled'))
            ->sum('amount');
    }

    /**
     * تفصيل رصيد الخزينة إلى: (رصيد العهدة / التغذية) مقابل (رصيد التبرعات)
     * مع مراعاة التسلسل الزمني للحركات وأي تصفير سابق للخزينة، وخصم المصروفات من العهدة أولاً.
     */
    public function getBalanceBreakdownAttribute(): array
    {
        $allTransactions = $this->relationLoaded('transactions')
            ? $this->transactions
                ->filter(fn ($tx) => ($tx->status ?? 'active') !== 'cancelled')
                ->sortBy('id')
                ->values()
            : $this->transactions()
                ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'cancelled'))
                ->orderBy('id')
                ->get(['id', 'type', 'amount', 'donation_id', 'expense_id', 'description', 'reference', 'status']);

        $openingRaw = (float) $this->opening_balance;
        $negativeOffset = $openingRaw < 0.0 ? abs($openingRaw) : 0.0;

        $custodyBalance = $openingRaw > 0.0 ? $openingRaw : 0.0;
        $donationsBalance = 0.0;

        $custodyIn = $custodyBalance;
        $donationsIn = 0.0;
        $totalOut = 0.0;

        $cumulativeRawNet = 0.0;

        foreach ($allTransactions as $tx) {
            $amt = (float) ($tx->amount ?? 0);
            if ($amt <= 0.0) {
                continue;
            }

            $type = (string) ($tx->type ?? 'in');
            $desc = (string) ($tx->description ?? '');
            $ref  = (string) ($tx->reference ?? '');

            if ($type === 'in') {
                $cumulativeRawNet += $amt;
                $isDonationTx = !empty($tx->donation_id)
                    || str_contains($desc, 'تبرع')
                    || str_contains($desc, 'كفالة')
                    || str_contains($desc, 'صدقة')
                    || str_contains($desc, 'زكاة')
                    || str_starts_with($ref, 'DON-');

                if ($isDonationTx) {
                    $donationsIn += $amt;
                    $donationsBalance += $amt;
                } else {
                    $custodyIn += $amt;
                    $custodyBalance += $amt;
                }
            } else {
                $cumulativeRawNet -= $amt;
                $totalOut += $amt;

                if (str_contains($desc, 'تصفير رصيد الخزينة')) {
                    $custodyBalance = 0.0;
                    $donationsBalance = 0.0;
                    $custodyIn = 0.0;
                    $donationsIn = 0.0;
                    $totalOut = 0.0;
                    continue;
                }

                $isDonationReversal = !empty($tx->donation_id)
                    || str_contains($desc, 'إلغاء تبرع')
                    || str_contains($desc, 'تخفيض وارد');

                if ($isDonationReversal) {
                    $deductDon = min($donationsBalance, $amt);
                    $donationsBalance -= $deductDon;
                    $custodyBalance = max(0.0, $custodyBalance - ($amt - $deductDon));
                } else {
                    // المصروفات والصرف النثري تُخصم من العهدة أولاً، وما زاد يُخصم من التبرعات
                    $deductCust = min($custodyBalance, $amt);
                    $custodyBalance -= $deductCust;
                    $donationsBalance = max(0.0, $donationsBalance - ($amt - $deductCust));
                }
            }

            // إذا كان هناك تصفير سابق عبر opening_balance سالب يعادل صافي الحركات حتى هذه النقطة، نبدأ دورة الرصيد من الصفر بعدها
            if ($negativeOffset > 0.0 && abs($cumulativeRawNet - $negativeOffset) < 0.01) {
                $custodyBalance = 0.0;
                $donationsBalance = 0.0;
                $custodyIn = 0.0;
                $donationsIn = 0.0;
                $totalOut = 0.0;
                $negativeOffset = 0.0;
            }
        }

        // إذا بقي فرق سالب من الرصيد الافتتاحي لم يُطابق نقطة تصفير محددة، يُخصم من التبرعات التاريخية أولاً ثم العهدة
        if ($negativeOffset > 0.0) {
            $absorbDon = min($donationsBalance, $negativeOffset);
            $donationsBalance -= $absorbDon;
            $remOffset = $negativeOffset - $absorbDon;
            if ($remOffset > 0.0) {
                $custodyBalance = max(0.0, $custodyBalance - $remOffset);
            }
        }

        $current = max(0.0, (float) $this->current_balance);
        if ($current <= 0.0) {
            $custodyBalance = 0.0;
            $donationsBalance = 0.0;
        } else {
            // الحفاظ على أولوية رصيد العهدة الفعلي وعدم طمسه بالتبرعات التاريخية
            $custodyBalance = min($current, round($custodyBalance, 2));
            $donationsBalance = max(0.0, round($current - $custodyBalance, 2));
        }

        return [
            'custody_in' => round($custodyIn, 2),
            'donations_in' => round($donationsIn, 2),
            'total_out' => round($totalOut, 2),
            'custody_balance' => $custodyBalance,
            'donations_balance' => $donationsBalance,
            'current_balance' => round($current, 2),
        ];
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

    /**
     * إرجاع الخزائن الخاصة بدار ضيافة محددة فقط (مع عزل تام عن خزائن الاستقبال والمشاريع والحملات ودور الضيافة الأخرى)
     */
    public static function forGuestHouse(\App\Models\GuestHouse $guestHouse): \Illuminate\Database\Eloquent\Collection
    {
        $hasGhCol = \Illuminate\Support\Facades\Schema::hasColumn('treasuries', 'guest_house_id');

        if ($hasGhCol) {
            $directMatched = self::active()
                ->where('guest_house_id', $guestHouse->id)
                ->orderBy('name')
                ->get();

            if ($directMatched->isNotEmpty()) {
                return $directMatched;
            }
        }

        $ghName = trim((string) $guestHouse->name);
        $ghLoc = trim((string) ($guestHouse->location ?? ''));

        $cityKeywords = [];
        foreach (['كفر الشيخ', 'كفرالشيخ', 'كفر', 'طنطا', 'الغربية', 'الغربيه', 'القاهرة', 'الإسكندرية', 'المنصورة'] as $kw) {
            if (str_contains($ghName, $kw) || str_contains($ghLoc, $kw)) {
                $cityKeywords[] = $kw;
                if ($kw === 'كفر الشيخ' || $kw === 'كفرالشيخ') {
                    $cityKeywords[] = 'كفر';
                }
                if ($kw === 'طنطا' || $kw === 'الغربية' || $kw === 'الغربيه') {
                    $cityKeywords[] = 'طنطا';
                    $cityKeywords[] = 'غربي';
                }
            }
        }
        $cityKeywords = array_values(array_unique($cityKeywords));

        $matched = self::active()
            ->whereNull('project_id')
            ->whereNull('campaign_id')
            ->when($hasGhCol, fn ($q) => $q->where(fn ($sub) => $sub->whereNull('guest_house_id')->orWhere('guest_house_id', $guestHouse->id)))
            ->where('type', '!=', self::TYPE_RECEPTION)
            ->where('type', '!=', self::TYPE_PROJECT)
            ->where('type', '!=', self::TYPE_CAMPAIGN)
            ->where('name', 'not like', '%استقبال%')
            ->where('code', 'not like', 'REC%')
            ->where('name', 'not like', '%مشروع%')
            ->where('name', 'not like', '%حملة%')
            ->where(function ($q) use ($guestHouse, $ghName, $cityKeywords) {
                $q->where('code', 'GH-' . $guestHouse->id)
                  ->orWhere('code', 'like', 'GH-' . $guestHouse->id . '-%');
                if ($ghName !== '') {
                    $q->orWhere('name', 'like', '%' . $ghName . '%');
                }
                foreach ($cityKeywords as $kw) {
                    $q->orWhere(function ($sub) use ($kw) {
                        $sub->where('name', 'like', '%' . $kw . '%')
                            ->where(function ($sub2) {
                                $sub2->where('name', 'like', '%ضياف%')
                                    ->orWhere('name', 'like', '%دار%')
                                    ->orWhere('type', self::TYPE_GUEST_HOUSE);
                            });
                    });
                }
            })
            ->orderBy('name')
            ->get();

        if ($matched->isEmpty()) {
            $code = 'GH-' . $guestHouse->id;
            $payload = [
                'name' => 'خزينة ' . ($ghName ?: ('دار الضيافة #' . $guestHouse->id)),
                'type' => self::TYPE_GUEST_HOUSE,
                'description' => 'خزينة وعهدة خاصة بـ ' . $ghName,
                'manager_id' => $guestHouse->manager_user_id,
                'location' => $ghLoc ?: $ghName,
                'currency' => 'EGP',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
            ];
            if ($hasGhCol) {
                $payload['guest_house_id'] = $guestHouse->id;
            }
            $created = self::firstOrCreate(['code' => $code], $payload);
            return new \Illuminate\Database\Eloquent\Collection([$created]);
        }

        return $matched;
    }

    /**
     * إرجاع خزينة الاستقبال فقط (مع عزل تام عن خزائن دور الضيافة والمشاريع والحملات)
     */
    public static function forReception(?\App\Models\User $user = null): \Illuminate\Database\Eloquent\Collection
    {
        $hasGhCol = \Illuminate\Support\Facades\Schema::hasColumn('treasuries', 'guest_house_id');

        $matched = self::active()
            ->whereNull('project_id')
            ->whereNull('campaign_id')
            ->when($hasGhCol, fn ($q) => $q->whereNull('guest_house_id'))
            ->where('type', '!=', self::TYPE_GUEST_HOUSE)
            ->where('type', '!=', self::TYPE_PROJECT)
            ->where('type', '!=', self::TYPE_CAMPAIGN)
            ->where('name', 'not like', '%ضياف%')
            ->where('name', 'not like', '%مشروع%')
            ->where('name', 'not like', '%حملة%')
            ->where(function ($q) {
                $q->where('type', self::TYPE_RECEPTION)
                  ->orWhere('name', 'like', '%استقبال%')
                  ->orWhere('name', 'like', '%الاستقبال%')
                  ->orWhere('code', 'like', 'REC%');
            })
            ->orderBy('id')
            ->get();

        if ($matched->isEmpty()) {
            $created = self::firstOrCreate(
                ['code' => 'REC-MAIN'],
                [
                    'name' => 'خزينة الاستقبال',
                    'type' => self::TYPE_RECEPTION,
                    'description' => 'خزينة وعهدة خاصة بمكتب الاستقبال (عهدة وتبرعات ومصروفات الاستقبال)',
                    'manager_id' => $user?->id,
                    'location' => 'مقر المؤسسة - الاستقبال',
                    'currency' => 'EGP',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                ]
            );
            return new \Illuminate\Database\Eloquent\Collection([$created]);
        }

        return $matched;
    }

    /**
     * إرجاع الخزينة الخاصة بمشروع محدد فقط (مع إنشائها تلقائياً إن لم تكن موجودة)
     */
    public static function forProject(\App\Models\Project $project): \Illuminate\Database\Eloquent\Collection
    {
        $hasGhCol = \Illuminate\Support\Facades\Schema::hasColumn('treasuries', 'guest_house_id');
        $projName = trim((string) $project->name);

        $matched = self::active()
            ->whereNull('campaign_id')
            ->when($hasGhCol, fn ($q) => $q->whereNull('guest_house_id'))
            ->where('type', '!=', self::TYPE_RECEPTION)
            ->where('type', '!=', self::TYPE_GUEST_HOUSE)
            ->where('type', '!=', self::TYPE_CAMPAIGN)
            ->where('name', 'not like', '%استقبال%')
            ->where('name', 'not like', '%ضياف%')
            ->where(function ($q) use ($project, $projName) {
                $q->where('project_id', $project->id)
                  ->orWhere('code', 'PROJ-' . $project->id);
                if ($projName !== '') {
                    $q->orWhere(function ($sub) use ($projName) {
                        $sub->whereNull('project_id')
                            ->where('name', 'like', '%' . $projName . '%');
                    });
                }
            })
            ->orderBy('name')
            ->get();

        if ($matched->isEmpty()) {
            $code = 'PROJ-' . $project->id;
            $created = self::firstOrCreate(
                ['code' => $code],
                [
                    'name' => 'خزينة ' . ($projName ?: ('مشروع #' . $project->id)),
                    'type' => self::TYPE_PROJECT,
                    'project_id' => $project->id,
                    'description' => 'خزينة خاصة بـ ' . $projName,
                    'manager_id' => $project->manager_user_id ?? null,
                    'currency' => 'EGP',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                ]
            );
            return new \Illuminate\Database\Eloquent\Collection([$created]);
        }

        return $matched;
    }

    /**
     * إرجاع الخزينة الخاصة بحملة محددة فقط (مع إنشائها تلقائياً إن لم تكن موجودة)
     */
    public static function forCampaign(\App\Models\Campaign $campaign): \Illuminate\Database\Eloquent\Collection
    {
        $hasGhCol = \Illuminate\Support\Facades\Schema::hasColumn('treasuries', 'guest_house_id');
        $campName = trim((string) $campaign->name);

        $matched = self::active()
            ->when($hasGhCol, fn ($q) => $q->whereNull('guest_house_id'))
            ->where('type', '!=', self::TYPE_RECEPTION)
            ->where('type', '!=', self::TYPE_GUEST_HOUSE)
            ->where('type', '!=', self::TYPE_PROJECT)
            ->where('name', 'not like', '%استقبال%')
            ->where('name', 'not like', '%ضياف%')
            ->where(function ($q) use ($campaign, $campName) {
                $q->where('campaign_id', $campaign->id)
                  ->orWhere('code', 'CAMP-' . $campaign->id);
                if ($campName !== '') {
                    $q->orWhere(function ($sub) use ($campName) {
                        $sub->whereNull('campaign_id')
                            ->where('name', 'like', '%' . $campName . '%');
                    });
                }
            })
            ->orderBy('name')
            ->get();

        if ($matched->isEmpty()) {
            $code = 'CAMP-' . $campaign->id;
            $created = self::firstOrCreate(
                ['code' => $code],
                [
                    'name' => 'خزينة ' . ($campName ?: ('حملة #' . $campaign->id)),
                    'type' => self::TYPE_CAMPAIGN,
                    'campaign_id' => $campaign->id,
                    'project_id' => $campaign->project_id ?? null,
                    'description' => 'خزينة خاصة بـ ' . $campName,
                    'currency' => 'EGP',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                ]
            );
            return new \Illuminate\Database\Eloquent\Collection([$created]);
        }

        return $matched;
    }

    /**
     * تصفية الخزائن المتاحة للموظف حسب دوره ومكانه (منع ظهور كل الخزائن لكل الموظفين)
     */
    public function scopeAccessibleForUser($query, ?\App\Models\User $user = null)
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $isGlobalFinanceOrAdmin = $user->email === 'admin@ensan.org'
            || $user->hasRole(['admin', 'super_admin', 'manager', 'finance', 'accountant', 'financial_manager', 'مدير النظام', 'الحسابات', 'المالية']);

        if ($isGlobalFinanceOrAdmin) {
            return $query;
        }

        $allowedIds = self::where('manager_id', $user->id)->pluck('id')->all();

        // Check if user is a Reception employee
        if ($user->hasRole(['reception', 'receptionist', 'موظف استقبال', 'الاستقبال']) || $user->hasPermission('reception.view')) {
            $recIds = self::forReception($user)->pluck('id')->all();
            $allowedIds = array_merge($allowedIds, $recIds);
        }

        // Check if user is assigned to specific Guest Houses / Projects
        $managedGh = \App\Models\GuestHouse::where('manager_user_id', $user->id)->get();
        foreach ($managedGh as $gh) {
            $allowedIds = array_merge($allowedIds, self::forGuestHouse($gh)->pluck('id')->all());
        }

        $allowedIds = array_values(array_unique(array_map('intval', $allowedIds)));

        if (!empty($allowedIds)) {
            return $query->whereIn('id', $allowedIds);
        }

        return $query->where('manager_id', $user->id);
    }
}
