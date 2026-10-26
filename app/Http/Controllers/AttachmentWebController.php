<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final class AttachmentWebController extends Controller
{
    public function show(Attachment $attachment): Response
    {
        $path = $attachment->path;

        // 1. Check local private disk
        if (str_starts_with($path, 'private/')) {
            if (Storage::disk('local')->exists($path)) {
                return Storage::disk('local')->response(
                    $path,
                    $attachment->original_name ?? basename($path),
                    ['Content-Disposition' => 'inline; filename="' . ($attachment->original_name ?? basename($path)) . '"']
                );
            }
        }

        // 2. Check public storage disk
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response(
                $path,
                $attachment->original_name ?? basename($path),
                ['Content-Disposition' => 'inline; filename="' . ($attachment->original_name ?? basename($path)) . '"']
            );
        }

        // 3. Check public_path directly
        $fullPublic = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($fullPublic)) {
            return response()->file($fullPublic, [
                'Content-Disposition' => 'inline; filename="' . ($attachment->original_name ?? basename($path)) . '"'
            ]);
        }

        abort(404, 'المستند أو الملف غير موجود.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv,zip|max:10240'
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType();

        $path = app(\App\Services\PrivateDocumentService::class)->store($file, 'attachments');

        Attachment::create([
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
            'path' => $path,
            'original_name' => $originalName,
            'mime' => $mime,
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح.');
    }

    public function destroy(Attachment $attachment)
    {
        if (str_starts_with($attachment->path, 'private/')) {
            Storage::disk('local')->delete($attachment->path);
        } else {
            app(\App\Services\ImageUploadService::class)->delete($attachment->path);
        }

        $attachment->delete();

        return back()->with('success', 'تم حذف الملف بنجاح.');
    }
}
