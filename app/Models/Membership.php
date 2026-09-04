<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Membership extends Model
{
    protected $fillable = [
        'entity_name', 'entity_type', 'service_provided', 'discount_percentage',
        'discount_conditions', 'beneficiary_category', 'discount_activation_method',
        'working_hours', 'entity_address', 'entity_location', 'contact_number',
        'contact_person_number', 'email', 'entity_contact_name', 'entity_source_name',
        'cooperation_start_date', 'cooperation_end_date', 'cooperation_status',
        'priority_level', 'beneficiaries_count', 'entity_rating', 'notes'
    ];

    protected $casts = [
        'cooperation_start_date' => 'date',
        'cooperation_end_date' => 'date',
    ];
}
