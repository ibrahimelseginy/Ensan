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
        return $disk->download($path, basename($path), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
