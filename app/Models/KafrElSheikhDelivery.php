<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class KafrElSheikhDelivery extends Model
{
    protected $fillable = ['name', 'phone', 'vehicle_type', 'notes'];
}
