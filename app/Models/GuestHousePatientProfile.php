<?php

namespace App\Models;

use App\Traits\UploadsImages;
use Illuminate\Database\Eloquent\Model;

final class GuestHousePatientProfile extends Model
{
    use UploadsImages;

    protected $fillable = [
        'beneficiary_id', 'guest_house_id', 'treatment_type', 'medical_center',
        'sessions_count', 'gender', 'patient_id_front_path', 'patient_id_back_path',
        'followup_card_path', 'referral_letter_path', 'medical_notes',
        'companion_name', 'companion_relation', 'companion_national_id',
        'companion_phone', 'companion_address', 'companion_id_front_path',
        'companion_id_back_path', 'manager_notes',
    ];

    public function getImageColumns(): array
    {
        return [
            'patient_id_front_path', 'patient_id_back_path',
            'followup_card_path', 'referral_letter_path',
            'companion_id_front_path', 'companion_id_back_path'
        ];
    }

    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
}
