<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class OncologyMedicineRep extends Model
{
    protected $fillable = ['name', 'phone', 'company', 'notes'];
}
