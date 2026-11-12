<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmployeeEvaluation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EmployeeEvaluationWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'                 => 'required|exists:users,id',
            'evaluation_period'       => 'nullable|string|max:50',
            'evaluation_date'         => 'required|date',
            'tasks_achievement'       => 'required|numeric|min:0.5|max:5',
            'target'                  => 'required|numeric|min:0.5|max:5',
            'work_quality'            => 'required|numeric|min:0.5|max:5',
            'punctuality'             => 'required|numeric|min:0.5|max:5',
            'attendance_discipline'   => 'required|numeric|min:0.5|max:5',
            'instructions_compliance' => 'required|numeric|min:0.5|max:5',
            'problem_solving'         => 'required|numeric|min:0.5|max:5',
            'teamwork'                => 'required|numeric|min:0.5|max:5',
            'communication'           => 'required|numeric|min:0.5|max:5',
            'development_ideas'       => 'required|numeric|min:0.5|max:5',
            'notes'                   => 'nullable|string|max:1000',
            'strengths'               => 'nullable|string|max:1000',
            'improvements'            => 'nullable|string|max:1000',
        ], [
            '*.min' => 'الحد الأدنى لدرجة كل بند هو 0.5',
            '*.max' => 'الحد الأقصى لدرجة كل بند هو 5.0',
            '*.required' => 'جميع بنود التقييم العشرة مطلوبة لحساب النتيجة بدقة.',
        ]);

        $evaluation = new EmployeeEvaluation($validated);
        $evaluation->evaluator_id = auth()->id();
        $evaluation->calculateResults();
        $evaluation->save();

        $user = User::find($validated['user_id']);

        return redirect()->back()->with('success', "تم تسجيل تقييم الموظف ({$user?->name}) بنجاح بنتيجة [{$evaluation->total_score} من 100] بتقدير [{$evaluation->rating_label}].");
    }

    public function update(Request $request, EmployeeEvaluation $evaluation): RedirectResponse
    {
        $validated = $request->validate([
            'evaluation_period'       => 'nullable|string|max:50',
            'evaluation_date'         => 'required|date',
            'tasks_achievement'       => 'required|numeric|min:0.5|max:5',
            'target'                  => 'required|numeric|min:0.5|max:5',
            'work_quality'            => 'required|numeric|min:0.5|max:5',
            'punctuality'             => 'required|numeric|min:0.5|max:5',
            'attendance_discipline'   => 'required|numeric|min:0.5|max:5',
            'instructions_compliance' => 'required|numeric|min:0.5|max:5',
            'problem_solving'         => 'required|numeric|min:0.5|max:5',
            'teamwork'                => 'required|numeric|min:0.5|max:5',
            'communication'           => 'required|numeric|min:0.5|max:5',
            'development_ideas'       => 'required|numeric|min:0.5|max:5',
            'notes'                   => 'nullable|string|max:1000',
            'strengths'               => 'nullable|string|max:1000',
            'improvements'            => 'nullable|string|max:1000',
        ]);

        $evaluation->fill($validated);
        $evaluation->calculateResults();
        $evaluation->save();

        return redirect()->back()->with('success', "تم تحديث تقييم الموظف بنجاح بنتيجة [{$evaluation->total_score} من 100] بتقدير [{$evaluation->rating_label}].");
    }

    public function destroy(EmployeeEvaluation $evaluation): RedirectResponse
    {
        $evaluation->delete();
        return redirect()->back()->with('success', 'تم حذف سجل التقييم بنجاح.');
    }
}

