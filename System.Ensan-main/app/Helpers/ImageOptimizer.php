<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Maximum width for images (maintains aspect ratio)
     */
    protected static int $maxWidth = 1920;

    /**
     * Maximum height for images (maintains aspect ratio)
     */
    protected static int $maxHeight = 1080;

    /**
     * WebP quality (0-100), 80 is a good balance of quality/size
     */
    protected static int $webpQuality = 80;

    /**
     * Process an uploaded image: resize, convert to WebP, and store.
     * Returns the stored path (relative to storage/app/public).
     *
     * @param UploadedFile $file The uploaded image file.
     * @param string $storagePath The directory within 'public' disk to store the image.
     * @param int|null $maxWidth Override default max width.
     * @param int|null $maxHeight Override default max height.
     * @param int|null $quality Override default WebP quality.
     * @return string|null The stored path, or null on failure.
     */
    public static function optimize(
        UploadedFile $file,
        string $storagePath,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $quality = null
        ): ?string
    {
        $maxWidth = $maxWidth ?? static::$maxWidth;
        $maxHeight = $maxHeight ?? static::$maxHeight;
        $quality = $quality ?? static::$webpQuality;
        $disk = 'public';

        // Increase memory for image processing
        @ini_set('memory_limit', '512M');

        // If GD is not available or file is not an image, store original
        if (!\extension_loaded('gd')) {
            \Illuminate\Support\Facades\Log::info('GD extension missing, storing original image.');
            return $file->store($storagePath, $disk);
        }

        try {
            $imageInfo = @\getimagesize($file->getPathname());
            if ($imageInfo === false) {
                return $file->store($storagePath, $disk);
            }

            $mimeType = $imageInfo['mime'];
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            $sourceImage = self::createImageFromFile($file->getPathname(), $mimeType);
            if ($sourceImage === null) {
                return $file->store($storagePath, $disk);
            }

            [$newWidth, $newHeight] = self::calculateDimensions(
                $originalWidth,
                $originalHeight,
                $maxWidth,
                $maxHeight
            );

            if ($newWidth !== $originalWidth || $newHeight !== $originalHeight) {
                $resizedImage = \imagecreatetruecolor($newWidth, $newHeight);
                \imagealphablending($resizedImage, false);
                \imagesavealpha($resizedImage, true);
                $transparent = \imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                \imagefill($resizedImage, 0, 0, $transparent);

                \imagecopyresampled(
                    $resizedImage, $sourceImage,
                    0, 0, 0, 0,
                    $newWidth, $newHeight,
                    $originalWidth, $originalHeight
                );
                \imagedestroy($sourceImage);
                $sourceImage = $resizedImage;
            }

            $filename = Str::uuid() . '.webp';
            $fullRelativePath = trim($storagePath, '/') . '/' . $filename;

            // Ensure directory exists using Storage facade
            if (!Storage::disk($disk)->exists($storagePath)) {
                Storage::disk($disk)->makeDirectory($storagePath);
            }

            $absolutePath = Storage::disk($disk)->path($fullRelativePath);

            // Convert to WebP and save
            $success = \imagewebp($sourceImage, $absolutePath, $quality);
            \imagedestroy($sourceImage);

            if ($success) {
                return $fullRelativePath;
            }

            return $file->store($storagePath, $disk);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('ImageOptimizer failed: ' . $e->getMessage());
            return $file->store($storagePath, $disk);
        }
    }

    /**
     * Create a GD image resource from a file path based on MIME type.
     */
    protected static function createImageFromFile(string $path, string $mimeType)
    {
        return match ($mimeType) {
                'image/jpeg', 'image/jpg' => \imagecreatefromjpeg($path),
                'image/png' => \imagecreatefrompng($path),
                'image/gif' => \imagecreatefromgif($path),
                'image/webp' => \imagecreatefromwebp($path),
                'image/bmp' => \imagecreatefrombmp($path),
                'image/avif' => \function_exists('imagecreatefromavif') ? \imagecreatefromavif($path) : null,
                default => null,
            };
    }

    /**
     * Calculate new dimensions while maintaining aspect ratio.
     *
     * @return array [newWidth, newHeight]
     */
    protected static function calculateDimensions(
        int $originalWidth,
        int $originalHeight,
        int $maxWidth,
        int $maxHeight
        ): array
    {
        // If image is within bounds, return original dimensions
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return [$originalWidth, $originalHeight];
        }

        // Calculate ratios
        $ratioWidth = $maxWidth / $originalWidth;
        $ratioHeight = $maxHeight / $originalHeight;
        $ratio = min($ratioWidth, $ratioHeight);

        $newWidth = (int)round($originalWidth * $ratio);
        $newHeight = (int)round($originalHeight * $ratio);

        return [$newWidth, $newHeight];
    }

    /**
     * Delete an old image from storage.
     */
    public static function delete(?string $path): void
    {
        if (!$path)
            return;

        // Clean path (remove accidental storage/ or /storage/ prefix)
        $path = preg_replace('/^(\/?storage\/)/', '', $path);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Optimize an existing image already in storage (batch processing).
     * Reads the file, converts to WebP, saves, and returns the new path.
     *
     * @param string $currentPath Current path in storage (relative to public disk).
     * @param string $storagePath Target directory for the optimized image.
     * @return string|null The new path, or null on failure (keeps original).
     */
    public static function optimizeExisting(string $currentPath, string $storagePath): ?string
    {
        // If GD is not available, skip optimization
        if (!\extension_loaded('gd')) {
            return null;
        }

        try {
            $absolutePath = storage_path('app/public/' . $currentPath);

            if (!file_exists($absolutePath)) {
                return null;
            }

            // Skip if already WebP
            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            if ($extension === 'webp') {
                return $currentPath; // Already optimized
            }

            $imageInfo = \getimagesize($absolutePath);
            if ($imageInfo === false) {
                return null;
            }

            $mimeType = $imageInfo['mime'];
            $sourceImage = self::createImageFromFile($absolutePath, $mimeType);
            if ($sourceImage === null) {
                return null;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            // Resize if too large
            [$newWidth, $newHeight] = self::calculateDimensions(
                $originalWidth,
                $originalHeight,
                static::$maxWidth,
                static::$maxHeight
            );

            if ($newWidth !== $originalWidth || $newHeight !== $originalHeight) {
                $resizedImage = \imagecreatetruecolor($newWidth, $newHeight);
                \imagealphablending($resizedImage, false);
                \imagesavealpha($resizedImage, true);
                $transparent = \imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                \imagefill($resizedImage, 0, 0, $transparent);

                \imagecopyresampled(
                    $resizedImage,
                    $sourceImage,
                    0, 0, 0, 0,
                    $newWidth, $newHeight,
                    $originalWidth, $originalHeight
                );

                \imagedestroy($sourceImage);
                $sourceImage = $resizedImage;
            }

            // Generate new WebP filename
            $filename = pathinfo($currentPath, PATHINFO_FILENAME) . '.webp';
            $newPath = $storagePath . '/' . $filename;

            $absoluteDir = storage_path('app/public/' . $storagePath);
            if (!is_dir($absoluteDir)) {
                mkdir($absoluteDir, 0755, true);
            }

            $newAbsolutePath = storage_path('app/public/' . $newPath);
            $success = \imagewebp($sourceImage, $newAbsolutePath, static::$webpQuality);
            \imagedestroy($sourceImage);

            if ($success) {
                // Delete original file (only if different path)
                if ($currentPath !== $newPath) {
                    Storage::disk('public')->delete($currentPath);
                }
                return $newPath;
            }

            return null;

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('ImageOptimizer::optimizeExisting failed: ' . $e->getMessage());
            return null;
        }
    }
}
