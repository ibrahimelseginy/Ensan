<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * UploadsImages — Trait للموديلات التي تحتوي على حقول صور
 *
 * الاستخدام:
 *   use UploadsImages;
 *
 *   public function getImageColumn(): string  { return 'image_path'; }
 *   public function getImageColumns(): array  { return ['image_path', 'icon_path']; }
 *
 * يستخدم تلقائياً الـ disk المحدد في IMAGE_UPLOAD_DISK (public أو uploads).
 */
trait UploadsImages
{
    // -------------------------------------------------------------------------
    // Overridable in Model
    // -------------------------------------------------------------------------

    /** اسم عمود الصورة الرئيسي في قاعدة البيانات */
    public function getImageColumn(): string
    {
        return 'image_path';
    }

    /** جميع أعمدة الصور (مفيد عند الحذف التلقائي) */
    public function getImageColumns(): array
    {
        return [$this->getImageColumn()];
    }

    // -------------------------------------------------------------------------
    // uploadImage()
    // -------------------------------------------------------------------------

    /**
     * رفع صورة وحذف القديمة تلقائياً.
     *
     * @param  UploadedFile $file
     * @param  string       $directory  المجلد داخل الـ disk
     * @param  string|null  $column     اسم العمود (يستخدم getImageColumn() افتراضياً)
     */
    public function uploadImage(
        UploadedFile $file,
        string $directory = 'uploads',
        ?string $column = null,
    ): void {
        $column = $column ?? $this->getImageColumn();

        // حذف الصورة القديمة
        $this->deleteImage($column, false);

        // رفع وضغط وتحويل
        $path = app(\App\Services\ImageUploadService::class)->upload($file, $directory);

        if ($path) {
            $this->{$column} = $path;
            $this->save();
        }
    }

    // -------------------------------------------------------------------------
    // deleteImage()
    // -------------------------------------------------------------------------

    /**
     * حذف الصورة من التخزين.
     *
     * @param  string|null $column
     * @param  bool        $saveModel  حفظ الموديل بعد الحذف
     */
    public function deleteImage(?string $column = null, bool $saveModel = true): void
    {
        $column      = $column ?? $this->getImageColumn();
        $currentPath = $this->{$column};

        if ($currentPath) {
            app(\App\Services\ImageUploadService::class)->delete($currentPath);
        }

        $this->{$column} = null;

        if ($saveModel) {
            $this->save();
        }
    }

    // -------------------------------------------------------------------------
    // URL Accessors
    // -------------------------------------------------------------------------

    /**
     * رابط الصورة الرئيسية الكامل.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFileUrl($this->getImageColumn());
    }

    /**
     * رابط كامل لأي عمود صورة.
     */
    public function getFileUrl(string $column): ?string
    {
        $raw = $this->{$column};

        if (!$raw) {
            return null;
        }

        return app(\App\Services\ImageUploadService::class)->url($raw);
    }

    /**
     * تنظيف المسار وإرجاع المسار النسبي فقط.
     */
    public static function normalizeImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            $parsed = parse_url($path);
            $path   = $parsed['path'] ?? '';
        }

        $path = preg_replace('/^\/?storage\//', '', $path);
        $path = preg_replace('/^\/?uploads\//', '', $path);
        $path = ltrim($path, '/');

        return $path ?: null;
    }

    // -------------------------------------------------------------------------
    // Model Events — حذف الصور عند حذف السجل
    // -------------------------------------------------------------------------

    protected static function bootUploadsImages(): void
    {
        static::deleted(function ($model) {
            // لا تحذف الصور عند الحذف الناعم (soft delete) — فقط عند force delete
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }

            foreach ($model->getImageColumns() as $column) {
                $path = $model->{$column};
                if ($path) {
                    app(\App\Services\ImageUploadService::class)->delete($path);
                }
            }
        });
    }
}
