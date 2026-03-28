<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadsImages;

final class MobileVolunteerRequest extends Model
{
    use UploadsImages;

    protected $fillable = [
        'name', 'phone', 'email', 'national_id', 'birth_date', 'gender', 'address', 'current_address',
        'education_level', 'faculty', 'university', 'current_job', 'previous_experience',
        'skills', 'goal', 'expectations', 'volunteer_hours', 'area_of_interest', 'message',
        'cv_path', 'id_card_path', 'status', 'admin_notes'
    ];

    public function getImageColumns(): array
    {
        return ['cv_path', 'id_card_path'];
    }

    public function cvExists(): bool
    {
        if (!$this->cv_path) return false;
        
        $cvPath = $this->cv_path;
        // Clean up path variations
        if (str_starts_with($cvPath, 'http')) {
            $parsed = parse_url($cvPath);
            $cvPath = ltrim(str_replace('/storage/', '', $parsed['path']), '/');
        } else {
            $cvPath = ltrim($cvPath, '/');
            if (str_starts_with($cvPath, 'storage/')) {
                $cvPath = substr($cvPath, 8);
            }
        }

        $pathsToTry = [
            \Illuminate\Support\Facades\Storage::disk('public')->path($cvPath),
            \Illuminate\Support\Facades\Storage::disk('local')->path($cvPath),
            storage_path("app/public/" . $cvPath),
            storage_path("app/private/" . $cvPath),
            public_path("storage/" . $cvPath)
        ];

        foreach ($pathsToTry as $path) {
            if (file_exists($path)) {
                return true;
            }
        }
        
        return false;
    }

    public function idCardExists(): bool
    {
        if (!$this->id_card_path) return false;
        return \Illuminate\Support\Facades\Storage::disk('public')->exists($this->id_card_path);
    }
    public function getCvUrlAttribute(): ?string
    {
        return $this->getFileUrl('cv_path');
    }

    public function getIdCardUrlAttribute(): ?string
    {
        return $this->getFileUrl('id_card_path');
    }

    protected $appends = ['cv_url', 'id_card_url'];
}
