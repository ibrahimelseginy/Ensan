<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmployeeEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'evaluator_id',
        'evaluation_period',
        'evaluation_date',
        'tasks_achievement',
        'target',
        'work_quality',
        'punctuality',
        'attendance_discipline',
        'instructions_compliance',
        'problem_solving',
        'teamwork',
        'communication',
        'development_ideas',
        'total_score',
        'percentage',
        'rating_label',
        'notes',
        'strengths',
        'improvements',
    ];

    protected $casts = [
        'evaluation_date'         => 'date',
        'tasks_achievement'       => 'float',
        'target'                  => 'float',
        'work_quality'            => 'float',
        'punctuality'             => 'float',
        'attendance_discipline'   => 'float',
        'instructions_compliance' => 'float',
        'problem_solving'         => 'float',
        'teamwork'                => 'float',
        'communication'           => 'float',
        'development_ideas'       => 'float',
        'total_score'             => 'float',
        'percentage'              => 'float',
    ];

    /**
     * Criteria weights according to official HR performance template
     */
    public const CRITERIA = [
        'tasks_achievement' => [
            'label'  => 'إنجاز المهام',
            'weight' => 0.25,
            'desc'   => 'دقة وسرعة إتمام المهام المطلوبة',
        ],
        'target' => [
            'label'  => 'التارجيت',
            'weight' => 0.15,
            'desc'   => 'تحقيق الأهداف والمستهدفات الشهرية',
        ],
        'work_quality' => [
            'label'  => 'جودة الشغل',
            'weight' => 0.10,
            'desc'   => 'الإتقان وخلو العمل من الأخطاء',
        ],
        'punctuality' => [
            'label'  => 'الالتزام بالمواعيد',
            'weight' => 0.10,
            'desc'   => 'تسليم الأعمال والالتزام بمواعيد الحضور والانصراف',
        ],
        'attendance_discipline' => [
            'label'  => 'الحضور والانضباط',
            'weight' => 0.20,
            'desc'   => 'الانتظام في العمل وعدم الغياب والالتزام باللوائح',
        ],
        'instructions_compliance' => [
            'label'  => 'الالتزام بالتعليمات',
            'weight' => 0.05,
            'desc'   => 'اتباع سياسات الإدارة وتوجيهات المشرفين',
        ],
        'problem_solving' => [
            'label'  => 'حل المشكلات',
            'weight' => 0.05,
            'desc'   => 'المرونة والقدرة على التعامل مع العقبات وتجاوزها',
        ],
        'teamwork' => [
            'label'  => 'التعاون',
            'weight' => 0.05,
            'desc'   => 'مساعدة الزملاء وروح الفريق',
        ],
        'communication' => [
            'label'  => 'التواصل',
            'weight' => 0.05,
            'desc'   => 'وضوح التواصل مع الإدارة والزملاء والعملاء',
        ],
        'development_ideas' => [
            'label'  => 'التطوير و الافكار',
            'weight' => 0.05,
            'desc'   => 'المبادرة واقتراح حلول وأفكار جديدة مفيدة للمؤسسة',
        ],
    ];

    public const MAX_ITEM_SCORE = 5.0;
    public const MIN_ITEM_SCORE = 0.5;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Compute total score out of 100, percentage, and rating label.
     */
    public function calculateResults(): array
    {
        $weightedSum = 0.0;
        foreach (self::CRITERIA as $key => $meta) {
            $score = (float) ($this->{$key} ?? 0);
            // Bound score between 0 and 5
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

