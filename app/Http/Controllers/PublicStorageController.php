<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

final class PublicStorageController extends Controller
{
    public function show(string $path)
    {
        abort_if(str_contains($path, '..') || str_contains($path, "\0") || str_contains($path, '\\'), 404);
        $disk = Storage::disk('public');
        $root = realpath($disk->path(''));
        $file = realpath($disk->path($path));
        abort_unless($root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file), 404);
        return response()->file($file, ['X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "sandbox"]);
    }
}
