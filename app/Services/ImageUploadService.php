<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * خدمة رفع الصور المركزية للنظام
 *
 * تُستخدم هذه الخدمة في أي مكان يحتاج إلى:
 *  - رفع صورة واحدة (صورة المستخدم، شعار الشريك، صورة المشروع... إلخ)
 *  - رفع مجموعة صور (معرض الأخبار، سلايدر الحملات... إلخ)
 *  - حذف صورة قديمة بأمان
 *  - استرجاع الرابط الكامل لصورة مخزّنة
 *
 * جميع الصور تُخزَّن في disk (public) وتُحوَّل تلقائياً إلى WebP مضغوطة.
 */
final class ImageUploadService
{
    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    /** الحجم الأقصى المسموح به للصورة (بالكيلوبايت) */
    public const MAX_SIZE_KB = 10240; // 10 MB

    /** أنواع الصور المسموح بها */
    public const ALLOWED_MIMES = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
        'image/webp', 'image/bmp',
        'image/heic', 'image/heif', 'image/avif',
    ];

    // -------------------------------------------------------------------------
    // Single-image Upload
    // -------------------------------------------------------------------------

    /**
     * رفع صورة واحدة، ضغطها، وتحويلها إلى WebP.
     *
     * @param  UploadedFile $file       الملف المرفوع
     * @param  string       $directory  المجلد داخل (public disk)، مثل: 'website/board'
     * @param  string|null  $oldPath    مسار الصورة القديمة لحذفها (اختياري)
     * @param  int|null     $maxWidth   أقصى عرض مخصص (اختياري)
     * @param  int|null     $maxHeight  أقصى ارتفاع مخصص (اختياري)
     * @param  int|null     $quality    جودة WebP 0-100 (اختياري)
     * @return string|null  المسار النسبي المحفوظ، أو null عند الفشل
     */
    public function upload(
        UploadedFile $file,
        string $directory,
        ?string $oldPath = null,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $quality = null,
    ): ?string {
        try {
            $mime = $file->getMimeType();
            $maxBytes = self::MAX_SIZE_KB * 1024;

            // Handle PDF and document uploads: store directly without image conversion
            if ($mime === 'application/pdf') {
                if ($file->getSize() > $maxBytes) return null;
                if (!preg_match('~^[a-zA-Z0-9_/-]+$~', $directory)) return null;
                $disk = config('filesystems.image_upload_disk', 'uploads');
                $savedPath = trim($directory, '/').'/'.Str::uuid().'.pdf';
                if (!Storage::disk($disk)->put($savedPath, file_get_contents($file->getRealPath()))) return null;
                if ($oldPath) $this->delete($oldPath);
                return $savedPath;
            }

            if (!in_array($mime, self::ALLOWED_MIMES, true) || $file->getSize() > $maxBytes) {
                return null;
            }
            if (!preg_match('~^[a-zA-Z0-9_/-]+$~', $directory)) return null;
            if (app(PrivateDocumentService::class)->isDocumentDirectory($directory)) {
                $path = app(PrivateDocumentService::class)->store($file, $directory);
                if ($oldPath) $this->delete($oldPath);
                return $path;
            }
            $dimensions = @getimagesize($file->getRealPath());
            if (!$dimensions || $dimensions[0] * $dimensions[1] > 25000000) return null;
            $manager = new \Intervention\Image\ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
            $encoded = $manager->decodePath($file->getRealPath())
                ->scaleDown(width: $maxWidth ?? 2048, height: $maxHeight ?? 2048)
                ->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: $quality ?? 82, strip: true));
            $disk = config('filesystems.image_upload_disk', 'uploads');
            $savedPath = trim($directory, '/').'/'.Str::uuid().'.webp';
            if (!Storage::disk($disk)->put($savedPath, (string) $encoded)) return null;
            if ($oldPath) $this->delete($oldPath);
            return $savedPath;
        } catch (\Throwable $e) {
            Log::error('[ImageUploadService] Upload failed.', ['directory' => $directory, 'exception' => get_class($e)]);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Multiple-images Upload
    // -------------------------------------------------------------------------

    /**
     * رفع مجموعة صور (array) ودمجها مع مجموعة موجودة مسبقاً.
     *
     * @param  UploadedFile[] $files        مصفوفة الملفات المرفوعة
     * @param  string         $directory    مجلد التخزين
     * @param  string[]       $existingPaths المسارات الموجودة حالياً (لإضافة الجديدة إليها)
     * @return string[]       المسارات بعد الإضافة
     */
    public function uploadMultiple(
        array $files,
        string $directory,
        array $existingPaths = [],
    ): array {
        foreach ($files as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $path = $this->upload($file, $directory);

            if ($path) {
                $existingPaths[] = $path;
            }
        }

        return $existingPaths;
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    /**
     * حذف صورة من التخزين بأمان.
     * يدعم: المسار النسبي، المسار الذي يبدأ بـ /storage/، والـ URL الكامل.
     *
     * @param  string|null $path
     * @return void
     */
    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (str_starts_with($path, 'private/')) {
            Storage::disk('local')->delete($path);
            return;
        }
        $cleaned = $this->normalizePath($path);
        if (!$cleaned) {
            return;
        }

        $disk = config('filesystems.image_upload_disk', 'uploads');
        if (Storage::disk($disk)->exists($cleaned)) {
            Storage::disk($disk)->delete($cleaned);
            return;
        }

        $fallback = ($disk === 'uploads') ? 'public' : 'uploads';
        if (Storage::disk($fallback)->exists($cleaned)) {
            Storage::disk($fallback)->delete($cleaned);
        }
    }

    /**
     * حذف مجموعة صور دفعةً واحدة.
     *
     * @param  string[] $paths
     * @return void
     */
    public function deleteMultiple(array $paths): void
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }
    }

    // -------------------------------------------------------------------------
    // URL Resolution
    // -------------------------------------------------------------------------

    /**
     * استرجاع الرابط الكامل لصورة مخزّنة.
     * يعمل في البيئتين (local و production) بدون تعديل.
     *
     * @param  string|null $path   المسار النسبي المخزَّن في قاعدة البيانات
     * @return string|null
     */
    public function url(?string $path, bool $absolute = false): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'private/')) return app(PrivateDocumentService::class)->url($path);

        // Return full external URLs as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleaned = $this->normalizePath($path);
        if (!$cleaned) {
            return null;
        }

        // Helper to format final URL (relative for web pages, absolute for API)
        $format = function (string $relative) use ($absolute): string {
            $isApi = function_exists('request') && request()?->is('api/*');
            if ($absolute || $isApi) {
                return url($relative);
            }
            return $relative;
        };

        // 1. Direct check in public/uploads/
        if (file_exists(public_path('uploads/' . $cleaned))) {
            return $format('/uploads/' . $cleaned);
        }

        // 2. Direct check in public/storage/
        if (file_exists(public_path('storage/' . $cleaned))) {
            return $format('/storage/' . $cleaned);
        }

        // 3. Direct check in storage/app/public/
        if (file_exists(storage_path('app/public/' . $cleaned))) {
            return $format('/storage/' . $cleaned);
        }

        // 4. Check via Storage disks
        $disk     = config('filesystems.image_upload_disk', 'uploads');
        $fallback = ($disk === 'uploads') ? 'public' : 'uploads';

        if (Storage::disk($disk)->exists($cleaned)) {
            $storageUrl = Storage::disk($disk)->url($cleaned);
            return $format($storageUrl);
        }

        if (Storage::disk($fallback)->exists($cleaned)) {
            $storageUrl = Storage::disk($fallback)->url($cleaned);
            return $format($storageUrl);
        }

        return null;
    }

    /**
     * استرجاع روابط كاملة لمصفوفة مسارات.
     *
     * @param  string[] $paths
     * @return string[]
     */
    public function urls(array $paths): array
    {
        return array_values(
            array_filter(
                array_map(fn (string $p) => $this->url($p), $paths)
            )
        );
    }

    // -------------------------------------------------------------------------
    // Path Normalization
    // -------------------------------------------------------------------------

    /**
     * تنظيف المسار المحفوظ في قاعدة البيانات وإرجاع المسار النسبي فقط.
     */
    public function normalizePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path);
            $path   = $parsed['path'] ?? '';
        }

        // Prevent path traversal and null-byte injection
        $path = str_replace(["\0", '..'], '', $path);
        $path = str_replace('\\', '/', $path);
        while (str_contains($path, '//')) {
            $path = str_replace('//', '/', $path);
        }

        $path = preg_replace('/^\/?storage\//', '', $path);
        $path = preg_replace('/^\/?uploads\//', '', $path);
        $path = ltrim($path, '/');

        return $path ?: null;
    }

    // -------------------------------------------------------------------------
    // Existence Check
    // -------------------------------------------------------------------------

    /**
     * التحقق من وجود صورة فعلياً في التخزين.
     *
     * @param  string|null $path
     * @return bool
     */
    public function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (str_starts_with($path, 'private/')) return Storage::disk('local')->exists($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return true;
        }

        $cleaned = $this->normalizePath($path);
        if (!$cleaned) {
            return false;
        }

        if (file_exists(public_path('uploads/' . $cleaned)) ||
            file_exists(public_path('storage/' . $cleaned)) ||
            file_exists(storage_path('app/public/' . $cleaned))) {
            return true;
        }

        $disk     = config('filesystems.image_upload_disk', 'uploads');
        $fallback = ($disk === 'uploads') ? 'public' : 'uploads';

        return Storage::disk($disk)->exists($cleaned)
            || Storage::disk($fallback)->exists($cleaned);
    }
}
