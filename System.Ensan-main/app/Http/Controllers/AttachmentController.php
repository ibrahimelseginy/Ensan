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
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

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
        $attachment->delete();
        return response()->noContent();
    }
}
