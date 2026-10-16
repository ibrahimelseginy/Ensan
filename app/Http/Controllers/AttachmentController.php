<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

final class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv,zip', 'max:10240'], // Max 10MB
        ]);

        $ext = strtolower((string) $request->file('file')->getClientOriginalExtension());
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar', 'sh', 'exe', 'bat', 'cmd', 'js', 'py', 'pl', 'cgi', 'htaccess'];
        if (in_array($ext, $dangerousExtensions, true)) {
            return redirect()->back()->with('error', 'نوع الملف غير مسموح به لأسباب أمنية.');
        }

        try {
            // Store the file
            $path = $request->file('file')->store('attachments', 'public');
            
            // Get file info
            $originalName = $request->file('file')->getClientOriginalName();
            $mimeType = $request->file('file')->getMimeType();

            // Create attachment record
            $attachmentData = [
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'path' => $path,
                'mime' => $mimeType,
            ];
            
            // Add original_name only if column exists (to avoid SQL errors)
            if (\Schema::hasColumn('attachments', 'original_name')) {
                $attachmentData['original_name'] = $originalName;
            }
            
            Attachment::create($attachmentData);

            return redirect()->back()->with('success', 'تم رفع المرفق بنجاح');
        } catch (\Exception $e) {
            \Log::error('Attachment upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'فشل رفع المرفق: ' . $e->getMessage());
        }
    }
    public function destroy(Attachment $attachment)
    {
        if (!empty($attachment->path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachment->path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->path);
        }
        $attachment->delete();
        return response()->noContent();
    }
}
