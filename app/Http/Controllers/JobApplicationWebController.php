<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * لوحة الموارد البشرية لمتابعة طلبات التوظيف الواردة من الموقع.
 */
final class JobApplicationWebController extends Controller
{
    public function index(Request $request): View
    {
        $status   = (string) $request->input('status', 'all');
        $position = (string) $request->input('position', '');
        $q        = trim((string) $request->input('q', ''));

        $applications = JobApplication::query()
            ->with('reviewer')
            ->when($status !== 'all' && $status !== '', fn ($query) => $query->where('status', $status))
            ->when($position !== '', fn ($query) => $query->where('position', $position))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('full_name', 'like', "%{$q}%")
                        ->orWhere('tracking_code', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('faculty', 'like', "%{$q}%")
                        ->orWhere('residence', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $counts = JobApplication::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total'     => (int) $counts->sum(),
            'new'       => (int) ($counts[JobApplication::STATUS_NEW] ?? 0),
            'reviewing' => (int) ($counts[JobApplication::STATUS_REVIEWING] ?? 0),
            'interview' => (int) ($counts[JobApplication::STATUS_INTERVIEW] ?? 0),
            'accepted'  => (int) ($counts[JobApplication::STATUS_ACCEPTED] ?? 0),
            'rejected'  => (int) ($counts[JobApplication::STATUS_REJECTED] ?? 0),
        ];

        $positions = JobApplication::query()
            ->select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        $statuses = JobApplication::STATUSES;

        return view('hr.job_applications.index', compact(
            'applications', 'stats', 'positions', 'statuses', 'status', 'position', 'q'
        ));
    }

    public function show(JobApplication $jobApplication): View
    {
        $jobApplication->load('reviewer');
        $statuses = JobApplication::STATUSES;

        return view('hr.job_applications.show', [
            'application' => $jobApplication,
            'statuses'    => $statuses,
        ]);
    }

    public function updateStatus(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        $data = $request->validate([
            'status'             => 'required|in:' . implode(',', array_keys(JobApplication::STATUSES)),
            'interview_date'     => 'nullable|required_if:status,interview|date',
            'interview_location' => 'nullable|string|max:191',
            'applicant_message'  => 'nullable|string|max:2000',
            'hr_notes'           => 'nullable|string|max:5000',
        ], [
            'interview_date.required_if' => 'يرجى تحديد موعد المقابلة.',
        ]);

        $jobApplication->update([
            'status'             => $data['status'],
            'interview_date'     => $data['status'] === JobApplication::STATUS_INTERVIEW ? ($data['interview_date'] ?? null) : $jobApplication->interview_date,
            'interview_location' => $data['interview_location'] ?? $jobApplication->interview_location,
            'applicant_message'  => $data['applicant_message'] ?? null,
            'hr_notes'           => $data['hr_notes'] ?? $jobApplication->hr_notes,
            'reviewed_by'        => auth()->id(),
            'reviewed_at'        => now(),
        ]);

        return back()->with('success', "تم تحديث حالة طلب {$jobApplication->full_name} ({$jobApplication->tracking_code}) إلى: {$jobApplication->status_label}");
    }

    public function downloadCv(JobApplication $jobApplication): StreamedResponse|RedirectResponse
    {
        $path = ltrim((string) $jobApplication->cv_path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        if ($path === '' || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'ملف السيرة الذاتية غير موجود على السيرفر.');
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $safeName = preg_replace('/[^\p{Arabic}\w\-]+/u', '_', $jobApplication->full_name) ?: 'cv';

        return Storage::disk('public')->download($path, "CV_{$jobApplication->tracking_code}_{$safeName}.{$ext}");
    }

    public function destroy(JobApplication $jobApplication): RedirectResponse
    {
        if ($jobApplication->cv_path && Storage::disk('public')->exists($jobApplication->cv_path)) {
            Storage::disk('public')->delete($jobApplication->cv_path);
        }
        $code = $jobApplication->tracking_code;
        $jobApplication->delete();

        return redirect()->route('hr.job-applications.index')->with('success', "تم حذف طلب التوظيف {$code}.");
    }
}

