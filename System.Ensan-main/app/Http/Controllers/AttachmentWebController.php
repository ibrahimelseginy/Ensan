<?php
namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttachmentWebController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'file' => 'required|file'
        ]);
        $path = $request->file('file')->store('attachments','public');
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
        $attachment->delete();
        return back();
    }
}
