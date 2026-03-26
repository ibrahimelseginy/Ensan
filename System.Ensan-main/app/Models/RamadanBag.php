<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RamadanBag extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_name',
        'national_id',
        'phone',
        'address',
        'bag_contents',
        'status',
        'project_id',
        'campaign_id',
        'marital_status',
        'spouse_name',
        'family_members',
        'case_conditions',
        'region',
        'bags_count',
        'phone_2',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
