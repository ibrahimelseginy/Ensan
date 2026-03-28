<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileCaseApplication extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumn(): string
    {
        return 'id_image_path';
    }

    public function getImageColumns(): array
    {
        return ['id_image_path', 'medical_report_path'];
    }

    protected $fillable = [
        'applicant_name',
        'applicant_phone',
        'applicant_id_number',
        'case_type',
        'description',
        'governorate',
        'city',
        'address',
        'id_image_path',
        'medical_report_path',
        'status',
        'admin_notes',
        'user_id'
    ];

    public function getIdImageUrlAttribute(): ?string
    {
        return $this->getFileUrl('id_image_path');
    }

    public function getMedicalReportUrlAttribute(): ?string
    {
        return $this->getFileUrl('medical_report_path');
    }

    protected $appends = ['id_image_url', 'medical_report_url'];
}
