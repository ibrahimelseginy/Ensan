<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_evaluations')) {
            Schema::create('employee_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('evaluation_period', 50)->nullable(); // e.g. "2026-11" or "الربع الرابع 2026"
                $table->date('evaluation_date')->nullable();
                
                // 10 Criteria from Excel (Scores from 0.5 to 5.0)
                $table->decimal('tasks_achievement', 4, 2)->default(0);       // وزن 0.25 (إنجاز المهام)
                $table->decimal('target', 4, 2)->default(0);                  // وزن 0.15 (التارجيت)
                $table->decimal('work_quality', 4, 2)->default(0);            // وزن 0.10 (جودة الشغل)
                $table->decimal('punctuality', 4, 2)->default(0);             // وزن 0.10 (الالتزام بالمواعيد)
                $table->decimal('attendance_discipline', 4, 2)->default(0);   // وزن 0.20 (الحضور والانضباط)
                $table->decimal('instructions_compliance', 4, 2)->default(0); // وزن 0.05 (الالتزام بالتعليمات)
                $table->decimal('problem_solving', 4, 2)->default(0);         // وزن 0.05 (حل المشكلات)
                $table->decimal('teamwork', 4, 2)->default(0);                // وزن 0.05 (التعاون)
                $table->decimal('communication', 4, 2)->default(0);           // وزن 0.05 (التواصل)
                $table->decimal('development_ideas', 4, 2)->default(0);       // وزن 0.05 (التطوير و الافكار)
                
                // Results
                $table->decimal('total_score', 5, 2)->default(0);             // المجموع من 100
                $table->decimal('percentage', 5, 2)->default(0);              // النسبة المئوية
                $table->string('rating_label', 50)->nullable();               // متميز، جيد جداً، جيد، مقبول، ضعيف
                
                // Qualitative notes
                $table->text('notes')->nullable();
                $table->text('strengths')->nullable();
                $table->text('improvements')->nullable();

                $table->timestamps();

                $table->index(['user_id', 'evaluation_date']);
                $table->index(['user_id', 'evaluation_period']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_evaluations');
    }
};
