<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class KafrElSheikhService extends Model
{
    protected $fillable = ['name', 'service_type', 'phone', 'notes'];
}
