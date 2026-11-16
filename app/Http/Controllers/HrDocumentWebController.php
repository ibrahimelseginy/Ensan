<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HrDocument;
use App\Models\Project;
use App\Services\PrivateDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class HrDocumentWebController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $q        = trim((string) $request->input('q', ''));
        $projectId = $request->input('project_id');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $documents = HrDocument::with(['project', 'uploader'])
            ->when($category && array_key_exists($category, HrDocument::categoryLabels()), fn ($query) => $query->where('category', $category))
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->when($dateFrom, fn ($query) => $query->whereDate('report_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('report_date', '<=', $dateTo))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('original_name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $rawCounts = HrDocument::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categories = HrDocument::categoryMeta();
        $counts = [
            'all' => (int) $rawCounts->sum(),
        ];
        foreach (array_keys($categories) as $catKey) {
            $counts[$catKey] = (int) ($rawCounts[$catKey] ?? 0);
        }

        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('hr.documents.index', compact(
            'documents',
            'categories',
            'counts',
            'projects',
            'category',
            'q',
            'projectId',
            'dateFrom',
            'dateTo'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:sessions,events,evaluations,policies,other',
            'report_date' => 'nullable|date',
            'project_id'  => 'nullable|exists:projects,id',
            'description' => 'nullable|string|max:2000',
            'file'        => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip|max:51200',
        ], [
            'title.required' => 'يرجى إدخال عنوان التقرير أو الملف.',
            'file.required'  => 'يرجى اختيار الملف المراد رفعه.',
            'file.max'       => 'حجم الملف يجب ألا يتجاوز 50 ميجابايت.',
            'file.mimes'     => 'صيغة الملف غير مدعومة.',
        ]);

        $file = $request->file('file');
        $path = app(PrivateDocumentService::class)->store($file, 'hr_documents');

        HrDocument::create([
            'title'         => $data['title'],
            'category'      => $data['category'],
            'report_date'   => $data['report_date'] ?? now()->toDateString(),
            'project_id'    => $data['project_id'] ?? null,
            'description'   => $data['description'] ?? null,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'uploaded_by'   => auth()->id(),
        ]);

        return redirect()
            ->route('hr.documents.index', ['category' => $data['category']])
            ->with('success', 'تم رفع الملف / التقرير إلى ملفات HR بنجاح.');
    }

    public function update(Request $request, HrDocument $hrDocument): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:sessions,events,evaluations,policies,other',
            'report_date' => 'nullable|date',
            'project_id'  => 'nullable|exists:projects,id',
            'description' => 'nullable|string|max:2000',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip|max:51200',
        ]);

        $updateData = [
            'title'       => $data['title'],
            'category'    => $data['category'],
            'report_date' => $data['report_date'] ?? $hrDocument->report_date,
            'project_id'  => $data['project_id'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $this->deleteStoredFile($hrDocument->file_path);
            $file = $request->file('file');
            $updateData['file_path']     = app(PrivateDocumentService::class)->store($file, 'hr_documents');
            $updateData['original_name'] = $file->getClientOriginalName();
            $updateData['mime']          = $file->getMimeType();
            $updateData['file_size']     = $file->getSize();
        }

        $hrDocument->update($updateData);

        return back()->with('success', 'تم تحديث بيانات الملف بنجاح.');
    }

    public function show(Request $request, HrDocument $hrDocument): Response
    {
        $path = $hrDocument->file_path;
        $filename = $hrDocument->original_name ?: basename($path);
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        if (str_starts_with($path, 'private/') && Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path, $filename, [
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, $filename, [
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]);
        }

        $fullPublic = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($fullPublic)) {
            return response()->file($fullPublic, [
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]);
        }

        abort(404, 'الملف المطلوب غير موجود.');
    }

    public function destroy(HrDocument $hrDocument): RedirectResponse
    {
        $this->deleteStoredFile($hrDocument->file_path);
        $hrDocument->delete();

        return back()->with('success', 'تم حذف الملف بنجاح.');
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }
        if (str_starts_with($path, 'private/')) {
            Storage::disk('local')->delete($path);
        } elseif (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

