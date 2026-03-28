<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebRoomBooking extends Model
{
    use \App\Traits\UploadsImages;

    public function getPatientIdUrlAttribute(): ?string
    {
        return $this->getFileUrl('patient_id_path');
    }

    public function getCompanionIdUrlAttribute(): ?string
    {
        return $this->getFileUrl('companion_id_path');
    }

    public function getMedicalTransferUrlAttribute(): ?string
    {
        return $this->getFileUrl('medical_transfer_path');
    }

    public function getFollowupCardUrlAttribute(): ?string
    {
        return $this->getFileUrl('followup_card_path');
    }

    public function getMedicalReportUrlAttribute(): ?string
    {
        return $this->getFileUrl('medical_report_path');
    }

    protected $appends = [
        'image_url', 
        'patient_id_url', 
        'companion_id_url', 
        'medical_transfer_url', 
        'followup_card_url', 
        'medical_report_url'
    ];

    public function getImageColumn(): string
    {
        return 'patient_id_path';
    }

    public function getImageColumns(): array
    {
        return [
            'patient_id_path',
            'companion_id_path',
            'medical_transfer_path',
            'followup_card_path',
            'medical_report_path'
        ];
    }

    protected $fillable = [
        'name',
        'phone',
        'email',
        'guest_house_id',
        'room_type',
        'check_in',
        'check_out',
        'notes',
        'status',
        'national_id',
        'address',
        'companion_name',
        'companion_phone',
        'arrival_date',
        'expected_duration',
        'medical_center',
        'patient_id_path',
        'companion_id_path',
        'medical_transfer_path',
        'followup_card_path',
        'medical_report_path',
        'source'
    ];

    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function getExpectedDurationArabicAttribute()
    {
        $durations = [
            'less_than_week' => 'أقل من أسبوع',
            'one_week'      => 'أسبوع',
            'two_weeks'     => 'أسبوعين',
            'three_weeks'   => 'ثلاثة أسابيع',
            'month'         => 'شهر',
            'more_than_month' => 'أكثر من شهر',
        ];

        return $durations[$this->expected_duration] ?? $this->expected_duration;
    }
}
