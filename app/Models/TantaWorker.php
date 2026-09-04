<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class TantaWorker extends Model
{
    protected $fillable = ['name', 'profession', 'phone', 'notes'];
}
