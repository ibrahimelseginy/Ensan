<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageRequest;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * وحدة التحكم المخصصة لرفع الصور عبر الـ API (أو الـ Web).
 *
 * Endpoints:
 *  POST   /upload/image         → رفع صورة واحدة
 *  POST   /upload/images        → رفع مجموعة صور
 *  DELETE /upload/image         → حذف صورة
 */
final class ImageUploadController extends Controller
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
    ) {}

    // -------------------------------------------------------------------------
    // Upload Single Image
    // -------------------------------------------------------------------------

    /**
     * رفع صورة واحدة.
     *
     * Body (multipart/form-data):
     *   image      : الملف (مطلوب)
     *   directory  : مجلد التخزين (اختياري، افتراضي: 'uploads')
     *   old_path   : مسار الصورة القديمة لحذفها (اختياري)
     *
     * Response:
     *   { "path": "uploads/xxxx.webp", "url": "https://..." }
     */
    public function upload(UploadImageRequest $request): JsonResponse
    {
        $directory = $this->uploadDirectory($request);
        $oldPath   = $request->input('old_path');
        if ($oldPath) $this->authorizePath($request, $oldPath);

        $path = $this->imageUploadService->upload(
            file: $request->file('image'),
            directory: $directory,
            oldPath: $oldPath ?: null,
        );

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'فشل رفع الصورة. تحقق من صحة الملف وحاول مجدداً.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => $this->imageUploadService->url($path),
        ], 201);
    }

    // -------------------------------------------------------------------------
    // Upload Multiple Images
    // -------------------------------------------------------------------------

    /**
     * رفع مجموعة صور.
     *
     * Body (multipart/form-data):
     *   images[]    : الملفات (مطلوب)
     *   directory   : مجلد التخزين (اختياري، افتراضي: 'uploads')
     *
     * Response:
     *   { "paths": [...], "urls": [...] }
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'images'    => 'required|array|min:1|max:20',
            'images.*'  => 'required|file|image|max:10240|mimes:jpeg,jpg,png,gif,webp,bmp',
            'directory' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9_\/\-]+$/',
        ]);

        $directory = $this->uploadDirectory($request);

        $paths = $this->imageUploadService->uploadMultiple(
            files: $request->file('images'),
            directory: $directory,
        );

        if (empty($paths)) {
            return response()->json([
                'success' => false,
                'message' => 'فشل رفع جميع الصور.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'paths'   => $paths,
            'urls'    => $this->imageUploadService->urls($paths),
        ], 201);
    }

    // -------------------------------------------------------------------------
    // Delete Image
    // -------------------------------------------------------------------------

    /**
     * حذف صورة من التخزين.
     *
     * Body (JSON or form):
     *   path : المسار النسبي للصورة (مطلوب)
     *
     * Response:
     *   { "success": true }
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string', 'max:500', 'not_regex:/\.\./'],
        ]);

        $path = $request->input('path');
        $this->authorizePath($request, $path);

        if (!$this->imageUploadService->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة غير موجودة أو تم حذفها مسبقاً.',
            ], 404);
        }

        $this->imageUploadService->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح.',
        ]);
    }
    private function uploadDirectory(Request $request): string
    {
        $user = $request->user();
        abort_unless($user, 401);
        $directory = trim($request->input('directory', 'uploads'), '/');
        return ($user->hasRole('admin') || $user->hasRole('manager'))
            ? $directory : 'api-users/'.$user->id.'/'.$directory;
    }

    private function authorizePath(Request $request, string $path): void
    {
        $user = $request->user();
        abort_unless($user, 401);
        $cleaned = $this->imageUploadService->normalizePath($path);
        abort_unless($cleaned && !str_contains($path, '..') && (
            $user->hasRole('admin') || $user->hasRole('manager') ||
            str_starts_with($cleaned, 'api-users/'.$user->id.'/')
        ), 403);
    }
}
