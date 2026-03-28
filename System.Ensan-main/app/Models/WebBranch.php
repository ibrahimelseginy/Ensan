<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebBranch extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'working_hours', 'email', 'google_maps_url', 'is_main', 'status_text', 'description'];
}
