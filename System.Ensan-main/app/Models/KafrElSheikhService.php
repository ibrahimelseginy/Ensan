<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KafrElSheikhService extends Model
{
    protected $fillable = ['name', 'service_type', 'phone', 'notes'];
}
