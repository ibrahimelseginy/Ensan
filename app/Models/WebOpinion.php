<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebOpinion extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'opinion', 'is_published'];
    
    protected $casts = [
        'is_published' => 'boolean',
    ];
}
