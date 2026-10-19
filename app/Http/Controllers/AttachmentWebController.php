<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AttachmentWebController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv,zip|max:10240'
        ]);
        $path = app(\App\Services\PrivateDocumentService::class)->store($request->file('file'), 'attachments');
        $att = Attachment::create([
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
            'path' => $path,
            'mime' => $request->file('file')->getMimeType(),
        ]);
        return back();
    }
    public function destroy(Attachment $attachment)
    {
        app(\App\Services\ImageUploadService::class)->delete($attachment->path);
        $attachment->delete();
        return back();
    }
}
