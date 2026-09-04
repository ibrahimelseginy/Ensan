<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class KafrElSheikhBroker extends Model
{
    protected $fillable = ['name', 'phone', 'area', 'notes'];
}
