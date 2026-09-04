<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request لرفع صورة واحدة.
 *
 * الحقول المقبولة:
 *   image     : الصورة (مطلوب) - حجم أقصى 10 MB
 *   directory : مجلد التخزين (اختياري) - يسمح فقط بالأحرف والأرقام والشرطات والمائلة
 *   old_path  : مسار الصورة القديمة لحذفها (اختياري)
 */
final class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكنك ربطها بصلاحيات محددة هنا
    }

    public function rules(): array
    {
        return [
            'image'     => [
                'required',
                'file',
                'image',
                'max:10240',
                'mimes:jpeg,jpg,png,gif,webp,bmp,svg,heic,heif,avif',
            ],
            'directory' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\/\-]+$/',
            ],
            'old_path'  => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'يجب اختيار صورة.',
            'image.file'     => 'يجب أن يكون الملف المرفوع ملف صالح.',
            'image.image'    => 'يجب أن يكون الملف المرفوع صورة.',
            'image.max'      => 'حجم الصورة يجب ألا يتجاوز 10 ميجابايت.',
            'image.mimes'    => 'صيغة الصورة غير مدعومة. الصيغ المسموحة: jpeg, jpg, png, gif, webp, bmp, svg, heic, heif, avif.',
            'directory.regex'=> 'مسار المجلد غير صالح. يُسمح فقط بالأحرف والأرقام والشرطات والمائلة.',
        ];
    }

    /**
     * إضافة directory افتراضي إن لم يُرسَل.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('directory') || !$this->input('directory')) {
            $this->merge(['directory' => 'uploads']);
        }
    }
}
