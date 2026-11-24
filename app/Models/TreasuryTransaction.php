<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TreasuryTransaction extends Model
{
    protected $fillable = [
        'treasury_id',
        'type',
        'amount',
        'currency',
        'description',
        'reference',
        'transaction_date',
        'created_by',
        'donation_id',
        'expense_id',
        'payroll_id',
        'journal_entry_id',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    // Get type in Arabic
    public function getTypeArabic()
    {
        return $this->type === 'in' ? 'وارد' : 'صادر';
    }

    /**
     * اسم الموظف الذي قام بتسجيل التبرع أو الحركة المالية
     */
    public function getRegistrarNameAttribute(): string
    {
        return $this->createdBy?->name ?: ($this->donation?->user?->name ?: 'النظام');
    }

    /**
     * صفة أو دور الموظف المسجل (استقبال / دار ضيافة / حسابات...)
     */
    public function getRegistrarTitleAttribute(): ?string
    {
        $creator = $this->createdBy ?? $this->donation?->user;
        if (!$creator) {
            return null;
        }

        if ($creator->relationLoaded('roles') && $creator->roles->isNotEmpty()) {
            return $creator->roles->first()?->name;
        }

        return $creator->job_title ?: ($creator->department ?: null);
    }

    /**
     * المصدر أو الجهة التي جاء منها التبرع أو الحركة (الاستقبال / دار الضيافة / المقر / تحويل)
     */
    public function getSourceLabelAttribute(): string
    {
        if ($this->donation) {
            if ($this->donation->guestHouse) {
                return 'دار ضيافة: ' . $this->donation->guestHouse->name;
            }

            $creator = $this->createdBy ?? $this->donation->user;
            if ($creator) {
                $job = strtolower((string) ($creator->job_title ?? ''));
                $dept = strtolower((string) ($creator->department ?? ''));
                $roles = $creator->relationLoaded('roles') ? $creator->roles->pluck('name')->toArray() : [];

                if (str_contains($job, 'reception') || str_contains($dept, 'استقبال') || in_array('الاستقبال', $roles, true)) {
                    return 'قسم الاستقبال';
                }

                foreach ($roles as $rName) {
                    if (str_contains($rName, 'دار ضيافة')) {
                        return $rName;
                    }
                }
            }

            if ($this->donation->project) {
                return 'مشروع: ' . $this->donation->project->name;
            }

            return 'قسم الاستقبال (تبرع مباشر)';
        }

        if (str_contains((string) $this->description, 'تحويل وتوريد')) {
            return 'تحويل بين الخزائن';
        }

        $creator = $this->createdBy;
        if ($creator) {
            $job = strtolower((string) ($creator->job_title ?? ''));
            $dept = strtolower((string) ($creator->department ?? ''));
            $roles = $creator->relationLoaded('roles') ? $creator->roles->pluck('name')->toArray() : [];

            if (str_contains($job, 'reception') || str_contains($dept, 'استقبال') || in_array('الاستقبال', $roles, true)) {
                return 'قسم الاستقبال';
            }

            foreach ($roles as $rName) {
                if (str_contains($rName, 'دار ضيافة')) {
                    return $rName;
                }
            }

            if ($creator->department) {
                return $creator->department;
            }
        }

        return 'الإدارة المالية';
    }
}
