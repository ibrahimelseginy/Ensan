<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileInKindDonation extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = [
        'donor_name',
        'donor_phone',
        'item_name',
        'item_description',
        'quantity',
        'image_path',
        'pickup_address',
        'preferred_pickup_time',
        'status',
        'user_id'
    ];
}
