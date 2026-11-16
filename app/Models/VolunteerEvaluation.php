<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VolunteerEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'evaluator_id',
        'evaluation_period',
        'evaluation_date',
        'tasks_achievement',
        'target',
        'punctuality',
        'attendance_discipline',
        'instructions_compliance',
        'initiative_volunteering',
        'teamwork',
        'communication_interaction',
        'ideas',
        'total_score',
        'percentage',
        'rating_label',
        'notes',
        'strengths',
        'improvements',
    ];

    protected $casts = [
        'evaluation_date'           => 'date',
        'tasks_achievement'         => 'float',
        'target'                    => 'float',
        'punctuality'               => 'float',
        'attendance_discipline'     => 'float',
        'instructions_compliance'   => 'float',
        'initiative_volunteering'   => 'float',
        'teamwork'                  => 'float',
        'communication_interaction' => 'float',
        'ideas'                     => 'float',
        'total_score'               => 'float',
        'percentage'                => 'float',
    ];

    /**
     * 9 Criteria weights according to official Volunteer HR Evaluation Template (Excel)
     */
    public const CRITERIA = [
        'tasks_achievement' => [
            'label'  => 'إنجاز المهام',
            'weight' => 0.20,
            'desc'   => 'دقة وسرعة إتمام المهام التطوعية المكلف بها',
        ],
        'target' => [
            'label'  => 'التارجيت',
            'weight' => 0.15,
            'desc'   => 'تحقيق المستهدفات المطلوبة للنشاط أو الفعالية',
        ],
        'punctuality' => [
            'label'  => 'الالتزام بالمواعيد',
            'weight' => 0.05,
            'desc'   => 'الحضور في الوقت المحدد لبدء الفعالية أو النشاط',
        ],
        'attendance_discipline' => [
            'label'  => 'الحضور والانضباط',
            'weight' => 0.10,
            'desc'   => 'الانتظام في المشاركات وعدم التغيب بدون إذن مسبق',
        ],
        'instructions_compliance' => [
            'label'  => 'الالتزام بالتعليمات',
            'weight' => 0.05,
            'desc'   => 'اتباع إرشادات وتوجيهات مشرف ومسؤول اليوم',
        ],
        'initiative_volunteering' => [
            'label'  => 'المبادرة و التطوع',
            'weight' => 0.10,
            'desc'   => 'روح المبادرة وتقديم المساعدة الإضافية دون طلب',
        ],
        'teamwork' => [
            'label'  => 'التعاون',
            'weight' => 0.10,
            'desc'   => 'مساعدة باقي المتطوعين والعمل بروح الفريق الواحد',
        ],
        'communication_interaction' => [
            'label'  => 'التواصل و التفاعل',
            'weight' => 0.20,
            'desc'   => 'التفاعل الإيجابي مع المستفيدين والإدارة والزملاء',
        ],
        'ideas' => [
            'label'  => 'افكار',
            'weight' => 0.05,
            'desc'   => 'طرح أفكار تطويرية واقتراحات لتحسين العمل الميداني',
        ],
    ];

    public const MAX_ITEM_SCORE = 5.0;
    public const MIN_ITEM_SCORE = 0.5;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id')->withTrashed();
    }

    /**
     * Compute total score out of 100, percentage, and rating label.
     */
    public function calculateResults(): array
    {
        $weightedSum = 0.0;
        foreach (self::CRITERIA as $key => $meta) {
            $score = (float) ($this->{$key} ?? 0);
            $score = max(0.0, min(self::MAX_ITEM_SCORE, $score));
            $weightedSum += ($score * $meta['weight']);
        }

        // Total score out of 100: ($weightedSum / 5.0) * 100 = $weightedSum * 20
        $totalScore = round($weightedSum * 20, 2);
        $percentage = $totalScore;
        $ratingLabel = self::determineRatingLabel($percentage);

        $this->total_score = $totalScore;
        $this->percentage = $percentage;
        $this->rating_label = $ratingLabel;

        return [
            'total_score'  => $totalScore,
            'percentage'   => $percentage,
            'rating_label' => $ratingLabel,
        ];
    }

    /**
     * Determine rating classification based on Excel guidelines:
     * 90% فأكثر: متميز
     * 80% - 89%: جيد جداً
     * 65% - 79%: جيد
     * 50% - 64%: مقبول
     * أقل من 50%: ضعيف
     */
    public static function determineRatingLabel(float $percentage): string
    {
        if ($percentage >= 90.0) {
            return 'متميز';
        }
        if ($percentage >= 80.0) {
            return 'جيد جداً';
        }
        if ($percentage >= 65.0) {
            return 'جيد';
        }
        if ($percentage >= 50.0) {
            return 'مقبول';
        }
        return 'ضعيف';
    }

    /**
     * Badge CSS class for rating
     */
    public function getRatingBadgeClass(): string
    {
        return match ($this->rating_label) {
            'متميز'    => 'bg-success text-white',
            'جيد جداً' => 'bg-primary text-white',
            'جيد'      => 'bg-info text-dark',
            'مقبول'    => 'bg-warning text-dark',
            'ضعيف'     => 'bg-danger text-white',
            default    => 'bg-secondary text-white',
        };
    }
}

