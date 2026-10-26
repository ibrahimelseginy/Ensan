<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

final class PrivateDocumentController extends Controller
{
    public function download(string $path)
    {
        abort_unless(str_starts_with($path, 'private/') && !str_contains($path, '..') && !str_contains($path, '\\') && !str_contains($path, "\0"), 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);

        $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        $headers = [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Type' => $mime,
        ];

        if (request()->has('download')) {
            return $disk->download($path, basename($path), $headers);
        }

        return $disk->response($path, basename($path), $headers);
    }
}
