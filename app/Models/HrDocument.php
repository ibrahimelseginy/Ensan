<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class HrDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'report_date',
        'project_id',
        'description',
        'file_path',
        'original_name',
        'mime',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'file_size'   => 'integer',
    ];

    public static function categoryLabels(): array
    {
        return [
            'sessions'    => 'تقارير السيشنز',
            'events'      => 'تقارير الفعاليات',
            'evaluations' => 'ملفات التقييمات والأداء',
            'policies'    => 'اللوائح والنماذج',
            'other'       => 'ملفات أخرى',
        ];
    }

    public static function categoryMeta(): array
    {
        return [
            'sessions' => [
                'label' => 'تقارير السيشنز',
                'icon'  => 'bi-easel2-fill',
                'color' => 'primary',
                'desc'  => 'تقارير الجلسات التدريبية وورش العمل',
            ],
            'events' => [
                'label' => 'تقارير الفعاليات',
                'icon'  => 'bi-calendar-event-fill',
                'color' => 'success',
                'desc'  => 'تقارير الفعاليات والأنشطة الميدانية',
            ],
            'evaluations' => [
                'label' => 'ملفات التقييمات والأداء',
                'icon'  => 'bi-clipboard2-data-fill',
                'color' => 'warning',
                'desc'  => 'كشوف وملفات تقييم الموظفين والمتطوعين',
            ],
            'policies' => [
                'label' => 'اللوائح والنماذج',
                'icon'  => 'bi-journal-bookmark-fill',
                'color' => 'info',
                'desc'  => 'السياسات الداخلية ونماذج الموارد البشرية',
            ],
            'other' => [
                'label' => 'ملفات أخرى',
                'icon'  => 'bi-folder2-open',
                'color' => 'secondary',
                'desc'  => 'مستندات وتقارير HR عامة',
            ],
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by')->withTrashed();
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo((string) ($this->original_name ?: $this->file_path), PATHINFO_EXTENSION));
    }

    public function getIsPreviewableAttribute(): bool
    {
        return in_array($this->extension, ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)
            || str_starts_with((string) $this->mime, 'image/')
            || $this->mime === 'application/pdf';
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) ($this->file_size ?? 0);
        if ($bytes <= 0) {
            return '—';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
