<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VolunteerEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class VolunteerEvaluationWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'                    => 'required|exists:users,id',
            'evaluation_period'          => 'nullable|string|max:50',
            'evaluation_date'            => 'required|date',
            'tasks_achievement'          => 'required|numeric|min:0.5|max:5',
            'target'                     => 'required|numeric|min:0.5|max:5',
            'punctuality'                => 'required|numeric|min:0.5|max:5',
            'attendance_discipline'      => 'required|numeric|min:0.5|max:5',
            'instructions_compliance'    => 'required|numeric|min:0.5|max:5',
            'initiative_volunteering'    => 'required|numeric|min:0.5|max:5',
            'teamwork'                   => 'required|numeric|min:0.5|max:5',
            'communication_interaction'  => 'required|numeric|min:0.5|max:5',
            'ideas'                      => 'required|numeric|min:0.5|max:5',
            'notes'                      => 'nullable|string|max:1000',
            'strengths'                  => 'nullable|string|max:1000',
            'improvements'               => 'nullable|string|max:1000',
        ], [
            '*.min' => 'الحد الأدنى لدرجة كل بند هو 0.5',
            '*.max' => 'الحد الأقصى لدرجة كل بند هو 5.0',
            '*.required' => 'جميع بنود تقييم المتطوع التسعة مطلوبة لحساب النتيجة بدقة.',
        ]);

        $evaluation = new VolunteerEvaluation($validated);
        $evaluation->evaluator_id = auth()->id();
        $evaluation->calculateResults();
        $evaluation->save();

        $volunteer = User::find($validated['user_id']);

        return redirect()->back()->with('success', "تم تسجيل تقييم المتطوع ({$volunteer?->name}) بنجاح بنتيجة [{$evaluation->total_score} من 100] بتقدير [{$evaluation->rating_label}].");
    }

    public function destroy(VolunteerEvaluation $evaluation): RedirectResponse
    {
        $evaluation->delete();
        return redirect()->back()->with('success', 'تم حذف سجل تقييم المتطوع بنجاح.');
    }
}

