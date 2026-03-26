<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadsImages
{
    /**
     * اسم الحقل في قاعدة البيانات الذي يحفظ مسار الصورة الأساسية.
     */
    public function getImageColumn(): string
    {
        return 'image_path';
    }

    /**
     * تحديد أسماء الحقول التي تحتوي على صور/ملفات 
     * (مفيد إذا كان الموديل يحتوي أكثر من صورة، يتم تجاوزه في الموديل).
     */
    public function getImageColumns(): array
    {
        return [$this->getImageColumn()];
    }

    /**
     * تحديد الـ Disk المستخدم.
     */
    public function getDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * دالة لرفع الصورة وحذف القديمة في حال التحديث.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'uploads', ?string $column = null): void
    {
        $column = $column ?? $this->getImageColumn();

        // 1. حذف الصورة القديمة إن وجدت
        $this->deleteImage($column, false);

        // 2. رفع الصورة الجديدة وحفظ المسار النسبي
        $path = $file->store($directory, $this->getDisk());
        
        // 3. التحديث في الموديل
        $this->{$column} = $path;
        $this->save();
    }

    /**
     * دالة لحذف الصورة من السيرفر.
     */
    public function deleteImage(?string $column = null, bool $saveModel = true): void
    {
        $column = $column ?? $this->getImageColumn();
        $currentPath = $this->{$column};

        if ($currentPath && Storage::disk($this->getDisk())->exists($currentPath)) {
            Storage::disk($this->getDisk())->delete($currentPath);
        }

        $this->{$column} = null;
        if ($saveModel) {
            $this->save();
        }
    }

    /**
     * دالة للحصول على الرابط الكامل للصورة الأساسية (Full URL).
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFileUrl($this->getImageColumn());
    }

    /**
     * دالة عامة للحصول على الرابط الكامل لأي حقل صورة آخر.
     */
    public function getFileUrl(string $column): ?string
    {
        $path = $this->{$column};

        if (!$path) {
            return null;
        }

        // استخدام asset لضمان إنتاج الرابط بشكل ديناميكي بناءً على الـ Host الذي دخل منه الطلب (مفيد جداً للموبايل)
        return asset('storage/' . $path);
    }
    
    /**
     * أحداث الموديل (Model Events)
     * لحذف الصور تلقائياً عندما يتم حذف السجل نفسه.
     */
    protected static function bootUploadsImages()
    {
        static::deleted(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }
            
            foreach ($model->getImageColumns() as $column) {
                $currentPath = $model->{$column};
                if ($currentPath && Storage::disk($model->getDisk())->exists($currentPath)) {
                    Storage::disk($model->getDisk())->delete($currentPath);
                }
            }
        });
    }
}
