<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileRoomBooking extends Model
{
    use \App\Traits\UploadsImages;

    protected $fillable = [
        'name',
        'phone',
        'national_id',
        'arrival_date',
        'expected_duration',
        'medical_center',
        'notes',
        'patient_id_file',
        'status'
    ];

    public function getImageColumn(): string
    {
        return 'patient_id_file';
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
