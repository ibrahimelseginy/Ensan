<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class RamadanIftar extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_name',
        'nickname',
        'national_id',
        'meals_count',
        'region',
        'guide_name',
        'guide_phone',
        'guide_phone_2',
        'delivery_method',
        'delivery_cost',
        'address',
        'notes',
        'project_id',
        'campaign_id',
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
