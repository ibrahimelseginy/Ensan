<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebVolunteerRequest extends Model
{
    use \App\Traits\UploadsImages;

    public function getImageColumn(): string
    {
        return 'cv_path';
    }

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

    protected $fillable = [
        'name',
        'phone',
        'email',
        'area_of_interest',
        'cv_path',
        'message',
        'status',
        'address',
        'current_address',
        'birth_date',
        'national_id',
        'id_card_path',
        'gender',
        'education_level',
        'faculty',
        'university',
        'current_job',
        'previous_experience',
        'skills',
        'goal',
        'expectations',
        'volunteer_hours'
    ];
}
